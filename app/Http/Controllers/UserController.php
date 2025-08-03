<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Supervisor bisa lihat semua user
        // Leader bisa lihat user dengan role yang levelnya di bawahnya
        $user = auth()->user();

        if ($user->hasRole('Supervisor')) {
            $users = User::with('roles')->get();
        } elseif ($user->hasRole('Leader')) {
            // Ambil role yang lebih rendah dari Leader (Admin Tracking, Admin Officer, Customer Service)
            $rolesBelowLeader = ['Admin Tracking', 'Admin Officer', 'Customer Service'];
            $users = User::role($rolesBelowLeader)->get();
        } else {
            // Admin & CS hanya bisa lihat user yang mereka punya akses untuk reset password (mungkin usernya sendiri saja)
            $users = User::where('id', $user->id)->get();
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Supervisor bisa buat semua role
        if ($user->hasRole('Supervisor')) {
            $roles = Role::all();
        }
        // Leader hanya bisa buat role di bawahnya (Admin Tracking, Admin Officer, Customer Service)
        elseif ($user->hasRole('Leader')) {
            $roles = Role::whereIn('name', ['Admin Tracking', 'Admin Officer', 'Customer Service'])->get();
        } else {
            abort(403, 'Unauthorized to create users');
        }

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek permission buat create
        if (!($user->hasRole('Supervisor') || $user->hasRole('Leader'))) {
            abort(403, 'Unauthorized to create users');
        }

        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
            'status' => 'required|in:active,pending,suspended,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|min:10|max:15',
        ]);

        // Validasi role sesuai role creator
        if ($user->hasRole('Leader')) {
            $allowedRoles = ['Admin Tracking', 'Admin Officer', 'Customer Service'];
            if (!in_array($request->role, $allowedRoles)) {
                return back()->withErrors(['role' => 'You cannot assign this role.']);
            }
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $newUser = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'avatar' => $avatarPath,
            'phone' => $request->phone,
        ]);

        $newUser->assignRole($request->role);

        session()->flash('success', 'User created successfully.');

        return redirect()->to(URL::signedRoute('users.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $viewUser = User::with('roles')->findOrFail($id);

        // Supervisor bisa lihat semua user
        if ($user->hasRole('Supervisor')) {
            return view('users.show', compact('viewUser'));
        }

        // Leader hanya bisa lihat user dengan role di bawahnya
        if ($user->hasRole('Leader')) {
            $allowedRoles = ['Admin Tracking', 'Admin Officer', 'Customer Service'];
            if (!$viewUser->hasAnyRole($allowedRoles)) {
                abort(403, 'Unauthorized to view this user');
            }
            return view('users.show', compact('viewUser'));
        }

        // Admin & CS hanya bisa lihat profile sendiri
        if ($user->id === $viewUser->id) {
            return view('users.show', compact('viewUser'));
        }

        abort(403, 'Unauthorized to view this user');
    }

    /**
     * Show the form for editing the specified resource.
     * Edit hanya untuk reset password dan update profile
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $editUser = User::findOrFail($id);

        // Admin & CS hanya bisa reset password (update password)
        if ($user->hasAnyRole(['Admin Tracking', 'Customer Service'])) {
            if ($user->id !== $editUser->id) {
                abort(403, 'Unauthorized to edit this user');
            }
            // Jadi dia cuma bisa edit password sendiri
        }

        if ($user->hasRole('Supervisor')) {
            $roles = Role::all();
        }
        // Leader hanya bisa buat role di bawahnya (Admin Tracking, Admin Officer, Customer Service)
        elseif ($user->hasRole('Leader')) {
            $roles = Role::whereIn('name', ['Admin Tracking', 'Admin Officer', 'Customer Service'])->get();
        } else {
            abort(403, 'Unauthorized to create users');
        }

        // Leader bisa reset password & update user yang role di bawahnya
        if ($user->hasRole('Leader')) {
            $allowedRoles = ['Admin Tracking', 'Admin Officer', 'Customer Service'];
            if (!$editUser->hasAnyRole($allowedRoles)) {
                abort(403, 'Unauthorized to edit this user');
            }
        }

        // Supervisor bebas edit semua user

        return view('users.edit', compact('editUser', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $editUser = User::findOrFail($id);

        // Validasi input untuk update password dan profile
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'sometimes|required|in:active,pending,suspended,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|min:10|max:15',
        ]);

        // Check permissions untuk update
        if ($user->hasAnyRole(['Admin Tracking', 'Customer Service'])) {
            // Admin & CS hanya bisa update password sendiri
            if ($user->id !== $editUser->id) {
                abort(403, 'Unauthorized');
            }
            // Mereka cuma boleh update password sendiri
            $editUser->password = $request->password ? Hash::make($request->password) : $editUser->password;
            $editUser->save();

            session()->flash('success', 'User updated successfully.');

            return redirect()->to(URL::signedRoute('users.index'));
        }

        if ($user->hasRole('Leader')) {
            $allowedRoles = ['Admin Tracking', 'Admin Officer', 'Customer Service'];
            if (!$editUser->hasAnyRole($allowedRoles)) {
                abort(403, 'Unauthorized');
            }
            // Leader bisa update profile dan reset password user yang role di bawahnya
        }

        // Supervisor bebas update

        if ($request->hasFile('avatar')) {
            if ($editUser->avatar && Storage::disk('public')->exists($editUser->avatar)) {
                Storage::disk('public')->delete($editUser->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $editUser->avatar = $avatarPath;
        }

        $editUser->name = $request->name ?? $editUser->name;
        $editUser->username = $request->username ?? $editUser->username;
        $editUser->email = $request->email ?? $editUser->email;
        $editUser->status = $request->status ?? $editUser->status;
        $editUser->phone = $request->phone ?? $editUser->phone;

        if ($request->password) {
            $editUser->password = Hash::make($request->password);
        }

        $editUser->save();

        session()->flash('success', 'User updated successfully.');

        return redirect()->to(URL::signedRoute('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     * Hanya Supervisor yang bisa delete user
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        if (!$user->hasRole('Supervisor')) {
            abort(403, 'Unauthorized to delete users');
        }

        $deleteUser = User::findOrFail($id);

        if ($deleteUser->avatar && Storage::disk('public')->exists($deleteUser->avatar)) {
            Storage::disk('public')->delete($deleteUser->avatar);
        }

        $deleteUser->delete();

        session()->flash('success', 'User deleted successfully.');

        return redirect()->to(URL::signedRoute('users.index'));
    }

    public function resetAccount(User $user)
    {
        // Generate password sementara random
        $tempPassword = Str::random(10);

        // Update password user dengan hashed temp password
        $user->password = Hash::make($tempPassword);
        $user->must_reset_password = true;
        $user->save();

        // Return password sementara agar bisa disampaikan ke user bawahan
        session()->flash('tempPassword', $tempPassword);

        return redirect()->to(URL::signedRoute('users.index'));
    }
}

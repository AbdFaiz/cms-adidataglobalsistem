<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    // List all roles
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    // Show form to create a new role
    public function create()
    {
        $permissions = Permission::all(); // List semua permission yang ada
        return view('roles.create', compact('permissions'));
    }

    // Store a new role with selected permissions
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        session()->flash('success', 'Role created successfully.');

        return redirect()->to(URL::signedRoute('roles.index'));
    }

    // Show specific role with permissions
    public function show($id)
    {
        // $role = Role::with('permissions')->findOrFail($id);
        // return view('roles.show', compact('role'));
    }

    // Show edit form with current permissions selected
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    // Update role name and permissions
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->name = $request->name;
        $role->guard_name = $request->guard_name;
        $role->save();

        $role->syncPermissions($request->permissions ?? []);

        session()->flash('success', 'Role updated successfully.');

        return redirect()->to(URL::signedRoute('roles.index'));
    }

    // Delete a role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Optional: Cek apakah role sedang dipakai user sebelum delete

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');

        return redirect()->to(URL::signedRoute('roles.index'));
    }
}
   
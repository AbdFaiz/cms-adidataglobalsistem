<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class PasswordController extends Controller
{
    public function showResetForm()
    {
        $user = Auth::user();
        if($user->must_reset_password == false) {
            session()->flash('error', 'Anda tidak perlu mengganti password saat ini.');

            return redirect()->to(URL::signedRoute('dashboard'));
        }
        return view('auth.passwords.change', compact('user'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->must_reset_password = false;
        $user->save();

        return redirect()->to(URL::signedRoute('dashboard'))->with('success', 'Password berhasil diganti');
    }
}

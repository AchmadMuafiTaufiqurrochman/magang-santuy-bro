<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Default role = customer (bisa disesuaikan)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // kamu bisa ubah sesuai kebutuhan
        ]);

        // Login langsung setelah daftar
        Auth::login($user);

        // Redirect sesuai role (sama seperti login controller)
        switch ($user->role) {
            case 'admin':
                return redirect()->intended('/admin');
            case 'customer':
                return redirect()->intended('/customer');
            case 'technician':
                return redirect()->intended('/technician');
            default:
                Auth::logout();
                return redirect('/app/login')->withErrors([
                    'role' => 'Role tidak dikenali.',
                ]);
        }
    }
}

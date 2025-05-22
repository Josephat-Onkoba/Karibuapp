<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Get authenticated user
            $user = Auth::user();
            
            // Check if it's first login
            if ($user->first_login) {
                // Create password change route based on user role
                $passwordChangeRoute = $user->role === 'admin' 
                    ? 'admin.password.change' 
                    : 'usher.password.change';
                
                return redirect()->route($passwordChangeRoute);
            }
            
            // Redirect based on role for normal login
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else if ($user->role === 'usher') {
                return redirect()->route('usher.dashboard');
            }
            
            // Fallback
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
} 
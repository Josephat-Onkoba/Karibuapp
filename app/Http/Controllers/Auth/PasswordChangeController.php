<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class PasswordChangeController extends Controller
{
    /**
     * Show the form for changing password on first login.
     */
    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * Process the password change.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        
        // Update the user
        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'first_login' => false
        ]);

        // Redirect based on user role
        $redirectRoute = $user->role === 'admin' ? 'admin.dashboard' : 'usher.dashboard';
        
        return redirect()->route($redirectRoute)
            ->with('success', 'Password has been changed successfully.');
    }
}

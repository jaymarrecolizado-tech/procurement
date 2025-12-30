<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Log activity
            activity_log('LOGIN', 'USER', Auth::id(), [
                'email' => $request->email,
                'role' => Auth::user()->role
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:END_USER,PROCUREMENT_OFFICER,BAC_SECRETARIAT,BAC_CHAIR,BAC_MEMBER,CANVASSER,SUPPLIER,ADMIN',
            'department' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ]);

        Auth::login($user);

        // Log activity
        activity_log('CREATED', 'USER', $user->id, [
            'email' => $user->email,
            'role' => $user->role
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Log activity before logout
        if (Auth::check()) {
            activity_log('LOGOUT', 'USER', Auth::id(), [
                'email' => Auth::user()->email,
                'role' => Auth::user()->role
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'department' => 'nullable|string|max:255',
        ]);

        $oldData = $user->toArray();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
        ]);

        // Log activity
        activity_log('UPDATED', 'USER', $user->id, [
            'old_data' => $oldData,
            'new_data' => $user->toArray()
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $oldPassword = $user->password;
        $user->update(['password' => Hash::make($request->password)]);

        // Log activity
        activity_log('UPDATED', 'USER', $user->id, [
            'action' => 'Password changed',
            'old_password_hash' => $oldPassword
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
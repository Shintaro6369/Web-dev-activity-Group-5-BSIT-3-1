<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show Register Form
    public function showRegister() {
        return view('auth.register');
    }

    // Handle Register (Create)
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    // Show Login Form
    public function showLogin() {
        return view('auth.login');
    }

    // Handle Login (Safe from SQL Injection via Laravel Eloquent)
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }

    // Read & Dashboard (CRUD - Read)
    public function dashboard() {
        $users = User::all();
        return view('auth.dashboard', compact('users'));
    }

    // Update User (CRUD - Update)
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));
        return redirect()->back()->with('success', 'User updated successfully!');
    }

    // Delete User (CRUD - Delete)
    public function destroy($id) {
        User::destroy($id);
        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    // Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
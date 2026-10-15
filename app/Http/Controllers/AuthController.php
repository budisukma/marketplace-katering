<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister() { return view('auth.register'); }
    public function showLogin() { return view('auth.login'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users,email',
            'password' => ['required','confirmed', Password::min(8)],
            'role' => 'required|in:merchant,customer',
            'company_name' => 'required_if:role,merchant|nullable|string|max:160',
        ]);
        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'],
            'password' => Hash::make($data['password']), 'role' => $data['role'],
        ]);
        if ($user->role === 'merchant') {
            Merchant::create(['user_id' => $user->id, 'company_name' => $data['company_name']]);
        }
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route($user->role === 'merchant' ? 'merchant.dashboard' : 'customer.dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required|string']);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $user = $request->user();
        return redirect()->route($user->role === 'merchant' ? 'merchant.dashboard' : 'customer.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}

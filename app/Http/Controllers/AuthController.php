<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Redirigir a admin si es administrador
            if ($user->is_admin) {
                return redirect('/admin/dashboard');
            }
            
            return redirect()->intended(route('home'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ]);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'unique:users,name'],
            'email' => ['required', 'email', 'unique:users'],
            'dni' => ['required', 'string', 'unique:users'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'birth_date' => ['required', 'date'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['nacimiento'] = $validated['birth_date'];
        $validated['telefono'] = $validated['phone'];
        $validated['name'] = $validated['username'];

        unset($validated['birth_date']);
        unset($validated['phone']);
        unset($validated['username']);

        $user = User::create($validated);
        
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush();

        return redirect()->route('login');
    }
}

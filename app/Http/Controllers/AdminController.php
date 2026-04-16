<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mision;
use App\Models\Evento;
use App\Models\Recompensa;
use App\Models\PaseDePaseo;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_usuarios' => User::count(),
            'total_misiones' => Mision::count(),
            'total_eventos' => Evento::count(),
            'total_paseos' => PaseDePaseo::count(),
            'total_lugares' => Lugar::count(),
            'total_recompensas' => Recompensa::count(),
        ];

        return view('admin.dashboard', $stats);
    }

    // CRUD de Usuarios
    public function usuarios(): View
    {
        $usuarios = User::all();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function crearUsuario(): View
    {
        return view('admin.usuarios.crear');
    }

    public function guardarUsuario(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'dni' => ['required', 'string', 'max:20', 'unique:users'],
            'telefono' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nacimiento' => ['required', 'date', 'before:today'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
        ], [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.unique' => 'El correo ya está registrado.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'El DNI ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya está registrado.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function editarUsuario(User $user): View
    {
        return view('admin.usuarios.editar', compact('user'));
    }

    public function actualizarUsuario(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'dni' => ['required', 'string', 'max:20', 'unique:users,dni,' . $user->id],
            'telefono' => ['required', 'string', 'max:20'],
            'nacimiento' => ['required', 'date', 'before:today'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.unique' => 'El correo ya está registrado.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'El DNI ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya está registrado.',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function eliminarUsuario(User $user): View
    {
        return view('admin.usuarios.eliminar', compact('user'));
    }

    public function confirmarEliminar(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}

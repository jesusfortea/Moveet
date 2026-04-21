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
use App\Rules\SpanishIdentityCard;

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

    public function recompensas(): View
    {
        $recompensas = Recompensa::query()
            ->orderBy('tipo')
            ->orderBy('puntos_necesarios')
            ->get();

        return view('admin.recompensas.index', compact('recompensas'));
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
            'name' => ['required', 'string', 'regex:/^[\pL\s\-]+$/u', 'min:3', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'unique:users'],
            'dni' => ['required', 'string', new SpanishIdentityCard, 'unique:users'],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', 'confirmed'],
            'nacimiento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:users'],
            'premium' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección de correo válida.',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe ser un número válido de España (9 dígitos empezando por 6, 7, 8 o 9).',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'nacimiento.after' => 'Debes tener una fecha de nacimiento válida.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, guiones y guiones bajos.',
            'username.unique' => 'Este nombre de usuario ya está registrado.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['premium'] = $request->boolean('premium');

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
            'name' => ['required', 'string', 'regex:/^[\pL\s\-]+$/u', 'min:3', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email,' . $user->id],
            'dni' => ['required', 'string', new SpanishIdentityCard, 'unique:users,dni,' . $user->id],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/'],
            'nacimiento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', 'confirmed'],
            'premium' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección de correo válida.',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe ser un número válido de España (9 dígitos empezando por 6, 7, 8 o 9).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'nacimiento.after' => 'Debes tener una fecha de nacimiento válida.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, guiones y guiones bajos.',
            'username.unique' => 'Este nombre de usuario ya está registrado.',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $validated['premium'] = $request->boolean('premium');

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

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
            'dni' => ['required', 'string', 'unique:users'],
            'telefono' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nacimiento' => ['required', 'date'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
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
            'dni' => ['required', 'string', 'unique:users,dni,' . $user->id],
            'telefono' => ['required', 'string'],
            'nacimiento' => ['required', 'date'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
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

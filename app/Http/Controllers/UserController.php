<?php

namespace App\Http\Controllers;

use App\Models\TarjetaBancaria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    private function resolveUser(): ?User
    {
        return Auth::user();
    }

    public function index(): View|RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $inventario = $user->inventario
            ->sortByDesc('obtenida_at')
            ->values();

        return view('usuario.index', [
            'usuario' => $user,
            'inventario' => $inventario,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'ruta_imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('ruta_imagen')) {
            if ($user->ruta_imagen && Storage::disk('public')->exists($user->ruta_imagen)) {
                Storage::disk('public')->delete($user->ruta_imagen);
            }

            $validated['ruta_imagen'] = $request->file('ruta_imagen')->store('profile-images', 'public');
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
        ]);

        if (array_key_exists('ruta_imagen', $validated)) {
            $user->ruta_imagen = $validated['ruta_imagen'];
        }

        $user->save();

        return redirect()
            ->route('usuario.index')
            ->with('status', 'Perfil actualizado correctamente.');
    }

    public function inventario(): View|RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->loadMissing(['inventario.recompensa']);

        $inventario = $user->inventario
            ->sortByDesc('obtenida_at')
            ->values();

        return view('usuario.inventario', [
            'usuario' => $user,
            'inventario' => $inventario,
        ]);
    }
}

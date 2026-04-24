<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private StreakService $streakService)
    {
    }

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

        $this->streakService->syncStreakState($user);
        $user = $user->fresh();

        $inventario = $user->inventario
            ->sortByDesc('obtenida_at')
            ->values();

        $logros = $user->logros()
            ->orderByDesc('user_logros.achieved_at')
            ->get();

        $referidos = $user->referidos()
            ->with('referred:id,name,email')
            ->latest('created_at')
            ->take(5)
            ->get();

        $referidosPremiados = $user->referidos()
            ->whereNotNull('rewarded_at')
            ->count();

        return view('usuario.index', [
            'usuario' => $user,
            'inventario' => $inventario,
            'streakFreezeCost' => $this->streakService->freezeCost(),
            'logros' => $logros,
            'referidos' => $referidos,
            'referidosPremiados' => $referidosPremiados,
        ]);
    }

    public function buyStreakFreeze(): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $this->streakService->syncStreakState($user);

        if (!$this->streakService->buyFreeze($user->fresh())) {
            return redirect()
                ->route('usuario.index')
                ->with('status', 'No tienes puntos suficientes para comprar un congelador de racha.');
        }

        return redirect()
            ->route('usuario.index')
            ->with('status', 'Has comprado un congelador de racha.');
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

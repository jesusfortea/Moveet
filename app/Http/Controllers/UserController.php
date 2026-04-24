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

        $tarjetas = $user->tarjetasBancarias()
            ->latest('id')
            ->get();
        $tarjetaPrincipal = $tarjetas->first();
        $inventario = $user->inventario
            ->sortByDesc('obtenida_at')
            ->values();

        return view('usuario.index', [
            'usuario' => $user,
            'tarjetas' => $tarjetas,
            'tarjeta' => $tarjetaPrincipal,
            'tarjetaCaducada' => $tarjetaPrincipal?->esta_caducada ?? false,
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

    public function createCard(): View|RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('usuario.tarjeta', [
            'usuario' => $user,
            'tarjetas' => $user->tarjetasBancarias()->latest('id')->get(),
        ]);
    }

    public function storeCard(Request $request): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'numero_tarjeta' => ['required', 'string', 'max:24'],
            'fecha_caducidad' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/(\d{2})$/'],
            'codigo_seguridad' => ['required', 'digits_between:3,4'],
            'titular' => ['required', 'string', 'max:120'],
        ]);

        try {
            $expira = Carbon::createFromFormat('m/y', $validated['fecha_caducidad'])->endOfMonth();
            if ($expira->lt(now()->startOfDay())) {
                return back()
                    ->withErrors(['fecha_caducidad' => 'La tarjeta esta caducada.'])
                    ->withInput();
            }
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['fecha_caducidad' => 'La fecha de caducidad no es valida.'])
                ->withInput();
        }

        $soloDigitos = preg_replace('/\D+/', '', $validated['numero_tarjeta']);

        if (strlen($soloDigitos) < 12 || strlen($soloDigitos) > 19) {
            return back()
                ->withErrors(['numero_tarjeta' => 'El numero de tarjeta no es valido.'])
                ->withInput();
        }

        $ultimosCuatro = substr($soloDigitos, -4);
        $numeroEnmascarado = '**** **** **** ' . $ultimosCuatro;

        TarjetaBancaria::create([
            'user_id' => $user->id,
            'titular' => $validated['titular'],
            'numero_enmascarado' => $numeroEnmascarado,
            'fecha_caducidad' => $validated['fecha_caducidad'],
            'token_pago' => 'tok_profile_' . bin2hex(random_bytes(8)),
            'marca' => 'Visa',
        ]);

        return redirect()
            ->route('usuario.index')
            ->with('status', 'Tarjeta guardada correctamente.');
    }

    public function destroyCard(TarjetaBancaria $tarjeta): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($tarjeta->user_id !== $user->id) {
            return redirect()
                ->route('usuario.index')
                ->with('status', 'No puedes eliminar una tarjeta que no te pertenece.');
        }

        $tarjeta->delete();

        return redirect()
            ->route('usuario.index')
            ->with('status', 'Tarjeta eliminada correctamente.');
    }
}

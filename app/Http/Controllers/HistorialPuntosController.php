<?php

namespace App\Http\Controllers;

use App\Models\PuntosHistorial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistorialPuntosController extends Controller
{
    /**
     * Admin: ver historial de puntos de todos los usuarios
     */
    public function adminIndex(Request $request): View
    {
        $user = Auth::user();

        if (!$user || !(bool) $user->is_admin) {
            return abort(403);
        }

        $query = PuntosHistorial::query();

        // Filtro por usuario si se proporciona
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por rango de fechas
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $historial = $query->with('usuario', 'usuarioRelacionado')
            ->orderByDesc('created_at')
            ->paginate(50);

        $usuarios = User::orderBy('name')->get();
        $tipos = ['earned', 'spent', 'reward', 'mission', 'store', 'referral', 'admin_adjustment'];

        $estadisticas = [
            'total_ganados' => (int) PuntosHistorial::where('tipo', 'earned')->sum('cantidad'),
            'total_gastados' => (int) PuntosHistorial::where('tipo', 'spent')->sum('cantidad'),
            'total_recompensas' => (int) PuntosHistorial::where('tipo', 'reward')->sum('cantidad'),
            'top_ganadores' => PuntosHistorial::where('tipo', 'earned')
                ->groupBy('user_id')
                ->selectRaw('user_id, SUM(cantidad) as total')
                ->with('usuario')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];

        return view('admin.historial_puntos.index', [
            'historial' => $historial,
            'usuarios' => $usuarios,
            'tipos' => $tipos,
            'estadisticas' => $estadisticas,
        ]);
    }

    /**
     * Usuario: ver su propio historial de puntos
     */
    public function userIndex(Request $request): View
    {
        $user = Auth::user();

        if (!$user) {
            return abort(401);
        }

        $query = PuntosHistorial::where('user_id', $user->id);

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $historial = $query
            ->orderByDesc('created_at')
            ->paginate(30);

        $tipos = ['earned', 'spent', 'reward', 'mission', 'store', 'referral'];

        $estadisticas = [
            'total_ganados' => (int) PuntosHistorial::where('user_id', $user->id)
                ->where('tipo', 'earned')
                ->sum('cantidad'),
            'total_gastados' => (int) PuntosHistorial::where('user_id', $user->id)
                ->where('tipo', 'spent')
                ->sum('cantidad'),
            'saldo_actual' => $user->puntos,
        ];

        return view('usuario.historial_puntos', [
            'historial' => $historial,
            'tipos' => $tipos,
            'estadisticas' => $estadisticas,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PuntosHistorial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $tipo = $request->tipo;

            if ($tipo === 'store') {
                $query->where('tipo', 'spent')
                    ->where(function ($sub) {
                        $sub->where('motivo', 'like', 'Compra en tienda:%')
                            ->orWhere('motivo', 'like', 'Compra PayPal de articulo:%');
                    });
                $query->orWhere(function ($sub) {
                    $sub->where('tipo', 'earned')
                        ->where(function ($motivo) {
                            $motivo->where('motivo', 'like', 'Compra PayPal de pack de puntos:%')
                                ->orWhere('motivo', 'like', 'Compra de pack de puntos:%');
                        });
                });
            } elseif ($tipo === 'earned') {
                $query->where('tipo', 'earned')
                    ->where(function ($motivo) {
                        $motivo->whereNull('motivo')
                            ->orWhere(function ($sub) {
                                $sub->where('motivo', 'not like', 'Compra PayPal de pack de puntos:%')
                                    ->where('motivo', 'not like', 'Compra de pack de puntos:%');
                            });
                    });
            } else {
                $query->where('tipo', $tipo);
            }
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
            'total_ganados' => (int) PuntosHistorial::whereIn('tipo', ['earned', 'mission', 'reward', 'referral'])
                ->where(function ($query) {
                    $query->where('tipo', '!=', 'earned')
                        ->orWhere(function ($sub) {
                            $sub->whereNull('motivo')
                                ->orWhere(function ($motivo) {
                                    $motivo->where('motivo', 'not like', 'Compra PayPal de pack de puntos:%')
                                        ->where('motivo', 'not like', 'Compra de pack de puntos:%');
                                });
                        });
                })
                ->sum('cantidad'),
            'total_gastados' => (int) abs(PuntosHistorial::whereIn('tipo', ['spent', 'store'])
                ->sum('cantidad')),
            'total_recompensas' => (int) PuntosHistorial::where('tipo', 'reward')->sum('cantidad'),
            'top_ganadores' => PuntosHistorial::whereIn('tipo', ['earned', 'mission', 'reward', 'referral'])
                ->where(function ($query) {
                    $query->where('tipo', '!=', 'earned')
                        ->orWhere(function ($sub) {
                            $sub->whereNull('motivo')
                                ->orWhere(function ($motivo) {
                                    $motivo->where('motivo', 'not like', 'Compra PayPal de pack de puntos:%')
                                        ->where('motivo', 'not like', 'Compra de pack de puntos:%');
                                });
                        });
                })
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
            $tipo = $request->tipo;

            if ($tipo === 'store') {
                $query->where('tipo', 'spent')
                    ->where(function ($sub) {
                        $sub->where('motivo', 'like', 'Compra en tienda:%')
                            ->orWhere('motivo', 'like', 'Compra PayPal de articulo:%');
                    });
                $query->orWhere(function ($sub) {
                    $sub->where('tipo', 'earned')
                        ->where(function ($motivo) {
                            $motivo->where('motivo', 'like', 'Compra PayPal de pack de puntos:%')
                                ->orWhere('motivo', 'like', 'Compra de pack de puntos:%');
                        });
                });
            } elseif ($tipo === 'earned') {
                $query->where('tipo', 'earned')
                    ->where(function ($motivo) {
                        $motivo->whereNull('motivo')
                            ->orWhere(function ($sub) {
                                $sub->where('motivo', 'not like', 'Compra PayPal de pack de puntos:%')
                                    ->where('motivo', 'not like', 'Compra de pack de puntos:%');
                            });
                    });
            } else {
                $query->where('tipo', $tipo);
            }
        }

        $historial = $query
            ->orderByDesc('created_at')
            ->paginate(30);

        $tipos = ['earned', 'spent', 'reward', 'mission', 'store', 'referral'];

        $estadisticas = [
            'total_ganados' => (int) PuntosHistorial::where('user_id', $user->id)
                ->whereIn('tipo', ['earned', 'mission', 'reward', 'referral'])
                ->where(function ($query) {
                    $query->where('tipo', '!=', 'earned')
                        ->orWhere(function ($sub) {
                            $sub->whereNull('motivo')
                                ->orWhere(function ($motivo) {
                                    $motivo->where('motivo', 'not like', 'Compra PayPal de pack de puntos:%')
                                        ->where('motivo', 'not like', 'Compra de pack de puntos:%');
                                });
                        });
                })
                ->sum('cantidad'),
            'total_gastados' => (int) PuntosHistorial::where('user_id', $user->id)
                ->whereIn('tipo', ['spent', 'store'])
                ->sum(DB::raw('ABS(cantidad)')),
            'saldo_actual' => $user->puntos,
        ];

        return view('usuario.historial_puntos', [
            'historial' => $historial,
            'tipos' => $tipos,
            'estadisticas' => $estadisticas,
        ]);
    }

    public function userDownload(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return abort(401);
        }

        $query = PuntosHistorial::where('user_id', $user->id);

        if ($request->filled('tipo')) {
            $tipo = $request->tipo;

            if ($tipo === 'store') {
                $query->where('tipo', 'spent')
                    ->where(function ($sub) {
                        $sub->where('motivo', 'like', 'Compra en tienda:%')
                            ->orWhere('motivo', 'like', 'Compra PayPal de articulo:%');
                    });
            } else {
                $query->where('tipo', $tipo);
            }
        }

        $registros = $query
            ->orderByDesc('created_at')
            ->get();

        $timestamp = now()->format('Ymd_His');
        $filename = 'historial_puntos_' . $timestamp . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        return response()->streamDownload(function () use ($registros) {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Fecha',
                'Tipo',
                'Cantidad',
                'Motivo',
                'Relacionado',
                'FacturaId',
            ]);

            foreach ($registros as $registro) {
                $cantidad = (int) $registro->cantidad;
                $related = $registro->related_user_id ? (string) $registro->related_user_id : '';
                $facturaId = ($registro->related_model === \App\Models\Factura::class)
                    ? (string) $registro->related_model_id
                    : '';
                $isStoreEarned = $registro->tipo === 'earned'
                    && ($registro->motivo && (
                        str_starts_with($registro->motivo, 'Compra PayPal de pack de puntos:')
                        || str_starts_with($registro->motivo, 'Compra de pack de puntos:')
                    ));
                $tipoExport = $isStoreEarned ? 'store' : (string) $registro->tipo;

                fputcsv($output, [
                    $registro->created_at?->format('Y-m-d H:i:s') ?? '',
                    $tipoExport,
                    (string) $cantidad,
                    (string) ($registro->motivo ?? ''),
                    $related,
                    $facturaId,
                ]);
            }

            fclose($output);
        }, $filename, $headers);
    }
}

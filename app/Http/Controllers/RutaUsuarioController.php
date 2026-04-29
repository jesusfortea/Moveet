<?php

namespace App\Http\Controllers;

use App\Models\RutaUsuario;
use App\Models\RutaUsuarioAttempt;
use App\Models\RutaUsuarioCompletion;
use App\Models\RutaUsuarioRating;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AchievementService;
use App\Services\NotificationService;
use App\Services\PointsHistoryService;
use App\Services\LevelService;

class RutaUsuarioController extends Controller
{
    private const CREATOR_REWARD_PERCENT = 0.10;
    private const CREATOR_DAILY_CAP = 500;

    public function __construct(
        private PointsHistoryService $pointsHistoryService,
        private NotificationService $notificationService,
        private AchievementService $achievementService,
        private LevelService $levelService,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $rutas = RutaUsuario::query()
            ->with('creador:id,name,username')
            ->where('publicado', true)
            ->where('activo', true)
            ->latest()
            ->get();

        $misRutas = RutaUsuario::query()
            ->where('creator_user_id', $user->id)
            ->latest()
            ->get();

        $misCompletadas = RutaUsuarioCompletion::query()
            ->where('user_id', $user->id)
            ->pluck('ruta_usuario_id')
            ->all();

        $misValoraciones = RutaUsuarioRating::query()
            ->where('user_id', $user->id)
            ->pluck('estrellas', 'ruta_usuario_id');

        $misIntentos = RutaUsuarioAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed'])
            ->get()
            ->keyBy('ruta_usuario_id');

        return view('rutas.index', [
            'rutas' => $rutas,
            'misRutas' => $misRutas,
            'misCompletadas' => $misCompletadas,
            'misValoraciones' => $misValoraciones,
            'misIntentos' => $misIntentos,
            'canCreate' => $this->canCreateRoute($user),
        ]);
    }

    public function crear(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->canCreateRoute($user)) {
            return redirect()
                ->route('rutas.index')
                ->with('status', 'Necesitas ser premium para crear rutas.');
        }

        return view('rutas.crear');
    }

    public function editar(RutaUsuario $ruta): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($ruta->creator_user_id !== $user->id) {
            return redirect()->route('rutas.index')->with('status', 'Solo puedes editar tus propias rutas.');
        }

        return view('rutas.editar', [
            'ruta' => $ruta,
        ]);
    }

    public function iniciar(RutaUsuario $ruta): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isRoutePlayableByUser($ruta, $user)) {
            return redirect()->route('rutas.index')->with('status', 'No puedes iniciar esta ruta.');
        }

        $completedCompletion = RutaUsuarioCompletion::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->first();

        $attempt = RutaUsuarioAttempt::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (!$attempt && !$completedCompletion) {
            $attempt = RutaUsuarioAttempt::create([
                'ruta_usuario_id' => $ruta->id,
                'user_id' => $user->id,
                'status' => 'active',
                'current_checkpoint_index' => 0,
                'checkpoint_total' => $this->getRouteCheckpoints($ruta)->count(),
                'verification_threshold_meters' => 40,
                'verification_token' => (string) Str::uuid(),
                'started_at' => now(),
            ]);
        } elseif (!$attempt) {
            $attempt = RutaUsuarioAttempt::create([
                'ruta_usuario_id' => $ruta->id,
                'user_id' => $user->id,
                'status' => 'completed',
                'current_checkpoint_index' => $this->getRouteCheckpoints($ruta)->count(),
                'checkpoint_total' => $this->getRouteCheckpoints($ruta)->count(),
                'verification_threshold_meters' => 40,
                'verification_token' => (string) Str::uuid(),
                'started_at' => $completedCompletion?->created_at ?? now(),
                'completed_at' => $completedCompletion?->created_at ?? now(),
                'last_verified_at' => $completedCompletion?->created_at ?? now(),
            ]);
        }

        if (!$attempt->verification_token) {
            $attempt->verification_token = (string) Str::uuid();
            $attempt->save();
        }

        if ($attempt->checkpoint_total === 0) {
            $attempt->checkpoint_total = $this->getRouteCheckpoints($ruta)->count();
            $attempt->save();
        }

        return view('rutas.iniciar', [
            'ruta' => $ruta,
            'attempt' => $attempt,
            'checkpoints' => $this->getRouteCheckpoints($ruta),
        ]);
    }

    public function verificarCheckpoint(Request $request, RutaUsuario $ruta)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isRoutePlayableByUser($ruta, $user)) {
            return $this->routeResponse($request, false, 'No puedes verificar esta ruta.', 403);
        }

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'checkpoint_index' => ['required', 'integer', 'min:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $attempt = RutaUsuarioAttempt::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->where('verification_token', $validated['token'])
            ->where('status', 'active')
            ->first();

        if (!$attempt) {
            return $this->routeResponse($request, false, 'No hay un intento activo para esta ruta.', 404);
        }

        $checkpoints = $this->getRouteCheckpoints($ruta);
        $expectedIndex = (int) $attempt->current_checkpoint_index;

        if ((int) $validated['checkpoint_index'] !== $expectedIndex) {
            return $this->routeResponse($request, false, 'Ese no es el siguiente punto pendiente.', 422);
        }

        if (!$checkpoints->has($expectedIndex)) {
            return $this->routeResponse($request, false, 'No quedan puntos pendientes.', 422);
        }

        $checkpoint = $checkpoints->get($expectedIndex);
        $distance = $this->distanceMeters(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $checkpoint['lat'],
            (float) $checkpoint['lng']
        );

        if ($distance > (float) $attempt->verification_threshold_meters) {
            return $this->routeResponse($request, false, 'Aun no estas lo bastante cerca del punto.', 422);
        }

        $attempt->current_checkpoint_index = $expectedIndex + 1;
        $attempt->last_verified_at = now();
        $attempt->last_latitude = $validated['latitude'];
        $attempt->last_longitude = $validated['longitude'];

        $completed = $attempt->current_checkpoint_index >= $attempt->checkpoint_total;

        if ($completed) {
            $attempt->status = 'completed';
            $attempt->completed_at = now();
        }

        $attempt->save();

        if ($completed) {
            $completionExists = RutaUsuarioCompletion::query()
                ->where('ruta_usuario_id', $ruta->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$completionExists) {
                DB::transaction(function () use ($user, $ruta) {
                    $points = (int) $ruta->puntos_recompensa;
                    $exp = (int) ($points * 0.5);

                    // Aplicar boosters
                    if ($user->points_booster_until && now()->lessThanOrEqualTo($user->points_booster_until)) {
                        $points *= 2;
                    }

                    if ($user->exp_booster_until && now()->lessThanOrEqualTo($user->exp_booster_until)) {
                        $exp *= 2;
                    }

                    $user->increment('puntos', $points);
                    
                    // Subir nivel
                    $this->levelService->addExperience($user, $exp);

                    RutaUsuarioCompletion::create([
                        'ruta_usuario_id' => $ruta->id,
                        'user_id' => $user->id,
                        'puntos_otorgados' => $points,
                    ]);

                    $this->refreshStats($ruta);
                });

                $this->pointsHistoryService->log(
                    $user,
                    'earned',
                    (int) $ruta->puntos_recompensa,
                    'Ruta completada: ' . $ruta->titulo
                );

                $this->notificationService->notify(
                    $user->id,
                    'route',
                    'Ruta completada',
                    'Has completado "' . $ruta->titulo . '" y ganado ' . (int) $ruta->puntos_recompensa . ' puntos.',
                    route('usuario.historial_puntos')
                );

                $this->achievementService->syncBaseAchievements($user->fresh());
            }

            return $this->routeResponse($request, true, 'Ruta completada correctamente.', 200, [
                'completed' => true,
                'progress' => $attempt->current_checkpoint_index,
                'total' => $attempt->checkpoint_total,
            ]);
        }

        return $this->routeResponse($request, true, 'Punto verificado correctamente. Sigue con el siguiente.', 200, [
            'completed' => false,
            'progress' => $attempt->current_checkpoint_index,
            'total' => $attempt->checkpoint_total,
        ]);
    }

    public function actualizar(Request $request, RutaUsuario $ruta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($ruta->creator_user_id !== $user->id) {
            return redirect()->route('rutas.index')->with('status', 'Solo puedes editar tus propias rutas.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'dificultad' => ['required', 'in:facil,media,dificil'],
            'puntos_recompensa' => ['required', 'integer', 'min:20', 'max:500'],
            'min_nivel' => ['required', 'integer', 'min:1', 'max:100'],
            'premium_only' => ['nullable', 'boolean'],
            'publicado' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $ruta->update([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'dificultad' => $validated['dificultad'],
            'puntos_recompensa' => $validated['puntos_recompensa'],
            'min_nivel' => $validated['min_nivel'],
            'premium_only' => (bool) ($validated['premium_only'] ?? false),
            'publicado' => (bool) ($validated['publicado'] ?? false),
            'activo' => (bool) ($validated['activo'] ?? true),
        ]);

        return redirect()
            ->route('rutas.index')
            ->with('status', 'Ruta actualizada correctamente.');
    }

    public function confirmarCompletar(RutaUsuario $ruta): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$ruta->activo || !$ruta->publicado) {
            return redirect()->route('rutas.index')->with('status', 'La ruta no esta disponible.');
        }

        if ($ruta->creator_user_id === $user->id) {
            return redirect()->route('rutas.index')->with('status', 'No puedes completar tu propia ruta para ganar puntos.');
        }

        if ($ruta->premium_only && !(bool) $user->premium) {
            return redirect()->route('rutas.index')->with('status', 'Esta ruta es solo para usuarios premium.');
        }

        if ((int) $user->nivel < (int) $ruta->min_nivel) {
            return redirect()->route('rutas.index')->with('status', 'No tienes el nivel requerido para esta ruta.');
        }

        $alreadyCompleted = RutaUsuarioCompletion::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyCompleted) {
            return redirect()->route('rutas.index')->with('status', 'Ya completaste esta ruta.');
        }

        return view('rutas.confirmar', [
            'ruta' => $ruta,
        ]);
    }

    public function guardar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->canCreateRoute($user)) {
            return redirect()
                ->route('rutas.index')
                ->with('status', 'No tienes permisos para publicar rutas.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'dificultad' => ['required', 'in:facil,media,dificil'],
            'distancia_metros' => ['required', 'integer', 'min:200', 'max:50000'],
            'puntos_recompensa' => ['required', 'integer', 'min:20', 'max:500'],
            'ruta_geojson' => ['required', 'json'],
            'min_nivel' => ['required', 'integer', 'min:1', 'max:100'],
            'premium_only' => ['nullable', 'boolean'],
            'publicado' => ['nullable', 'boolean'],
        ]);

        RutaUsuario::create([
            'creator_user_id' => $user->id,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'dificultad' => $validated['dificultad'],
            'distancia_metros' => $validated['distancia_metros'],
            'puntos_recompensa' => $validated['puntos_recompensa'],
            'ruta_geojson' => json_decode($validated['ruta_geojson'], true, 512, JSON_THROW_ON_ERROR),
            'min_nivel' => $validated['min_nivel'],
            'premium_only' => (bool) ($validated['premium_only'] ?? false),
            'publicado' => (bool) ($validated['publicado'] ?? true),
            'activo' => true,
        ]);

        return redirect()
            ->route('rutas.index')
            ->with('status', 'Ruta publicada correctamente.');
    }

    public function completar(RutaUsuario $ruta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$ruta->activo || !$ruta->publicado) {
            return redirect()->route('rutas.index')->with('status', 'La ruta no esta disponible.');
        }

        if ($ruta->creator_user_id === $user->id) {
            return redirect()->route('rutas.index')->with('status', 'No puedes completar tu propia ruta para ganar puntos.');
        }

        if ($ruta->premium_only && !(bool) $user->premium) {
            return redirect()->route('rutas.index')->with('status', 'Esta ruta es solo para usuarios premium.');
        }

        if ((int) $user->nivel < (int) $ruta->min_nivel) {
            return redirect()->route('rutas.index')->with('status', 'No tienes el nivel requerido para esta ruta.');
        }

        $alreadyCompleted = RutaUsuarioCompletion::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyCompleted) {
            return redirect()->route('rutas.index')->with('status', 'Ya completaste esta ruta.');
        }

        return redirect()->route('rutas.iniciar', $ruta)->with('status', 'Usa la ruta para ir verificando cada punto.');
    }

    public function valorar(Request $request, RutaUsuario $ruta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($ruta->creator_user_id === $user->id) {
            return redirect()->route('rutas.index')->with('status', 'No puedes valorar tu propia ruta.');
        }

        $validated = $request->validate([
            'estrellas' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        $completion = RutaUsuarioCompletion::query()
            ->where('ruta_usuario_id', $ruta->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$completion) {
            return redirect()->route('rutas.index')->with('status', 'Primero debes completar la ruta para valorarla.');
        }

        DB::transaction(function () use ($user, $ruta, $completion, $validated) {
            RutaUsuarioRating::updateOrCreate(
                [
                    'ruta_usuario_id' => $ruta->id,
                    'user_id' => $user->id,
                ],
                [
                    'estrellas' => (int) $validated['estrellas'],
                    'comentario' => $validated['comentario'] ?? null,
                ]
            );

            if ((int) $validated['estrellas'] === 5 && !$completion->creator_rewarded_at) {
                $creator = $ruta->creador;

                if ($creator) {
                    $rewardCandidate = (int) floor((int) $completion->puntos_otorgados * self::CREATOR_REWARD_PERCENT);
                    $earnedToday = (int) RutaUsuarioCompletion::query()
                        ->whereHas('ruta', fn ($q) => $q->where('creator_user_id', $creator->id))
                        ->whereDate('creator_rewarded_at', now()->toDateString())
                        ->sum('creator_reward_points');

                    $remainingCap = max(0, self::CREATOR_DAILY_CAP - $earnedToday);
                    $reward = min($rewardCandidate, $remainingCap);

                    if ($reward > 0) {
                        $creator->increment('puntos', $reward);

                        $this->pointsHistoryService->log(
                            $creator,
                            'reward',
                            $reward,
                            'Bonus creador por valoracion 5 estrellas de ruta: ' . $ruta->titulo,
                            $user->id
                        );

                        $this->notificationService->notify(
                            $creator->id,
                            'route',
                            'Tu ruta ha sido valorada con 5 estrellas',
                            'Has recibido ' . $reward . ' puntos de bonus por tu ruta "' . $ruta->titulo . '".',
                            route('rutas.index')
                        );

                        $completion->update([
                            'creator_reward_points' => $reward,
                            'creator_rewarded_at' => now(),
                        ]);
                    }
                }
            }

            $this->refreshStats($ruta);
        });

        return redirect()->route('rutas.index')->with('status', 'Gracias por valorar la ruta.');
    }

    private function canCreateRoute($user): bool
    {
        return (bool) $user->premium;
    }

    private function canManageRoute($user, RutaUsuario $ruta): bool
    {
        return (bool) $user && $ruta->creator_user_id === $user->id;
    }

    private function isRoutePlayableByUser(RutaUsuario $ruta, $user): bool
    {
        return $ruta->activo
            && $ruta->publicado
            && $ruta->creator_user_id !== $user->id
            && (!$ruta->premium_only || (bool) $user->premium)
            && (int) $user->nivel >= (int) $ruta->min_nivel;
    }

    private function getRouteCheckpoints(RutaUsuario $ruta)
    {
        $coordinates = data_get($ruta->ruta_geojson, 'geometry.coordinates', data_get($ruta->ruta_geojson, 'coordinates', []));

        return collect($coordinates)
            ->map(function ($coordinate) {
                return [
                    'lng' => (float) ($coordinate[0] ?? 0),
                    'lat' => (float) ($coordinate[1] ?? 0),
                ];
            })
            ->values();
    }

    private function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function routeResponse(Request $request, bool $success, string $message, int $status = 200, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge([
                'success' => $success,
                'message' => $message,
            ], $extra), $status);
        }

        return redirect()
            ->route('rutas.index')
            ->with('status', $message);
    }

    private function refreshStats(RutaUsuario $ruta): void
    {
        $ratingCount = $ruta->ratings()->count();
        $ratingAvg = (float) ($ruta->ratings()->avg('estrellas') ?? 0);
        $completadasCount = $ruta->completions()->count();
        $puntosGenerados = (int) $ruta->completions()->sum('creator_reward_points');

        $ruta->update([
            'rating_count' => $ratingCount,
            'rating_promedio' => $ratingCount > 0 ? round($ratingAvg, 2) : 0,
            'completadas_count' => $completadasCount,
            'puntos_generados' => $puntosGenerados,
        ]);
    }
}

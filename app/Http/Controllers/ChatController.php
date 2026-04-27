<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Contacto;
use App\Models\Mensaje;
use App\Models\SolicitudAmistad;
use App\Models\User;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AchievementService;
use App\Services\NotificationService;

class ChatController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private AchievementService $achievementService,
    ) {
    }

    private function resolveUser(): ?User
    {
        return Auth::user();
    }

    private function generateUniqueFriendCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (User::query()->where('friend_code', $code)->exists());

        return $code;
    }

    private function ensureFriendCode(User $user): string
    {
        if (preg_match('/^\d{6}$/', (string) $user->friend_code) === 1) {
            return $user->friend_code;
        }

        $user->forceFill([
            'friend_code' => $this->generateUniqueFriendCode(),
        ])->save();

        return $user->friend_code;
    }

    private function resolveUserFromQrValue(string $value): ?User
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = parse_url($value, PHP_URL_PATH);

            if (!$path) {
                return null;
            }

            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            $value = end($segments) ?: '';
        }

        if (preg_match('/^\d{6}$/', $value) === 1) {
            return User::query()->where('friend_code', $value)->first();
        }

        return null;
    }

    private function resolveLegacyInvitationUser(string $value, User $currentUser): ?User
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = parse_url($value, PHP_URL_PATH);

            if (!$path) {
                return null;
            }

            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            $value = end($segments) ?: '';
        }

        $padding = strlen($value) % 4;

        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if (!$decoded) {
            return null;
        }

        $parts = explode('|', $decoded);

        if (count($parts) !== 2 || !ctype_digit($parts[0]) || !ctype_digit($parts[1])) {
            return null;
        }

        [$emisorId, $receptorId] = array_map('intval', $parts);

        if ($receptorId !== $currentUser->id) {
            return null;
        }

        return User::find($emisorId);
    }

    private function generatePersonalQrData(User $user): array
    {
        $code = $this->ensureFriendCode($user);
        $url = route('chat.qr.accept', ['code' => $code]);
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::L,
            'svgPreserveAspectRatio' => 'xMidYMid meet',
            'svgViewBoxSize' => 300,
            'drawLightModules' => true,
        ]);
        
        $qrCode = new QRCode($options);
        // Obtener el SVG como string puro (no como data URI)
        $svg = $qrCode->render($url);
        
        // Si es un data URI, extraer solo el SVG
        if (strpos($svg, 'data:image/svg+xml') === 0) {
            // Decodificar desde base64
            $parts = explode(',', $svg, 2);
            if (count($parts) === 2) {
                $svg = base64_decode($parts[1]);
            }
        }

        return [
            'code' => $code,
            'url' => $url,
            'svg' => $svg,
        ];
    }

    private function ensureAcceptedContactPair(int $firstUserId, int $secondUserId): Contacto
    {
        $contactoPrincipal = Contacto::firstOrCreate([
            'user_id' => $firstUserId,
            'contacto_id' => $secondUserId,
        ]);

        Contacto::firstOrCreate([
            'user_id' => $secondUserId,
            'contacto_id' => $firstUserId,
        ]);

        Chat::firstOrCreate([
            'contacto_id' => $contactoPrincipal->id,
        ]);

        return $contactoPrincipal;
    }

    private function resolveFriendshipAction(User $user, User $target): array
    {
        if ($user->id === $target->id) {
            return [
                'ok' => false,
                'message' => 'No puedes agregarte a ti mismo.',
                'contact_id' => null,
            ];
        }

        $contactoActual = Contacto::query()
            ->where('user_id', $user->id)
            ->where('contacto_id', $target->id)
            ->first();

        if ($contactoActual) {
            return [
                'ok' => true,
                'message' => 'Ese usuario ya es tu contacto.',
                'contact_id' => $contactoActual->id,
            ];
        }

        $solicitudRecibida = SolicitudAmistad::query()
            ->where('emisor_id', $target->id)
            ->where('receptor_id', $user->id)
            ->first();

        if ($solicitudRecibida && $solicitudRecibida->estado === 'pendiente') {
            DB::transaction(function () use ($solicitudRecibida, $target, $user) {
                $solicitudRecibida->update(['estado' => 'aceptada']);
                $this->ensureAcceptedContactPair($target->id, $user->id);
            });

            $contactoActual = Contacto::query()
                ->where('user_id', $user->id)
                ->where('contacto_id', $target->id)
                ->first();

            return [
                'ok' => true,
                'message' => "Solicitud aceptada. Ya puedes chatear con {$target->name}.",
                'contact_id' => $contactoActual?->id,
            ];
        }

        if ($solicitudRecibida && $solicitudRecibida->estado === 'aceptada') {
            $this->ensureAcceptedContactPair($target->id, $user->id);

            $contactoActual = Contacto::query()
                ->where('user_id', $user->id)
                ->where('contacto_id', $target->id)
                ->first();

            return [
                'ok' => true,
                'message' => 'Ese usuario ya es tu contacto.',
                'contact_id' => $contactoActual?->id,
            ];
        }

        $solicitudEnviada = SolicitudAmistad::query()
            ->where('emisor_id', $user->id)
            ->where('receptor_id', $target->id)
            ->first();

        if ($solicitudEnviada) {
            if ($solicitudEnviada->estado === 'pendiente') {
                return [
                    'ok' => true,
                    'message' => 'Ya enviaste una solicitud a este usuario.',
                    'contact_id' => null,
                ];
            }

            if ($solicitudEnviada->estado === 'aceptada') {
                $this->ensureAcceptedContactPair($user->id, $target->id);

                $contactoActual = Contacto::query()
                    ->where('user_id', $user->id)
                    ->where('contacto_id', $target->id)
                    ->first();

                return [
                    'ok' => true,
                    'message' => 'Ese usuario ya es tu contacto.',
                    'contact_id' => $contactoActual?->id,
                ];
            }

            $solicitudEnviada->update(['estado' => 'pendiente']);

            return [
                'ok' => true,
                'message' => "Solicitud reenviada a {$target->name}.",
                'contact_id' => null,
            ];
        }

        try {
            SolicitudAmistad::create([
                'emisor_id' => $user->id,
                'receptor_id' => $target->id,
                'estado' => 'pendiente',
            ]);
        } catch (QueryException $exception) {
            if ((int) $exception->getCode() === 23000) {
                return [
                    'ok' => true,
                    'message' => 'Ya existe una solicitud con ese usuario.',
                    'contact_id' => null,
                ];
            }

            throw $exception;
        }

        return [
            'ok' => true,
            'message' => "Solicitud enviada a {$target->name}.",
            'contact_id' => null,
        ];
    }

    private function ensureChat(Contacto $contacto): Chat
    {
        return $this->resolveSharedChat($contacto) ?? Chat::create([
            'contacto_id' => $contacto->id,
        ]);
    }

    private function resolveSharedChat(Contacto $contacto): ?Chat
    {
        $pairContactIds = Contacto::query()
            ->where(function ($query) use ($contacto) {
                $query->where('id', $contacto->id)
                    ->orWhere(function ($subQuery) use ($contacto) {
                        $subQuery->where('user_id', $contacto->contacto_id)
                            ->where('contacto_id', $contacto->user_id);
                    });
            })
            ->pluck('id')
            ->all();

        if (empty($pairContactIds)) {
            return null;
        }

        return Chat::query()
            ->with('ultimoMensaje')
            ->whereIn('contacto_id', $pairContactIds)
            ->latest('id')
            ->first();
    }

    private function contactPayload(Contacto $contacto): array
    {
        $chat = $this->resolveSharedChat($contacto);
        $bloqueadoPorOtro = Contacto::query()
            ->where('user_id', $contacto->contacto_id)
            ->where('contacto_id', Auth::id())
            ->where('bloqueado', true)
            ->exists();

        return [
            'id' => $contacto->id,
            'nombre' => $contacto->amigo?->name ?? 'Contacto',
            'avatar' => $contacto->amigo?->ruta_imagen_url,
            'chat_id' => $chat?->id,
            'ultimo_mensaje' => $chat?->ultimoMensaje?->contenido,
            'ultimo_mensaje_at' => $chat?->ultimoMensaje?->created_at?->toISOString(),
            'bloqueado' => (bool) $contacto->bloqueado || $bloqueadoPorOtro,
            'bloqueado_por_mi' => (bool) $contacto->bloqueado,
            'bloqueado_por_otro' => $bloqueadoPorOtro,
        ];
    }

    private function messagePayload(Mensaje $mensaje): array
    {
        return [
            'id' => $mensaje->id,
            'chat_id' => $mensaje->chat_id,
            'emisor_id' => $mensaje->emisor_id,
            'contenido' => $mensaje->contenido,
            'created_at' => $mensaje->created_at?->toISOString(),
            'hora' => $mensaje->created_at?->format('H:i'),
            'mio' => $mensaje->emisor_id === Auth::id(),
        ];
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $contactosModels = Contacto::query()
            ->with(['amigo'])
            ->where('user_id', $user->id)
            ->get();

        $contactos = $contactosModels
            ->map(function (Contacto $contacto) use ($user) {
                $chat = $this->resolveSharedChat($contacto);
                $bloqueadoPorOtro = Contacto::query()
                    ->where('user_id', $contacto->contacto_id)
                    ->where('contacto_id', $user->id)
                    ->where('bloqueado', true)
                    ->exists();

                return [
                    'id' => $contacto->id,
                    'nombre' => $contacto->amigo?->name ?? 'Contacto',
                    'avatar' => $contacto->amigo?->ruta_imagen_url,
                    'chat_id' => $chat?->id,
                    'ultimo_mensaje' => $chat?->ultimoMensaje?->contenido,
                    'ultimo_mensaje_at' => $chat?->ultimoMensaje?->created_at?->toISOString(),
                    'bloqueado' => (bool) $contacto->bloqueado || $bloqueadoPorOtro,
                    'bloqueado_por_mi' => (bool) $contacto->bloqueado,
                    'bloqueado_por_otro' => $bloqueadoPorOtro,
                    'contacto_id' => $contacto->contacto_id,
                    'model' => $contacto,
                ];
            });

        $selectedContactId = $request->query('contacto');
        $selectedContactId = is_numeric($selectedContactId) ? (int) $selectedContactId : null;
        $contactoSeleccionado = $selectedContactId ? $contactos->firstWhere('id', $selectedContactId) : null;
        $mensajes = collect();
        $chatSeleccionado = null;

        if ($contactoSeleccionado && $contactoSeleccionado['chat_id']) {
            $chatSeleccionado = Chat::query()->with('ultimoMensaje')->find($contactoSeleccionado['chat_id']);

            $mensajes = Mensaje::query()
                ->with('emisor')
                ->where('chat_id', $contactoSeleccionado['chat_id'])
                ->orderBy('created_at')
                ->get();
        }

        $usersDisponibles = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        $solicitudesRecibidas = SolicitudAmistad::query()
            ->with('emisor')
            ->where('receptor_id', $user->id)
            ->where('estado', 'pendiente')
            ->latest('created_at')
            ->get();

        return view('chat.index', [
            'usuario' => $user,
            'contactos' => $contactos,
            'contactosModels' => $contactosModels,
            'contactoSeleccionadoId' => $contactoSeleccionado['id'] ?? null,
            'contactoSeleccionado' => $contactoSeleccionado,
            'chatSeleccionado' => $chatSeleccionado,
            'mensajes' => $mensajes,
            'usersDisponibles' => $usersDisponibles,
            'solicitudesRecibidas' => $solicitudesRecibidas,
            'personalQr' => $this->generatePersonalQrData($user),
        ]);
    }

    public function messages(Request $request, Contacto $contacto): JsonResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if ($contacto->user_id !== $user->id) {
            return response()->json(['message' => 'Contacto no permitido'], 403);
        }

        $contacto->load(['amigo', 'chat.ultimoMensaje']);
        $chat = $this->ensureChat($contacto);

        $mensajes = Mensaje::query()
            ->with('emisor')
            ->where('chat_id', $chat->id)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'contacto' => $this->contactPayload($contacto),
            'messages' => $mensajes->map(fn(Mensaje $mensaje) => $this->messagePayload($mensaje))->values(),
            'last_message_id' => $mensajes->last()?->id,
        ]);
    }

    public function sendMessage(Request $request, Contacto $contacto): JsonResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if ($contacto->user_id !== $user->id) {
            return response()->json(['message' => 'Contacto no permitido'], 403);
        }

        if ($contacto->bloqueado) {
            return response()->json(['message' => 'Has bloqueado a este contacto. Desbloquéalo para poder enviar mensajes.'], 403);
        }

        $bloqueadoPorDestino = Contacto::query()
            ->where('user_id', $contacto->contacto_id)
            ->where('contacto_id', $user->id)
            ->where('bloqueado', true)
            ->exists();

        if ($bloqueadoPorDestino) {
            return response()->json(['message' => 'Este usuario te ha bloqueado y no puedes enviarle mensajes.'], 403);
        }

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:2000'],
        ]);

        $mensaje = DB::transaction(function () use ($contacto, $user, $validated) {
            $chat = $this->ensureChat($contacto);

            return Mensaje::create([
                'chat_id' => $chat->id,
                'emisor_id' => $user->id,
                'contenido' => trim($validated['contenido']),
            ]);
        });

        $this->notificationService->notify(
            $contacto->contacto_id,
            'chat',
            'Nuevo mensaje de ' . $user->name,
            trim($validated['contenido']),
            route('chat.index', ['contacto' => $contacto->id])
        );

        $mensaje->load('emisor');
        $contacto->load(['amigo', 'chat.ultimoMensaje']);

        return response()->json([
            'message' => 'Mensaje enviado',
            'contacto' => $this->contactPayload($contacto),
            'message_item' => $this->messagePayload($mensaje),
            'last_message_id' => $mensaje->id,
        ]);
    }

    public function storeContact(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'contacto' => ['required', 'string', 'max:255'],
        ]);

        $term = trim($validated['contacto']);

        $contactoUsuario = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($query) use ($term) {
                $query->where('name', $term)
                    ->orWhere('email', $term);
            })
            ->first();

        if (!$contactoUsuario) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se ha encontrado ningún usuario con ese nombre o email.',
                ], 404);
            }

            return back()
                ->withErrors(['contacto' => 'No se ha encontrado ningún usuario con ese nombre o email.'])
                ->withInput();
        }

        $result = $this->resolveFriendshipAction($user, $contactoUsuario);
        $redirectUrl = $result['contact_id']
            ? route('chat.index', ['contacto' => $result['contact_id']])
            : route('chat.index');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $result['ok'],
                'message' => $result['message'],
                'contact_id' => $result['contact_id'],
                'redirect_url' => $redirectUrl,
            ], $result['ok'] ? 200 : 422);
        }

        return redirect()->to($redirectUrl)->with($result['ok'] ? 'status' : 'error', $result['message']);
    }

    public function acceptInvitation(Request $request, string $code): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $friend = $this->resolveUserFromQrValue($code)
            ?? $this->resolveLegacyInvitationUser($code, $user);

        if (!$friend) {
            return redirect()->route('chat.index')->with('error', 'Código QR inválido.');
        }

        $result = $this->resolveFriendshipAction($user, $friend);
        $redirectUrl = $result['contact_id']
            ? route('chat.index', ['contacto' => $result['contact_id']])
            : route('chat.index');

        return redirect()->to($redirectUrl)->with($result['ok'] ? 'status' : 'error', $result['message']);
    }

    public function scanQr(Request $request): JsonResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        $validated = $request->validate([
            'qr_value' => ['required', 'string', 'max:4000'],
        ]);

        $friend = $this->resolveUserFromQrValue($validated['qr_value'])
            ?? $this->resolveLegacyInvitationUser($validated['qr_value'], $user);

        if (!$friend) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha podido leer un QR válido de Moveet.',
            ], 422);
        }

        $result = $this->resolveFriendshipAction($user, $friend);

        return response()->json([
            'ok' => $result['ok'],
            'message' => $result['message'],
            'contact_id' => $result['contact_id'],
            'redirect_url' => $result['contact_id']
                ? route('chat.index', ['contacto' => $result['contact_id']])
                : route('chat.index'),
        ], $result['ok'] ? 200 : 422);
    }

    public function acceptRequest(SolicitudAmistad $solicitud): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($solicitud->receptor_id !== $user->id) {
            abort(403, 'No autorizado para esta solicitud.');
        }

        if ($solicitud->estado !== 'pendiente') {
            return redirect()->route('chat.index')->with('status', 'La solicitud ya fue procesada.');
        }

        DB::transaction(function () use ($solicitud) {
            $solicitud->update(['estado' => 'aceptada']);
            $this->ensureAcceptedContactPair($solicitud->emisor_id, $solicitud->receptor_id);
        });

        $this->notificationService->notify(
            $solicitud->emisor_id,
            'social',
            'Solicitud aceptada',
            'Tu solicitud fue aceptada. Ya puedes chatear con tu nuevo contacto.',
            route('chat.index')
        );

        $this->achievementService->syncBaseAchievements($user);

        return redirect()->route('chat.index')->with('status', 'Solicitud aceptada. Ya podéis chatear.');
    }

    public function rejectRequest(SolicitudAmistad $solicitud): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($solicitud->receptor_id !== $user->id) {
            abort(403, 'No autorizado para esta solicitud.');
        }

        if ($solicitud->estado === 'pendiente') {
            $solicitud->update(['estado' => 'rechazada']);
        }

        return redirect()->route('chat.index')->with('status', 'Solicitud rechazada.');
    }

    public function deleteContact(Contacto $contacto): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($contacto->user_id !== $user->id) {
            abort(403, 'No autorizado para eliminar este contacto.');
        }

        $nombreAmigo = $contacto->amigo?->name ?? 'Contacto';

        DB::transaction(function () use ($contacto, $user) {
            Contacto::query()
                ->where('user_id', $contacto->contacto_id)
                ->where('contacto_id', $user->id)
                ->delete();

            $contacto->delete();

            SolicitudAmistad::query()
                ->where(function ($query) use ($user, $contacto) {
                    $query->where('emisor_id', $user->id)
                        ->where('receptor_id', $contacto->contacto_id);
                })
                ->orWhere(function ($query) use ($user, $contacto) {
                    $query->where('emisor_id', $contacto->contacto_id)
                        ->where('receptor_id', $user->id);
                })
                ->delete();
        });

        return redirect()->route('chat.index')->with('status', "Contacto {$nombreAmigo} eliminado.");
    }

    public function blockContact(Contacto $contacto): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($contacto->user_id !== $user->id) {
            abort(403, 'No autorizado para bloquear este contacto.');
        }

        $nombreAmigo = $contacto->amigo?->name ?? 'Contacto';

        $contacto->update(['bloqueado' => true]);

        return redirect()->route('chat.index')->with('status', "Usuario {$nombreAmigo} bloqueado. No recibirá tus mensajes.");
    }

    public function unblockContact(Contacto $contacto): RedirectResponse
    {
        $user = $this->resolveUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($contacto->user_id !== $user->id) {
            abort(403, 'No autorizado para desbloquear este contacto.');
        }

        $nombreAmigo = $contacto->amigo?->name ?? 'Contacto';

        $contacto->update(['bloqueado' => false]);

        return redirect()->route('chat.index')->with('status', "Usuario {$nombreAmigo} desbloqueado.");
    }
}

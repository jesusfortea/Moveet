<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Contacto;
use App\Models\Mensaje;
use App\Models\SolicitudAmistad;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
            'messages' => $mensajes->map(fn (Mensaje $mensaje) => $this->messagePayload($mensaje))->values(),
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

    public function storeContact(Request $request): RedirectResponse
    {
        $user = Auth::user();

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
            return back()
                ->withErrors(['contacto' => 'No se ha encontrado ningún usuario con ese nombre.'])
                ->withInput();
        }

        $yaEsContacto = Contacto::query()
            ->where('user_id', $user->id)
            ->where('contacto_id', $contactoUsuario->id)
            ->exists();

        if ($yaEsContacto) {
            return redirect()
                ->route('chat.index')
                ->with('status', 'Ese usuario ya es tu contacto.');
        }

        $solicitudRecibida = SolicitudAmistad::query()
            ->where('emisor_id', $contactoUsuario->id)
            ->where('receptor_id', $user->id)
            ->first();

        if ($solicitudRecibida && $solicitudRecibida->estado === 'pendiente') {
            return redirect()
                ->route('chat.index')
                ->with('status', 'Ese usuario ya te ha enviado solicitud. Acéptala para empezar a chatear.');
        }

        if ($solicitudRecibida && $solicitudRecibida->estado === 'aceptada') {
            $contactoPrincipal = Contacto::firstOrCreate([
                'user_id' => $user->id,
                'contacto_id' => $contactoUsuario->id,
            ]);

            Contacto::firstOrCreate([
                'user_id' => $contactoUsuario->id,
                'contacto_id' => $user->id,
            ]);

            Chat::firstOrCreate([
                'contacto_id' => $contactoPrincipal->id,
            ]);

            return redirect()
                ->route('chat.index')
                ->with('status', 'Ese usuario ya era tu contacto.');
        }

        $solicitudEnviada = SolicitudAmistad::query()
            ->where('emisor_id', $user->id)
            ->where('receptor_id', $contactoUsuario->id)
            ->first();

        if ($solicitudEnviada) {
            if ($solicitudEnviada->estado === 'pendiente') {
                return redirect()
                    ->route('chat.index')
                    ->with('status', 'Ya enviaste una solicitud a este usuario.');
            }

            if ($solicitudEnviada->estado === 'rechazada') {
                $solicitudEnviada->update([
                    'estado' => 'pendiente',
                ]);

                return redirect()
                    ->route('chat.index')
                    ->with('status', 'Solicitud reenviada correctamente.');
            }

            if ($solicitudEnviada->estado === 'aceptada') {
                $contactoPrincipal = Contacto::firstOrCreate([
                    'user_id' => $user->id,
                    'contacto_id' => $contactoUsuario->id,
                ]);

                Contacto::firstOrCreate([
                    'user_id' => $contactoUsuario->id,
                    'contacto_id' => $user->id,
                ]);

                Chat::firstOrCreate([
                    'contacto_id' => $contactoPrincipal->id,
                ]);

                return redirect()
                    ->route('chat.index')
                    ->with('status', 'Ese usuario ya es tu contacto.');
            }
        }

        try {
            SolicitudAmistad::create([
                'emisor_id' => $user->id,
                'receptor_id' => $contactoUsuario->id,
                'estado' => 'pendiente',
            ]);
        } catch (QueryException $exception) {
            // Handles rare double-submit/race cases guarded by unique(emisor_id, receptor_id)
            if ((int) $exception->getCode() === 23000) {
                return redirect()
                    ->route('chat.index')
                    ->with('status', 'Ya existe una solicitud con ese usuario.');
            }

            throw $exception;
        }

        return redirect()
            ->route('chat.index')
            ->with('status', 'Solicitud enviada correctamente.');
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

            $contactoPrincipal = Contacto::firstOrCreate([
                'user_id' => $solicitud->emisor_id,
                'contacto_id' => $solicitud->receptor_id,
            ]);

            Contacto::firstOrCreate([
                'user_id' => $solicitud->receptor_id,
                'contacto_id' => $solicitud->emisor_id,
            ]);

            Chat::firstOrCreate([
                'contacto_id' => $contactoPrincipal->id,
            ]);
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
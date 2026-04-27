@extends('layouts.plantillaHome')

@section('title', 'Chat · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endpush

@push('scripts')
<script>
    window.chatConfig = {
        currentUserId: {{ $usuario->id }},
        contactId: {{ $contactoSeleccionadoId ?? 'null' }},
        messagesUrl: @json(isset($contactoSeleccionadoId) ? route('chat.messages.index', ['contacto' => $contactoSeleccionadoId]) : null),
        sendUrl: @json(isset($contactoSeleccionadoId) ? route('chat.messages.store', ['contacto' => $contactoSeleccionadoId]) : null),
        lastMessageId: {{ $mensajes->last()?->id ?? 'null' }},
        qrScanUrl: @json(route('chat.qr.scan')),
    };
</script>
<script src="{{ asset('js/chat.js') }}"></script>
<script src="{{ asset('js/invite.js') }}"></script>
@endpush

@section('content')
<div class="chat-page">
    <h1 class="chat-page-title">Chat</h1>

    @if (session('status'))
        <div class="chat-alert">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="chat-alert chat-alert--error">{{ session('error') }}</div>
    @endif

    <div class="chat-layout {{ isset($contactoSeleccionadoId) ? 'chat-layout--mobile-chat' : 'chat-layout--mobile-list' }}">
        <section class="chat-panel">
            <div class="chat-mobile-top">
                <a href="{{ route('chat.index') }}" class="chat-mobile-back">&lt; Chats</a>
                <strong>{{ $contactoSeleccionado['nombre'] ?? 'Mensajes' }}</strong>
            </div>

            <div class="chat-messages" id="chat-messages" data-last-message-id="{{ $mensajes->last()?->id ?? '' }}">
                @forelse ($mensajes as $mensaje)
                    <article class="message-bubble {{ $mensaje->emisor_id === $usuario->id ? 'message-bubble--out' : 'message-bubble--in' }}">
                        <p>{{ $mensaje->contenido }}</p>
                        <span>{{ $mensaje->created_at?->format('H:i') }}</span>
                    </article>
                @empty
                    <div class="chat-empty">
                        <p>Selecciona un contacto para empezar a chatear.</p>
                    </div>
                @endforelse
            </div>

            @php
                $esContactoBloqueado = (bool) ($contactoSeleccionado['bloqueado'] ?? false);
                $bloqueadoPorMi = (bool) ($contactoSeleccionado['bloqueado_por_mi'] ?? false);
                $bloqueadoPorOtro = (bool) ($contactoSeleccionado['bloqueado_por_otro'] ?? false);

                if ($bloqueadoPorMi) {
                    $placeholderComposer = 'Has bloqueado a este contacto. Desbloquéalo para chatear.';
                } elseif ($bloqueadoPorOtro) {
                    $placeholderComposer = 'Este usuario te ha bloqueado. No puedes escribirle.';
                } elseif ($esContactoBloqueado) {
                    $placeholderComposer = 'Contacto bloqueado.';
                } else {
                    $placeholderComposer = 'Escribe algo...';
                }

                $composerHabilitado = isset($contactoSeleccionadoId) && ! $esContactoBloqueado;
            @endphp

            <form class="chat-composer" id="chat-composer" method="POST" action="{{ isset($contactoSeleccionadoId) ? route('chat.messages.store', ['contacto' => $contactoSeleccionadoId]) : '#' }}">
                @csrf
                <input type="text" name="contenido" placeholder="{{ $placeholderComposer }}" {{ $composerHabilitado ? '' : 'disabled' }}>
                <button type="submit" class="chat-send-btn" {{ $composerHabilitado ? '' : 'disabled' }}>➤</button>
            </form>
        </section>

        <aside class="contacts-panel">
            <div class="contacts-panel-header contacts-panel-header--stack">
                <div class="contacts-panel-title-row">
                    <h2>Contactos</h2>
                    <div class="contacts-panel-tools">
                        <button type="button" class="contacts-tool-btn" id="open-my-qr">Mi QR</button>
                        <button type="button" class="contacts-tool-btn contacts-tool-btn--secondary" id="open-scan-qr">Escanear QR</button>
                    </div>
                </div>
            </div>

            @if (($solicitudesRecibidas ?? collect())->isNotEmpty())
                <section class="requests-panel">
                    <h3>Solicitudes pendientes</h3>

                    @foreach ($solicitudesRecibidas as $solicitud)
                        <article class="request-card">
                            <div class="request-avatar">
                                @if ($solicitud->emisor?->ruta_imagen_url)
                                    <img src="{{ $solicitud->emisor->ruta_imagen_url }}" alt="{{ $solicitud->emisor->name }}">
                                @else
                                    <span>{{ strtoupper(substr($solicitud->emisor?->name ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>

                            <div class="request-meta">
                                <strong>{{ $solicitud->emisor?->name ?? 'Usuario' }}</strong>
                                <small>Quiere agregarte a chat</small>
                            </div>

                            <div class="request-actions">
                                <form method="POST" action="{{ route('chat.solicitudes.accept', $solicitud) }}">
                                    @csrf
                                    <button type="submit" class="btn-request btn-request--accept">Aceptar</button>
                                </form>

                                <form method="POST" action="{{ route('chat.solicitudes.reject', $solicitud) }}">
                                    @csrf
                                    <button type="submit" class="btn-request btn-request--reject">Rechazar</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif

            <div class="contacts-list">
                @forelse ($contactos as $contacto)
                    <div class="contact-card-wrapper {{ ($contactoSeleccionadoId ?? null) === $contacto['id'] ? 'active' : '' }}">
                        <a class="contact-card" href="{{ route('chat.index', ['contacto' => $contacto['id']]) }}" {{ $contacto['bloqueado'] ? 'aria-disabled=true' : '' }}>
                            <div class="contact-avatar {{ $contacto['bloqueado'] ? 'contact-avatar--bloqueado' : '' }}">
                                @if ($contacto['avatar'])
                                    <img src="{{ $contacto['avatar'] }}" alt="{{ $contacto['nombre'] }}">
                                @else
                                    <span>{{ strtoupper(substr($contacto['nombre'], 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="contact-meta">
                                <strong>
                                    {{ $contacto['nombre'] }}
                                    @if ($contacto['bloqueado'])
                                        <span class="contact-blocked-badge">🚫</span>
                                    @endif
                                </strong>
                                <small>
                                    @if ($contacto['bloqueado'])
                                        {{ ($contacto['bloqueado_por_mi'] ?? false) ? 'Bloqueado por ti' : 'Bloqueado por el otro usuario' }}
                                    @else
                                        {{ $contacto['ultimo_mensaje'] ?? 'Sin mensajes todavía' }}
                                    @endif
                                </small>
                            </div>
                        </a>

                        <div class="contact-actions">
                            @if ($contacto['bloqueado_por_mi'] ?? false)
                                <form method="POST" action="{{ route('chat.contactos.unblock', $contacto['model']) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="contact-action-btn contact-action-btn--unblock" title="Desbloquear">🔓</button>
                                </form>
                            @elseif (!($contacto['bloqueado_por_otro'] ?? false))
                                <form method="POST" action="{{ route('chat.contactos.block', $contacto['model']) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="contact-action-btn contact-action-btn--block" title="Bloquear">🚫</button>
                                </form>
                            @else
                                <span class="contact-action-btn contact-action-btn--blocked" title="Bloqueado por el otro usuario">🔒</span>
                            @endif

                            <form method="POST" action="{{ route('chat.contactos.destroy', $contacto['model']) }}" style="margin: 0;" data-swal-confirm data-swal-confirm-title="Eliminar contacto" data-swal-confirm-message="¿Seguro que quieres eliminar este contacto?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="contact-action-btn contact-action-btn--delete" title="Eliminar">🗑️</button>
                            </form>

                            <form method="POST" action="{{ route('reportes.usuarios.store', $contacto['contacto_id']) }}" style="margin: 0;" data-swal-confirm data-swal-confirm-title="Enviar reporte" data-swal-confirm-message="¿Enviar reporte de este perfil al administrador?">
                                @csrf
                                <input type="hidden" name="reason" value="conducta_inapropiada">
                                <input type="hidden" name="details" value="Reporte enviado desde chat.">
                                <button type="submit" class="contact-action-btn contact-action-btn--delete" title="Reportar">⚠️</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="contacts-empty">Todavía no tienes contactos.</p>
                @endforelse
            </div>

            <details class="add-contact-drawer">
                <summary class="add-contact-fab" aria-label="Agregar contacto">+</summary>

                <form class="add-contact-card" id="add-contact-form" method="POST" action="{{ route('chat.contactos.store') }}">
                    @csrf
                    <h3>Agregar contacto</h3>

                    @if ($errors->has('contacto'))
                        <p class="add-contact-error">{{ $errors->first('contacto') }}</p>
                    @endif

                    <input type="text" name="contacto" id="contacto-input" value="{{ old('contacto') }}" placeholder="Nombre o email..." autocomplete="off">
                    <button type="submit">Enviar solicitud</button>
                    <p class="invite-inline-message" id="add-contact-feedback" hidden></p>
                </form>
            </details>
        </aside>
    </div>

    <dialog class="qr-modal" id="my-qr-modal">
        <div class="qr-modal-content">
            <button class="qr-modal-close" id="close-my-qr-modal" aria-label="Cerrar">✕</button>
            <h2>Tu QR para añadir amigos</h2>
            <p class="invite-message">Escanéalo desde otro móvil o comparte el enlace directo.</p>
            <div class="qr-container" id="qr-container">{!! $personalQr['svg'] !!}</div>
            <div class="invite-code-display">
                <p>Código de invitación:</p>
                <div class="code-box">
                    <span id="invite-code" data-link="{{ $personalQr['url'] }}">{{ $personalQr['code'] }}</span>
                    <button type="button" class="copy-code-btn" id="copy-code-btn">Copiar</button>
                </div>
            </div>
            <div class="invite-actions">
                <button type="button" class="qr-modal-action" id="copy-qr-link">Copiar enlace</button>
                <button type="button" class="qr-modal-action secondary" id="close-my-qr-btn">Cerrar</button>
            </div>
        </div>
    </dialog>

    <dialog class="qr-modal qr-modal--scan" id="scan-qr-modal">
        <div class="qr-modal-content qr-modal-content--scan">
            <button class="qr-modal-close" id="close-scan-qr-modal" aria-label="Cerrar">✕</button>
            <h2>Escanear QR</h2>
            <p class="invite-message">Apunta la cámara al QR de otro usuario de Moveet.</p>

            <div class="scanner-shell">
                <video id="qr-video" class="qr-video" playsinline muted></video>
                <div class="scanner-placeholder" id="scanner-placeholder">Activa la cámara para escanear</div>
            </div>

            <p class="scanner-status" id="scanner-status">Preparado para escanear.</p>

            <div class="invite-actions invite-actions--scan">
                <button type="button" class="qr-modal-action" id="start-scan-btn">Activar cámara</button>
                <button type="button" class="qr-modal-action secondary" id="stop-scan-btn">Detener</button>
            </div>

            <form class="scan-manual-form" id="scan-manual-form">
                <label for="scan-manual-input">Pegar enlace o código</label>
                <input type="text" id="scan-manual-input" name="qr_value" placeholder="Pega aquí el enlace o el código QR">
                <button type="submit" class="qr-modal-action">Usar código</button>
            </form>
        </div>
    </dialog>
</div>
@endsection

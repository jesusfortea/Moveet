@extends('layouts.admin')

@section('title', 'Resenas de usuarios - Admin')

@push('styles')
<style>
    .reviews-admin-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .reviews-admin-tab {
        border: 1px solid #c5d8d6;
        background: white;
        color: #516260;
        border-radius: 999px;
        padding: 10px 16px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        font-family: 'Nunito', sans-serif;
        transition: all 0.2s ease;
    }

    .reviews-admin-tab.active {
        background: #8FA8A6;
        border-color: #8FA8A6;
        color: white;
    }

    .reviews-admin-hidden {
        display: none;
    }

    .reviews-admin-list {
        display: grid;
        gap: 16px;
    }

    .reviews-admin-card {
        background: white;
        border: 1px solid #d8e3e0;
        border-left: 4px solid #8FA8A6;
        border-radius: 10px;
        padding: 20px;
    }

    .reviews-admin-card--pending {
        border-left-color: #d4a017;
    }

    .reviews-admin-card--published {
        border-left-color: #3b8f6b;
    }

    .reviews-admin-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
    }

    .reviews-admin-card-meta {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 8px;
        font-size: 12px;
        color: #6c7f7c;
    }

    .reviews-admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .reviews-admin-badge--pending {
        background: #fff4db;
        color: #9a6700;
    }

    .reviews-admin-badge--published {
        background: #dff4e7;
        color: #17603f;
    }

    .reviews-admin-copy {
        background: #f7f9f8;
        border: 1px solid #e2ebea;
        border-radius: 8px;
        padding: 14px;
        color: #1E2A28;
        line-height: 1.5;
        margin-bottom: 14px;
    }

    .reviews-admin-response {
        background: #edf7f1;
        border: 1px solid #cfe7d8;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .reviews-admin-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .reviews-admin-actions > * {
        flex: 1 1 180px;
    }

    .reviews-admin-link,
    .reviews-admin-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: 'Nunito', sans-serif;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .reviews-admin-link {
        background: #8FA8A6;
        color: white;
    }

    .reviews-admin-link:hover {
        background: #7a9a98;
    }

    .reviews-admin-delete {
        background: #f8e0e0;
        color: #8e2323;
    }

    .reviews-admin-delete:hover {
        background: #efcaca;
    }

    .reviews-admin-empty {
        background: white;
        border: 1px dashed #d8e3e0;
        border-radius: 10px;
        padding: 40px 24px;
        text-align: center;
        color: #6c7f7c;
    }

    @media (max-width: 640px) {
        .reviews-admin-card {
            padding: 16px;
        }

        .reviews-admin-card-top {
            flex-direction: column;
        }

        .reviews-admin-badge {
            white-space: normal;
        }

        .reviews-admin-tab {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div style="padding: 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0; color: #1E2A28;">RESENAS DE USUARIOS</h1>
            <p style="margin: 8px 0 0; color: #516260; font-size: 14px;">Revisa, publica y modera las opiniones enviadas por los usuarios.</p>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            {{ $message }}
        </div>
    @endif

    <div class="reviews-admin-tabs">
        <button type="button" class="reviews-admin-tab active" data-tab="pendientes">
            <i class="fas fa-clock"></i>
            En revision ({{ $pendientes->total() }})
        </button>
        <button type="button" class="reviews-admin-tab" data-tab="respondidas">
            <i class="fas fa-check-circle"></i>
            Publicadas ({{ $respondidas->total() }})
        </button>
    </div>

    <div id="pendientes" class="reviews-admin-panel">
        @if ($pendientes->count() > 0)
            <div class="reviews-admin-list">
                @foreach ($pendientes as $pregunta)
                    <article class="reviews-admin-card reviews-admin-card--pending">
                        <div class="reviews-admin-card-top">
                            <div>
                                <h3 style="margin: 0; font-size: 1.15rem; color: #1E2A28;">{{ $pregunta->titulo }}</h3>
                                <div class="reviews-admin-card-meta">
                                    <span><i class="fas fa-user-circle"></i> {{ $pregunta->usuario->name }}</span>
                                    <span><i class="fas fa-calendar-alt"></i> {{ $pregunta->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="reviews-admin-badge reviews-admin-badge--pending">
                                <i class="fas fa-clock"></i>
                                En revision
                            </span>
                        </div>

                        <div class="reviews-admin-copy">
                            {{ $pregunta->contenido }}
                        </div>

                        <div class="reviews-admin-actions">
                            <a href="{{ route('preguntas.show', $pregunta) }}" class="reviews-admin-link">
                                <i class="fas fa-eye"></i>
                                Revisar
                            </a>
                            <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" onsubmit="return confirm('Estas seguro de que deseas eliminar esta resena?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="reviews-admin-delete">
                                    <i class="fas fa-trash"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($pendientes->hasPages())
                <div style="margin-top: 20px;">
                    {{ $pendientes->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="reviews-admin-empty">
                <div style="font-size: 42px; margin-bottom: 10px; color: #8FA8A6;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p style="margin: 0; font-size: 16px; font-weight: 700; color: #1E2A28;">No hay resenas pendientes.</p>
                <p style="margin: 8px 0 0;">Todo esta al dia.</p>
            </div>
        @endif
    </div>

    <div id="respondidas" class="reviews-admin-panel reviews-admin-hidden">
        @if ($respondidas->count() > 0)
            <div class="reviews-admin-list">
                @foreach ($respondidas as $pregunta)
                    <article class="reviews-admin-card reviews-admin-card--published">
                        <div class="reviews-admin-card-top">
                            <div>
                                <h3 style="margin: 0; font-size: 1.15rem; color: #1E2A28;">{{ $pregunta->titulo }}</h3>
                                <div class="reviews-admin-card-meta">
                                    <span><i class="fas fa-user-circle"></i> {{ $pregunta->usuario->name }}</span>
                                    <span><i class="fas fa-calendar-alt"></i> {{ $pregunta->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="reviews-admin-badge reviews-admin-badge--published">
                                <i class="fas fa-check-circle"></i>
                                Publicada
                            </span>
                        </div>

                        <div style="margin-bottom: 10px; font-size: 12px; font-weight: 700; color: #516260;">Resena</div>
                        <div class="reviews-admin-copy">
                            {{ Str::limit($pregunta->contenido, 200) }}
                        </div>

                        @if (filled($pregunta->respuesta))
                            <div style="margin-bottom: 10px; font-size: 12px; font-weight: 700; color: #17603f;">Respuesta del equipo</div>
                            <div class="reviews-admin-response">
                                <p style="margin: 0 0 8px; color: #1E2A28; line-height: 1.5;">{{ Str::limit($pregunta->respuesta, 200) }}</p>
                                <p style="margin: 0; font-size: 12px; color: #516260;">
                                    <i class="fas fa-user-shield"></i>
                                    Publicada por: <strong>{{ $pregunta->respondidaPor->name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        @endif

                        <div class="reviews-admin-actions">
                            <a href="{{ route('preguntas.show', $pregunta) }}" class="reviews-admin-link">
                                <i class="fas fa-eye"></i>
                                Ver detalle
                            </a>
                            <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" onsubmit="return confirm('Estas seguro de que deseas eliminar esta resena?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="reviews-admin-delete">
                                    <i class="fas fa-trash"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($respondidas->hasPages())
                <div style="margin-top: 20px;">
                    {{ $respondidas->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="reviews-admin-empty">
                <div style="font-size: 42px; margin-bottom: 10px; color: #b6c5c2;">
                    <i class="fas fa-inbox"></i>
                </div>
                <p style="margin: 0; font-size: 16px; font-weight: 700; color: #1E2A28;">No hay resenas publicadas todavia.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.reviews-admin-tab').forEach(function (button) {
        button.addEventListener('click', function () {
            const tabName = this.dataset.tab;

            document.querySelectorAll('.reviews-admin-panel').forEach(function (panel) {
                panel.classList.add('reviews-admin-hidden');
            });

            document.querySelectorAll('.reviews-admin-tab').forEach(function (tab) {
                tab.classList.remove('active');
            });

            document.getElementById(tabName).classList.remove('reviews-admin-hidden');
            this.classList.add('active');
        });
    });
</script>
@endsection

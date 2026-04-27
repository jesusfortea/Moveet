@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Panel de reseñas</h1>
        <p class="text-gray-600">Revisa, publica y modera las opiniones de los usuarios.</p>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
            <p class="text-green-800">{{ $message }}</p>
        </div>
    @endif

    <div class="mb-6">
        <div class="flex gap-4 border-b border-gray-200">
            <button class="tab-button px-4 py-2 font-semibold border-b-2 transition active" data-tab="pendientes">
                <i class="fas fa-clock mr-2"></i>
                En revision ({{ $pendientes->total() }})
            </button>
            <button class="tab-button px-4 py-2 font-semibold border-b-2 transition text-gray-600 border-transparent hover:text-gray-900" data-tab="respondidas">
                <i class="fas fa-check-circle mr-2"></i>
                Publicadas ({{ $respondidas->total() }})
            </button>
        </div>
    </div>

    <div id="pendientes" class="tab-content">
        @if ($pendientes->count() > 0)
            <div class="space-y-4">
                @foreach ($pendientes as $pregunta)
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4 gap-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $pregunta->titulo }}</h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mt-2">
                                    <span><i class="fas fa-user-circle mr-1"></i>{{ $pregunta->usuario->name }}</span>
                                    <span><i class="fas fa-calendar-alt mr-1"></i>{{ $pregunta->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                En revision
                            </span>
                        </div>

                        <p class="text-gray-700 mb-4 bg-gray-50 p-4 rounded border border-gray-200">
                            {{ $pregunta->contenido }}
                        </p>

                        <div class="flex gap-2">
                            <a href="{{ route('preguntas.show', $pregunta) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-center">
                                <i class="fas fa-eye mr-2"></i>
                                Revisar
                            </a>
                            <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" class="inline-block" onsubmit="return confirm('Estas seguro de que deseas eliminar esta resena?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition">
                                    <i class="fas fa-trash mr-2"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($pendientes->hasPages())
                <div class="mt-8">
                    {{ $pendientes->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-check-circle text-5xl text-green-300 mb-4"></i>
                <p class="text-gray-600 text-lg">No hay reseñas pendientes.</p>
                <p class="text-gray-500 mt-2">Todo esta al dia.</p>
            </div>
        @endif
    </div>

    <div id="respondidas" class="tab-content hidden">
        @if ($respondidas->count() > 0)
            <div class="space-y-4">
                @foreach ($respondidas as $pregunta)
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4 gap-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $pregunta->titulo }}</h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mt-2">
                                    <span><i class="fas fa-user-circle mr-1"></i>{{ $pregunta->usuario->name }}</span>
                                    <span><i class="fas fa-calendar-alt mr-1"></i>{{ $pregunta->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Publicada
                            </span>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Resena:</h4>
                            <p class="text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 text-sm">
                                {{ Str::limit($pregunta->contenido, 200) }}
                            </p>
                        </div>

                        @if (filled($pregunta->respuesta))
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-green-700 mb-2">Respuesta del equipo:</h4>
                                <p class="text-gray-700 bg-green-50 p-3 rounded border border-green-200 text-sm">
                                    {{ Str::limit($pregunta->respuesta, 200) }}
                                </p>
                                <p class="text-xs text-gray-600 mt-2">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Publicada por: <strong>{{ $pregunta->respondidaPor->name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <a href="{{ route('preguntas.show', $pregunta) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-center text-sm">
                                <i class="fas fa-eye mr-2"></i>
                                Ver detalle
                            </a>
                            <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" class="inline-block" onsubmit="return confirm('Estas seguro de que deseas eliminar esta resena?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition text-sm">
                                    <i class="fas fa-trash mr-2"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($respondidas->hasPages())
                <div class="mt-8">
                    {{ $respondidas->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 text-lg">No hay reseñas publicadas todavía.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .tab-button {
        border-bottom: 3px solid transparent;
    }

    .tab-button.active {
        @apply border-blue-500 text-blue-600;
    }

    .tab-content.hidden {
        display: none;
    }
</style>

<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'text-blue-600', 'border-blue-500');
                btn.classList.add('text-gray-600', 'border-transparent', 'hover:text-gray-900');
            });

            document.getElementById(tabName).classList.remove('hidden');

            this.classList.add('active', 'text-blue-600', 'border-blue-500');
            this.classList.remove('text-gray-600', 'border-transparent', 'hover:text-gray-900');
        });
    });
</script>
@endsection

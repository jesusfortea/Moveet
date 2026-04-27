@extends('layouts.plantillaHome')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('preguntas.index') }}" class="text-green-600 hover:text-green-700 mb-6 inline-block">← Volver</a>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ $message }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow mb-6 border-l-4 border-yellow-500">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $pregunta->titulo }}</h1>
                <p class="text-gray-600">Por {{ $pregunta->usuario->name }} • {{ $pregunta->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="text-sm px-3 py-1 rounded-full {{ $pregunta->estado === 'respondida' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $pregunta->estado === 'respondida' ? '✓ Respondida' : '⏳ Pendiente' }}
            </span>
        </div>

        <p class="text-gray-700 bg-gray-50 p-4 rounded border border-gray-200 mb-4">{{ $pregunta->contenido }}</p>

        @auth
            @if (auth()->id() === $pregunta->user_id)
                <div class="flex gap-2 pt-4 border-t">
                    <a href="{{ route('preguntas.edit', $pregunta) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                        Editar
                    </a>
                    <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm">
                            Eliminar
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    @if ($pregunta->estaRespondida())
        <div class="bg-green-50 p-6 rounded-lg shadow border-l-4 border-green-500">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Respuesta</h2>
            <p class="text-gray-700 bg-white p-4 rounded border border-green-200 mb-4">{{ $pregunta->respuesta }}</p>
            <p class="text-sm text-gray-600">
                Respondido por {{ $pregunta->respondidaPor->name ?? 'Admin' }} el {{ $pregunta->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>
    @else
        @auth
            @if (auth()->user()->is_admin)
                <div class="bg-blue-50 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Responder Pregunta</h2>
                    <form action="{{ route('preguntas.responder', $pregunta) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="respuesta" class="block text-sm font-medium text-gray-700 mb-2">Tu respuesta</label>
                            <textarea name="respuesta" id="respuesta" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                      placeholder="Escribe tu respuesta aquí..." required minlength="10" maxlength="3000">{{ old('respuesta') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Mínimo 10 caracteres, máximo 3000</p>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                            Publicar Respuesta
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    @endif
</div>
@endsection

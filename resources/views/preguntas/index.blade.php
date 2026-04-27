@extends('layouts.plantillaHome')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-2">Preguntas Frecuentes</h1>
    <p class="text-gray-600 mb-8">Encuentra respuestas a preguntas comunes</p>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ $message }}
        </div>
    @endif

    @auth
        <div class="mb-8">
            <a href="{{ route('preguntas.create') }}" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                + Hacer una Pregunta
            </a>
        </div>
    @else
        <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-blue-800">
                <a href="{{ route('login') }}" class="font-semibold hover:underline">Inicia sesión</a> para hacer tus propias preguntas
            </p>
        </div>
    @endauth

    <div class="space-y-4">
        @forelse ($preguntas as $pregunta)
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition border-l-4 {{ $pregunta->estado === 'respondida' ? 'border-green-500' : 'border-yellow-500' }}">
                <a href="{{ route('preguntas.show', $pregunta) }}" class="block">
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="text-xl font-semibold text-gray-900 hover:text-green-600">{{ $pregunta->titulo }}</h2>
                        <span class="text-sm px-3 py-1 rounded-full {{ $pregunta->estado === 'respondida' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $pregunta->estado === 'respondida' ? '✓ Respondida' : '⏳ Pendiente' }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-2 line-clamp-2">{{ $pregunta->contenido }}</p>
                    <p class="text-sm text-gray-500">Por {{ $pregunta->usuario->name }} • {{ $pregunta->created_at->diffForHumans() }}</p>
                </a>
            </div>
        @empty
            <div class="bg-white p-12 rounded-lg shadow text-center">
                <p class="text-gray-600">No hay preguntas aún</p>
            </div>
        @endforelse
    </div>

    @if ($preguntas->hasPages())
        <div class="mt-8">
            {{ $preguntas->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection

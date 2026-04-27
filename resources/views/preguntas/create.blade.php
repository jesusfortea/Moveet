@extends('layouts.plantillaHome')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-6">Hacer una Pregunta</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Error:</strong>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('preguntas.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        
        <div class="mb-4">
            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
            <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                   placeholder="¿Cuál es tu pregunta?"
                   required maxlength="255">
        </div>

        <div class="mb-4">
            <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">Pregunta</label>
            <textarea name="contenido" id="contenido" rows="6"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                      placeholder="Escribe tu pregunta aquí..."
                      required minlength="10" maxlength="2000">{{ old('contenido') }}</textarea>
            <p class="text-sm text-gray-500 mt-1">Mínimo 10 caracteres, máximo 2000</p>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                Enviar Pregunta
            </button>
            <a href="{{ route('preguntas.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

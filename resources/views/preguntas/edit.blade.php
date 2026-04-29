@extends('layouts.plantillaHome')

@section('title', 'Editar rese&ntilde;a · Moveet')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('preguntas.show', $pregunta) }}" class="inline-flex items-center text-sm font-semibold text-[#6B8F8D] transition hover:text-[#1E2A28]">
                &larr; Volver a la rese&ntilde;a
            </a>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-[#d5e0df] md:p-8">
            <h1 class="text-3xl font-bold text-[#1E2A28]">Editar rese&ntilde;a</h1>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <strong class="block text-sm font-semibold">Revisa estos campos</strong>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('preguntas.update', $pregunta) }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="titulo" class="mb-2 block text-sm font-semibold text-gray-700">T&iacute;tulo</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $pregunta->titulo) }}" class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200" required maxlength="255">
                </div>

                <div>
                    <label for="contenido" class="mb-2 block text-sm font-semibold text-gray-700">Tu rese&ntilde;a</label>
                    <textarea name="contenido" id="contenido" rows="8" class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200" required minlength="10" maxlength="2000">{{ old('contenido', $pregunta->contenido) }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">M&iacute;nimo 10 caracteres, m&aacute;ximo 2000.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                        Guardar cambios
                    </button>
                    <a href="{{ route('preguntas.show', $pregunta) }}" class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        @if ($pregunta->estaRespondida())
            <div class="mt-6 rounded-2xl border border-yellow-200 bg-yellow-50 px-4 py-4 text-sm text-yellow-800">
                Esta rese&ntilde;a ya est&aacute; publicada. Si necesitas un cambio importante, habla con el administrador.
            </div>
        @endif
    </div>
</div>
@endsection

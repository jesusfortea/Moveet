@extends('layouts.plantillaHome')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-6 lg:grid-cols-[1.05fr_minmax(0,1.55fr)] lg:items-start">
            <section class="rounded-2xl border border-[#d5e0df] bg-gradient-to-br from-[#f7fbfa] via-[#eef4f2] to-[#e5efed] p-6 shadow-sm md:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#6B8F8D]">Reseñas</p>
                <h1 class="mt-3 text-3xl font-bold text-[#1E2A28] md:text-4xl">Escribe tu reseña</h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-[#58706e]">
                    Comparte tu experiencia con claridad para que otras personas entiendan rapido como se vive Moveet desde dentro.
                </p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60">
                        <p class="text-sm font-semibold text-[#1E2A28]">Titulo claro</p>
                        <p class="mt-1 text-sm text-[#58706e]">Resume en una frase lo que quieres destacar.</p>
                    </div>
                    <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60">
                        <p class="text-sm font-semibold text-[#1E2A28]">Opinion util</p>
                        <p class="mt-1 text-sm text-[#58706e]">Anade detalles concretos para que tu reseña tenga valor.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-[#d5e0df] md:p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                        <strong class="block text-sm font-semibold">Revisa estos campos</strong>
                        <ul class="mt-2 list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('preguntas.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="titulo" class="mb-2 block text-sm font-semibold text-gray-700">Titulo</label>
                        <input
                            type="text"
                            name="titulo"
                            id="titulo"
                            value="{{ old('titulo') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200"
                            placeholder="Resume tu experiencia"
                            required
                            maxlength="255"
                        >
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="contenido" class="block text-sm font-semibold text-gray-700">Tu reseña</label>
                            <p class="text-xs text-gray-500">Minimo 10 caracteres</p>
                        </div>
                        <textarea
                            name="contenido"
                            id="contenido"
                            rows="10"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200"
                            placeholder="Cuenta que te ha gustado, que mejorarias o como ha sido tu experiencia..."
                            required
                            minlength="10"
                            maxlength="2000"
                        >{{ old('contenido') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Maximo 2000 caracteres.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                            Enviar reseña
                        </button>
                        <a href="{{ route('preguntas.index') }}" class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

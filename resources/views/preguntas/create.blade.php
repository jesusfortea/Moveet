@extends('layouts.plantillaHome')

@section('title', 'Crear rese&ntilde;a · Moveet')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a href="{{ route('preguntas.index') }}" class="inline-flex items-center text-sm font-semibold text-[#6B8F8D] transition hover:text-[#1E2A28]">
                &larr; Volver a rese&ntilde;as
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.05fr_minmax(0,1.55fr)] lg:items-start">
            <section class="rounded-2xl border border-[#d5e0df] bg-gradient-to-br from-[#f7fbfa] via-[#eef4f2] to-[#e5efed] p-6 shadow-sm md:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#6B8F8D]">Rese&ntilde;as</p>
                <h1 class="mt-3 text-3xl font-bold text-[#1E2A28] md:text-4xl">Escribe tu rese&ntilde;a</h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-[#58706e]">
                    Comparte tu experiencia con claridad para que otras personas entiendan r&aacute;pido c&oacute;mo se vive Moveet desde dentro.
                </p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60">
                        <p class="text-sm font-semibold text-[#1E2A28]">T&iacute;tulo claro</p>
                        <p class="mt-1 text-sm text-[#58706e]">Resume en una frase lo que quieres destacar.</p>
                    </div>
                    <div class="rounded-2xl bg-white/70 p-4 shadow-sm ring-1 ring-white/60">
                        <p class="text-sm font-semibold text-[#1E2A28]">Opini&oacute;n &uacute;til</p>
                        <p class="mt-1 text-sm text-[#58706e]">A&ntilde;ade detalles concretos para que tu rese&ntilde;a tenga valor.</p>
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
                        <label for="titulo" class="mb-2 block text-sm font-semibold text-gray-700">T&iacute;tulo</label>
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
                            <label for="contenido" class="block text-sm font-semibold text-gray-700">Tu rese&ntilde;a</label>
                            <p class="text-xs text-gray-500">M&iacute;nimo 10 caracteres</p>
                        </div>
                        <textarea
                            name="contenido"
                            id="contenido"
                            rows="10"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200"
                            placeholder="Cuenta qu&eacute; te ha gustado, qu&eacute; mejorar&iacute;as o c&oacute;mo ha sido tu experiencia..."
                            required
                            minlength="10"
                            maxlength="2000"
                        >{{ old('contenido') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">M&aacute;ximo 2000 caracteres.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                            Enviar rese&ntilde;a
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

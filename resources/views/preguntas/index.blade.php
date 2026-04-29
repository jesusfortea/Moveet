@extends('layouts.plantillaHome')

@section('title', 'Rese&ntilde;as · Moveet')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-5xl">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-[#1E2A28] md:text-4xl">Rese&ntilde;as de la comunidad</h1>
                <p class="mt-2 text-[#58706e]">Lee opiniones reales y comparte tu experiencia con Moveet.</p>
            </div>

            @auth
                <a href="{{ route('preguntas.create') }}" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                    Escribir una rese&ntilde;a
                </a>
            @endauth
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ $message }}
            </div>
        @endif

        @guest
            <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-blue-800">
                <a href="{{ route('login') }}" class="font-semibold hover:underline">Inicia sesi&oacute;n</a> para compartir tu propia rese&ntilde;a.
            </div>
        @endguest

        <div class="space-y-4">
            @forelse ($preguntas as $pregunta)
                <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-[#d5e0df] transition hover:shadow-md">
                    <a href="{{ route('preguntas.show', $pregunta) }}" class="block">
                        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-[#1E2A28] transition hover:text-green-600">{{ $pregunta->titulo }}</h2>
                                <p class="mt-1 text-sm text-[#58706e]">
                                    Por {{ $pregunta->usuario->name }} &middot; {{ $pregunta->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold whitespace-nowrap {{ $pregunta->estado === 'respondida' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $pregunta->estado === 'respondida' ? 'Publicada' : 'En revisi&oacute;n' }}
                            </span>
                        </div>

                        <p class="text-[#58706e]">{{ $pregunta->contenido }}</p>
                    </a>
                </article>
            @empty
                <div class="rounded-2xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-[#d5e0df]">
                    <p class="text-[#58706e]">Todav&iacute;a no hay rese&ntilde;as publicadas.</p>
                </div>
            @endforelse
        </div>

        @if ($preguntas->hasPages())
            <div class="mt-8">
                {{ $preguntas->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

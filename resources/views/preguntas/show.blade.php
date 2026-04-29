@extends('layouts.plantillaHome')

@section('title', 'Detalle de rese&ntilde;a · Moveet')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('preguntas.index') }}" class="inline-flex items-center text-sm font-semibold text-[#6B8F8D] transition hover:text-[#1E2A28]">
                &larr; Volver a rese&ntilde;as
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ $message }}
            </div>
        @endif

        <article class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-[#d5e0df]">
            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h1 class="text-3xl font-bold text-[#1E2A28]">{{ $pregunta->titulo }}</h1>
                    <p class="mt-2 text-sm text-[#58706e]">
                        Por {{ $pregunta->usuario->name }} &middot; {{ $pregunta->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold whitespace-nowrap {{ $pregunta->estado === 'respondida' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $pregunta->estado === 'respondida' ? 'Publicada' : 'En revisi&oacute;n' }}
                </span>
            </div>

            <div class="rounded-2xl border border-[#e3ecea] bg-[#f8fbfa] p-4 text-[#1E2A28] whitespace-pre-line">{{ $pregunta->contenido }}</div>

            @auth
                @if (auth()->id() === $pregunta->user_id)
                    <div class="mt-5 flex flex-col gap-3 border-t border-[#e3ecea] pt-5 sm:flex-row">
                        <a href="{{ route('preguntas.edit', $pregunta) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Editar rese&ntilde;a
                        </a>
                        <form action="{{ route('preguntas.destroy', $pregunta) }}" method="POST" data-swal-confirm data-swal-confirm-title="Eliminar rese&ntilde;a" data-swal-confirm-message="&iquest;Seguro que quieres eliminar esta rese&ntilde;a?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-red-100 px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-200 sm:w-auto">
                                Eliminar
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </article>

        @if ($pregunta->estaRespondida() && filled($pregunta->respuesta))
            <section class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-sm">
                <h2 class="text-2xl font-bold text-[#1E2A28]">Respuesta del equipo</h2>
                <p class="mt-4 rounded-2xl border border-green-200 bg-white p-4 text-[#1E2A28] whitespace-pre-line">{{ $pregunta->respuesta }}</p>
                <p class="mt-4 text-sm text-[#58706e]">
                    Publicada por {{ $pregunta->respondidaPor->name ?? 'Admin' }} el {{ $pregunta->updated_at->format('d/m/Y H:i') }}
                </p>
            </section>
        @elseif(auth()->check() && auth()->user()->is_admin)
            <section class="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                <h2 class="text-2xl font-bold text-[#1E2A28]">Revisar y publicar</h2>
                <form action="{{ route('preguntas.responder', $pregunta) }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="respuesta" class="mb-2 block text-sm font-semibold text-gray-700">Comentario del equipo</label>
                        <textarea name="respuesta" id="respuesta" rows="6" class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200" placeholder="A&ntilde;ade una nota o respuesta visible para esta rese&ntilde;a..." required minlength="10" maxlength="3000">{{ old('respuesta') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">M&iacute;nimo 10 caracteres, m&aacute;ximo 3000.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                        Publicar rese&ntilde;a
                    </button>
                </form>
            </section>
        @endif
    </div>
</div>
@endsection

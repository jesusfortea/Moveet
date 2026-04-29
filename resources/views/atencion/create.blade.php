@extends('layouts.plantillaLanding')

@section('title', 'Atenci&oacute;n al usuario &middot; Moveet')

@section('content')
<div class="w-full px-4 py-8 md:px-6 md:py-10">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a href="{{ auth()->check() ? route('home') : url('/') }}" class="inline-flex items-center text-sm font-semibold text-[#6B8F8D] transition hover:text-[#1E2A28]">
                &larr; Volver a Moveet
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.05fr_minmax(0,1.35fr)] lg:items-start">
            <section class="rounded-2xl border border-[#d5e0df] bg-gradient-to-br from-[#8FA8A6] via-[#7f9d9a] to-[#6d8b88] p-6 text-white shadow-lg md:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/80">Atenci&oacute;n al usuario</p>
                <h1 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">Cu&eacute;ntanos tu duda y te respondemos por correo.</h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-white/90">
                    Si tienes una pregunta, un problema o necesitas ayuda con tu cuenta, escr&iacute;benos aqu&iacute;.
                    El mensaje llegar&aacute; a <strong>moveetrun@gmail.com</strong> y podremos responderte directamente.
                </p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-2xl bg-white/10 p-4 shadow-sm ring-1 ring-white/15">
                        <p class="text-sm font-semibold">Explica el problema con detalle</p>
                        <p class="mt-1 text-sm text-white/80">Cuanto m&aacute;s contexto nos des, m&aacute;s r&aacute;pido podremos ayudarte.</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 shadow-sm ring-1 ring-white/15">
                        <p class="text-sm font-semibold">{{ $isAuthenticated ? 'Usaremos tu correo de cuenta' : 'Revisa bien tu correo' }}</p>
                        <p class="mt-1 text-sm text-white/80">
                            {{ $isAuthenticated ? 'Te responderemos directamente al email vinculado a tu perfil.' : 'As&iacute; podremos contestarte sin perder el hilo de la consulta.' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 shadow-sm ring-1 ring-white/15 sm:col-span-2 lg:col-span-1">
                        <p class="text-sm font-semibold">Respuesta del equipo Moveet</p>
                        <p class="mt-1 text-sm text-white/80">Revisaremos tu mensaje lo antes posible.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-[#d5e0df] md:p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <strong class="block font-semibold">Revisa estos campos</strong>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('atencion.store') }}" method="POST" class="space-y-5">
                    @csrf

                    @if ($isAuthenticated)
                        <div class="rounded-2xl border border-[#d5e0df] bg-[#f6faf9] px-4 py-3 text-sm text-[#58706e]">
                            Enviaremos la consulta usando tu cuenta de <strong>{{ $defaultEmail }}</strong>.
                        </div>
                    @else
                        <div>
                            <label for="nombre" class="mb-2 block text-sm font-semibold text-gray-700">Nombre</label>
                            <input
                                id="nombre"
                                name="nombre"
                                type="text"
                                value="{{ old('nombre', $defaultName) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-[#6B8F8D] focus:ring-2 focus:ring-[#d5e0df]"
                                required
                                maxlength="120"
                            >
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Correo electr&oacute;nico</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $defaultEmail) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-[#6B8F8D] focus:ring-2 focus:ring-[#d5e0df]"
                                required
                                maxlength="255"
                            >
                        </div>
                    @endif

                    <div>
                        <label for="asunto" class="mb-2 block text-sm font-semibold text-gray-700">Asunto</label>
                        <input
                            id="asunto"
                            name="asunto"
                            type="text"
                            value="{{ old('asunto') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-[#6B8F8D] focus:ring-2 focus:ring-[#d5e0df]"
                            required
                            maxlength="150"
                            placeholder="Ejemplo: problema con mi cuenta"
                        >
                    </div>

                    <div>
                        <label for="mensaje" class="mb-2 block text-sm font-semibold text-gray-700">Tu duda o consulta</label>
                        <textarea
                            id="mensaje"
                            name="mensaje"
                            rows="8"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-[#6B8F8D] focus:ring-2 focus:ring-[#d5e0df]"
                            required
                            minlength="10"
                            maxlength="4000"
                            placeholder="Cu&eacute;ntanos qu&eacute; ocurre y te ayudamos."
                        >{{ old('mensaje') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-[#8FA8A6] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7a9a98]"
                        >
                            Enviar consulta
                        </button>
                        <a
                            href="{{ auth()->check() ? route('home') : url('/') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-300"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

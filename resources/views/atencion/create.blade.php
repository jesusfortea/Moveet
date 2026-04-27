<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atenci&oacute;n al usuario - Moveet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#eef4f3] text-[#1E2A28]">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 md:px-6">
        <div class="grid w-full gap-6 lg:grid-cols-[1fr_minmax(0,1.2fr)]">
            <section class="rounded-3xl bg-[#8FA8A6] px-7 py-8 text-white shadow-lg md:px-10 md:py-12">
                <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-semibold text-white/90 hover:text-white">
                    &larr; Volver a Moveet
                </a>

                <p class="mt-8 text-sm font-semibold uppercase tracking-[0.2em] text-white/80">Atenci&oacute;n al usuario</p>
                <h1 class="mt-3 text-4xl font-black leading-tight">Cu&eacute;ntanos tu duda y te respondemos por correo.</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-white/90">
                    Si tienes una pregunta, un problema o necesitas ayuda con tu cuenta, escr&iacute;benos aqu&iacute;.
                    El mensaje llegar&aacute; a <strong>moveetrun@gmail.com</strong> y podremos responderte directamente.
                </p>

                <div class="mt-8 space-y-3 text-sm text-white/90">
                    <div class="rounded-2xl bg-white/10 px-4 py-3">Explica el problema con el mayor detalle posible.</div>
                    @if ($isAuthenticated)
                        <div class="rounded-2xl bg-white/10 px-4 py-3">Usaremos el correo de tu cuenta para responderte directamente.</div>
                    @else
                        <div class="rounded-2xl bg-white/10 px-4 py-3">Aseg&uacute;rate de poner bien tu correo para que podamos contestarte.</div>
                    @endif
                    <div class="rounded-2xl bg-white/10 px-4 py-3">Te responderemos desde el equipo de Moveet lo antes posible.</div>
                </div>
            </section>

            <section class="rounded-3xl bg-white px-6 py-7 shadow-lg ring-1 ring-[#d5e0df] md:px-8 md:py-8">
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

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#8FA8A6] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7a9a98]"
                    >
                        Enviar consulta
                    </button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>

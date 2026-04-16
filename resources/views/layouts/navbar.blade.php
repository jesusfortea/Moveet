@php
    $navUser = \Illuminate\Support\Facades\Auth::user();
@endphp

<nav class="bg-[#8FA8A6] w-full h-[15vh] min-h-[92px] fixed top-0 left-0 z-50">
    <div class="h-full px-3 md:px-5 flex items-center justify-between gap-4">

        <div class="hidden md:flex items-center gap-3 min-w-0 w-[36%]">
            <img class="h-[12vh] max-h-[86px] min-h-16 w-auto" src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet">

            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ $navUser ? route('usuario.index') : route('login') }}" class="bg-white border border-gray-200 rounded-full w-16 h-16 min-w-16 min-h-16 overflow-hidden flex items-center justify-center">
                    @if ($navUser?->ruta_imagen_url)
                        <img src="{{ $navUser->ruta_imagen_url }}" alt="Foto de perfil" class="w-full h-full object-cover rounded-full">
                    @else
                        <span class="text-sm font-bold text-[#6B8F8D]">{{ strtoupper(substr($navUser?->name ?? 'U', 0, 1)) }}</span>
                    @endif
                </a>

                <div class="min-w-0 leading-tight text-[#1E2A28]">
                    @if ($navUser)
                        <a href="{{ route('usuario.index') }}" class="block font-bold truncate">{{ $navUser->name }}</a>
                        <p class="text-[12px] truncate">{{ $navUser->puntos }} puntos</p>
                        <p class="text-[12px] truncate">Nvl {{ $navUser->nivel }}</p>
                    @else
                        <a href="{{ route('login') }}" class="block font-bold">Iniciar sesión</a>
                        <p class="text-[12px]">Accede a tu cuenta</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="md:hidden w-full flex items-center justify-between px-1 text-[#1E2A28]">
            <a href="{{ $navUser ? route('usuario.index') : route('login') }}" class="bg-white border border-gray-200 rounded-full w-11 h-11 overflow-hidden flex items-center justify-center">
                @if ($navUser?->ruta_imagen_url)
                    <img src="{{ $navUser->ruta_imagen_url }}" alt="Foto de perfil" class="w-full h-full object-cover rounded-full">
                @else
                    <span class="text-xs font-bold text-[#6B8F8D]">{{ strtoupper(substr($navUser?->name ?? 'U', 0, 1)) }}</span>
                @endif
            </a>

            <button id="mobile-nav-toggle" type="button" class="w-12 h-12 flex items-center justify-center rounded hover:bg-[#7f9d9a]" aria-label="Abrir menú">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="w-12 h-12 flex items-center justify-center rounded hover:bg-[#7f9d9a]" aria-label="Salir">
                    <img src="{{ asset('img/Exit.png') }}" alt="Salir" class="w-6 h-6">
                </button>
            </form>
        </div>

        <div class="flex-1 h-full hidden md:flex items-center justify-center gap-7 lg:gap-9 text-[#1E2A28] font-semibold">
            <a href="{{ route('home') }}" class="whitespace-nowrap hover:opacity-80">Inicio</a>
            <a class="whitespace-nowrap hover:opacity-80">Evento</a>
            <a href="{{ route('chat.index') }}" class="whitespace-nowrap hover:opacity-80">Chat</a>
            <a class="whitespace-nowrap hover:opacity-80">Pase de paseo</a>
            <a class="whitespace-nowrap hover:opacity-80">Tienda</a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="shrink-0 hidden md:block">
            @csrf
            <button type="submit" class="flex items-center justify-center gap-2 text-[#1E2A28] font-semibold px-2 py-1 rounded hover:bg-[#7f9d9a]">
                <img src="{{ asset('img/Exit.png') }}" alt="Salir" class="w-8 h-8">
                <span>Salir</span>
            </button>
        </form>
    </div>
</nav>

<div id="mobile-nav-backdrop" class="md:hidden fixed inset-0 bg-black/20 z-40 hidden"></div>

<aside id="mobile-nav-panel" class="md:hidden fixed top-[15vh] left-0 w-[220px] max-w-[75vw] h-[calc(100vh-15vh)] bg-[#9bb0ae] z-50 shadow-xl -translate-x-full transition-transform duration-200 ease-out">
    <div class="h-full flex flex-col justify-between p-4 text-[#1E2A28]">
        <div class="space-y-5 text-center font-medium">
            <a href="{{ route('home') }}" class="block">Inicio</a>
            <a class="block">Evento</a>
            <a href="{{ route('chat.index') }}" class="block">Chat</a>
            <a class="block">Pase de paseo</a>
            <a class="block">Tienda</a>
        </div>

        <div class="flex items-center gap-2 text-[11px] text-[#4f5f5d]">
            <img class="w-10 h-10 object-contain" src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet">
            <div>
                <p class="leading-tight">Moveet</p>
                <p class="leading-tight">www.moveet.es</p>
            </div>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('mobile-nav-toggle');
        const panel = document.getElementById('mobile-nav-panel');
        const backdrop = document.getElementById('mobile-nav-backdrop');

        if (!toggle || !panel || !backdrop) {
            return;
        }

        const closeMenu = () => {
            panel.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        };

        const openMenu = () => {
            panel.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        };

        toggle.addEventListener('click', function () {
            if (panel.classList.contains('-translate-x-full')) {
                openMenu();
                return;
            }

            closeMenu();
        });

        backdrop.addEventListener('click', closeMenu);
        panel.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
    });
</script>
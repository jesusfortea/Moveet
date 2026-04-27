@php
    $navUser = \Illuminate\Support\Facades\Auth::user();
@endphp

<nav class="bg-[#8FA8A6] w-full h-[15vh] min-h-[92px] sticky top-0 left-0 z-3000">
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
                        <div class="flex items-center gap-1.5">
                            <p class="text-[12px] truncate">{{ $navUser->puntos }} puntos</p>
                            <a href="{{ route('tienda.puntos') }}" class="flex items-center justify-center w-4 h-4 bg-[#1E2A28] text-white rounded-full text-[10px] font-bold hover:bg-[#324542] transition-colors" title="Ir a la tienda">+</a>
                        </div>
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

        <div class="flex-1 h-full hidden md:flex items-center justify-center gap-6 lg:gap-8 text-[#1E2A28] font-semibold">
            <!-- Inicio -->
            <a href="{{ route('home') }}" class="whitespace-nowrap hover:opacity-80">Inicio</a>
            
            <!-- Actividad -->
            <div class="relative group h-full flex items-center">
                <button class="flex items-center gap-1 hover:opacity-80 outline-none">
                    Actividad
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute top-[80%] left-1/2 -translate-x-1/2 min-w-[170px] bg-[#EEF2F1] shadow-lg rounded-xl border border-gray-200/40 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 flex flex-col py-2 z-50">
                    <a href="{{ route('eventos') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Evento</a>
                    <a href="{{ route('rutas.index') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Rutas</a>
                    @if($navUser?->premium)
                        <a href="{{ route('rutas.crear') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Crear ruta</a>
                    @endif
                </div>
            </div>

            <!-- Beneficios -->
            <div class="relative group h-full flex items-center">
                <button class="flex items-center gap-1 hover:opacity-80 outline-none">
                    Beneficios
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute top-[80%] left-1/2 -translate-x-1/2 min-w-[170px] bg-[#EEF2F1] shadow-lg rounded-xl border border-gray-200/40 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 flex flex-col py-2 z-50">
                    <a href="{{ route('tienda.index') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Tienda</a>
                    <a href="{{ route('pase.paseo') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Pase de paseo</a>
                    <a href="{{ route('usuario.inventario') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Recompensas</a>
                </div>
            </div>

            <!-- Comunidad -->
            <div class="relative group h-full flex items-center">
                <button class="flex items-center gap-1 hover:opacity-80 outline-none">
                    Comunidad
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute top-[80%] left-1/2 -translate-x-1/2 min-w-[170px] bg-[#EEF2F1] shadow-lg rounded-xl border border-gray-200/40 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 flex flex-col py-2 z-50">
                    <a href="{{ route('chat.index') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Chat</a>
                    <a href="{{ route('preguntas.index') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap"><i class="fas fa-star mr-1"></i> Reseñas</a>
                </div>
            </div>

            <!-- Perfil -->
            <div class="relative group h-full flex items-center">
                <button class="flex items-center gap-1 hover:opacity-80 outline-none">
                    Perfil
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute top-[80%] right-0 min-w-[180px] bg-[#EEF2F1] shadow-lg rounded-xl border border-gray-200/40 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 flex flex-col py-2 z-50">
                    <a href="{{ route('usuario.historial_puntos') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Historial</a>
                    <a href="{{ route('usuario.notificaciones') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Notificaciones</a>
                    <a href="{{ route('atencion.create') }}" class="px-5 py-2.5 hover:bg-[#C5D8D6] transition-colors whitespace-nowrap">Atención al usuario</a>
                </div>
            </div>
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

<aside id="mobile-nav-panel" class="md:hidden fixed top-[15vh] left-0 w-[240px] max-w-[75vw] h-[calc(100vh-15vh)] bg-[#9bb0ae] z-50 shadow-xl -translate-x-full transition-transform duration-200 ease-out overflow-y-auto">
    <div class="min-h-full flex flex-col justify-between p-4 text-[#1E2A28]">
        <div>
            @if ($navUser)
                <div class="flex flex-col items-center mb-5 pb-4 border-b border-black/10">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-bold text-lg">{{ $navUser->puntos }} puntos</span>
                        <a href="{{ route('tienda.puntos') }}" class="flex items-center justify-center w-5 h-5 bg-[#1E2A28] text-white rounded-full text-[12px] font-bold">+</a>
                    </div>
                    <span class="text-xs opacity-70">Nvl {{ $navUser->nivel }}</span>
                </div>
            @endif
            
            <div class="space-y-4 text-left font-medium pl-1">
                <a href="{{ route('home') }}" class="block hover:opacity-70">Inicio</a>
                
                <div class="pt-3 border-t border-black/10">
                    <p class="text-[10px] uppercase tracking-widest opacity-50 mb-2 font-bold">Actividad</p>
                    <div class="space-y-3 pl-3 border-l-2 border-[#7f9d9a]">
                        <a href="{{ route('eventos') }}" class="block hover:opacity-70">Evento</a>
                        <a href="{{ route('rutas.index') }}" class="block hover:opacity-70">Rutas</a>
                        @if($navUser?->premium)
                            <a href="{{ route('rutas.crear') }}" class="block hover:opacity-70">Crear ruta</a>
                        @endif
                    </div>
                </div>

                <div class="pt-3 border-t border-black/10">
                    <p class="text-[10px] uppercase tracking-widest opacity-50 mb-2 font-bold">Beneficios</p>
                    <div class="space-y-3 pl-3 border-l-2 border-[#7f9d9a]">
                        <a href="{{ route('tienda.index') }}" class="block hover:opacity-70">Tienda</a>
                        <a href="{{ route('pase.paseo') }}" class="block hover:opacity-70">Pase de paseo</a>
                        <a href="{{ route('usuario.inventario') }}" class="block hover:opacity-70">Recompensas</a>
                    </div>
                </div>

                <div class="pt-3 border-t border-black/10">
                    <p class="text-[10px] uppercase tracking-widest opacity-50 mb-2 font-bold">Comunidad</p>
                    <div class="space-y-3 pl-3 border-l-2 border-[#7f9d9a]">
                        <a href="{{ route('chat.index') }}" class="block hover:opacity-70">Chat</a>
                        <a href="{{ route('preguntas.index') }}" class="block hover:opacity-70"><i class="fas fa-star mr-1"></i> Reseñas</a>
                    </div>
                </div>

                <div class="pt-3 border-t border-black/10 pb-4">
                    <p class="text-[10px] uppercase tracking-widest opacity-50 mb-2 font-bold">Perfil</p>
                    <div class="space-y-3 pl-3 border-l-2 border-[#7f9d9a]">
                        <a href="{{ route('usuario.historial_puntos') }}" class="block hover:opacity-70">Historial</a>
                        <a href="{{ route('usuario.notificaciones') }}" class="block hover:opacity-70">Notificaciones</a>
                        <a href="{{ route('atencion.create') }}" class="block hover:opacity-70">Atención al usuario</a>
                    </div>
                </div>
            </div>
        </div>{{-- fin flex-col --}}

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

        toggle.onclick = function () {
            if (panel.classList.contains('-translate-x-full')) {
                openMenu();
                return;
            }

            closeMenu();
        };

        backdrop.onclick = closeMenu;
        panel.querySelectorAll('a').forEach(function (link) {
            link.onclick = closeMenu;
        });
    });
</script>

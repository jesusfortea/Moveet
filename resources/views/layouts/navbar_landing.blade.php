<nav class="landing-nav fixed top-0 w-full z-50 py-4 bg-white border-b border-moveet-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex justify-between items-center">
        <a href="{{ route('landing') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/LogoUsarDiaDia.png') }}" class="h-10" alt="Moveet">
            <span class="text-2xl font-black text-moveet-text">Moveet</span>
        </a>
        
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('landing') }}#como-funciona" class="nav-link">Cómo funciona</a>
            <a href="{{ route('landing') }}#ventajas" class="nav-link">Ventajas</a>
            <a href="{{ route('preguntas.index') }}" class="nav-link {{ request()->routeIs('preguntas.index') ? 'text-moveet-primary' : '' }}">Reseñas</a>
            <div class="h-6 w-px bg-moveet-border"></div>
            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="font-bold text-moveet-text hover:text-moveet-primary">Panel Admin</a>
                @else
                    <a href="{{ route('home') }}" class="font-bold text-moveet-text hover:text-moveet-primary">Panel de Control</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="font-bold text-moveet-text hover:text-moveet-primary">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn-moveet text-sm py-3 px-6">Empezar gratis</a>
            @endauth
        </div>

        <button class="md:hidden text-2xl" id="mobile-menu-btn"><i class="fas fa-bars"></i></button>
    </div>
</nav>

<!-- Mobile Menu -->
<div id="mobile-menu" class="fixed inset-0 bg-white z-[60] hidden flex-col p-8">
    <div class="flex justify-between items-center mb-10">
        <a href="{{ route('landing') }}" class="text-2xl font-black">Moveet</a>
        <button id="close-menu" class="text-3xl"><i class="fas fa-times"></i></button>
    </div>
    <div class="flex flex-col gap-6">
        <a href="{{ route('landing') }}#como-funciona" class="text-2xl font-bold">Cómo funciona</a>
        <a href="{{ route('landing') }}#ventajas" class="text-2xl font-bold">Ventajas</a>
        <a href="{{ route('preguntas.index') }}" class="text-2xl font-bold">Reseñas</a>
        <hr>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">Panel Admin</a>
            @else
                <a href="{{ route('home') }}" class="text-2xl font-bold">Panel de Control</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="text-2xl font-bold">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="btn-moveet text-center py-4">Registrarse</a>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('mobile-menu-btn');
        const close = document.getElementById('close-menu');
        const menu = document.getElementById('mobile-menu');

        if (btn && close && menu) {
            btn.onclick = () => menu.classList.remove('hidden');
            close.onclick = () => menu.classList.add('hidden');
            menu.querySelectorAll('a').forEach(a => a.onclick = () => menu.classList.add('hidden'));
        }
    });
</script>

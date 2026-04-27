<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin - Moveet')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#f5f5f5] text-[#1E2A28] overflow-hidden">
    @php
        $adminUser = \Illuminate\Support\Facades\Auth::user();
        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
        $adminLinks = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'keywords' => 'inicio panel principal'],
            ['label' => 'Usuarios', 'route' => 'admin.usuarios', 'keywords' => 'clientes cuentas personas'],
            ['label' => 'Misiones', 'route' => 'admin.misiones', 'keywords' => 'retos tareas'],
            ['label' => 'Eventos', 'route' => 'admin.eventos', 'keywords' => 'actividades calendario'],
            ['label' => 'Rese&ntilde;as de usuarios', 'route' => 'admin.preguntas', 'keywords' => 'preguntas faq opiniones comentarios valoraciones resenas reseñas'],
            ['label' => 'Pase de paseo', 'route' => 'admin.pase_paseo', 'keywords' => 'pases paseo'],
            ['label' => 'Lugares', 'route' => 'admin.lugares', 'keywords' => 'sitios ubicaciones'],
            ['label' => 'Recompensas', 'route' => 'admin.recompensas', 'keywords' => 'premios regalos'],
            ['label' => 'Tienda', 'route' => 'admin.tienda', 'keywords' => 'productos compras'],
        ];
    @endphp

    <div class="h-screen grid grid-rows-[78px_minmax(0,1fr)_22px]">
        <header class="bg-[#8FA8A6] px-4">
            <div class="flex h-full items-center gap-3">
                <div class="shrink-0">
                    <img src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet" class="h-[54px] w-auto">
                </div>

                <div class="flex flex-1 justify-center">
                    <div class="relative w-full max-w-[500px]">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[12px] text-[#8a9594]">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            id="admin-menu-search"
                            type="text"
                            placeholder="Buscar secciones del panel..."
                            class="w-full rounded-[8px] border-2 border-[#2f2f2f] bg-white px-4 py-2 pl-8 text-[12px] text-[#1E2A28] outline-none"
                        >
                    </div>
                </div>

                <div class="hidden shrink-0 items-center gap-3 text-white md:flex">
                    <div class="flex h-[40px] w-[40px] items-center justify-center rounded-full bg-white text-[13px] font-bold text-[#8FA8A6]">
                        {{ strtoupper(substr($adminUser?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="leading-tight">
                        <p class="text-[13px] font-bold">{{ $adminUser->name ?? 'Admin' }}</p>
                        <p class="text-[10px] opacity-95">usuario administrador</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[12px] font-semibold transition hover:opacity-80">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="grid min-h-0 grid-cols-[184px_minmax(0,1fr)] overflow-hidden">
            <aside class="min-h-0 overflow-hidden bg-[#c5d8d6]">
                <div class="h-full overflow-y-auto px-3 py-4 space-y-3">
                    @foreach ($adminLinks as $link)
                        @php
                            $isActive = $currentRoute === $link['route'] || str_starts_with($currentRoute ?? '', $link['route'] . '.');
                        @endphp
                        <a
                            href="{{ route($link['route']) }}"
                            data-menu-label="{{ mb_strtolower($link['label'] . ' ' . ($link['keywords'] ?? '')) }}"
                            class="admin-menu-link block rounded-[8px] px-3 py-3 text-center text-[12px] font-semibold leading-tight transition {{ $isActive ? 'bg-[#9fb2b0] text-white' : 'bg-white text-[#1E2A28] hover:bg-[#f0f0f0]' }}"
                        >
                            {!! $link['label'] !!}
                        </a>
                    @endforeach

                    <p id="admin-menu-empty" class="hidden rounded-[8px] bg-white px-3 py-3 text-center text-[11px] text-[#58706e]">
                        No hay coincidencias para esa b&uacute;squeda.
                    </p>
                </div>
            </aside>

            <main class="min-h-0 overflow-y-auto bg-white px-4 py-4">
                @yield('content')
            </main>
        </div>

        <footer class="flex items-center justify-center bg-[#8FA8A6] text-center text-[10px] font-semibold leading-tight text-white">
            Moveet
        </footer>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('admin-menu-search');
            const links = Array.from(document.querySelectorAll('.admin-menu-link'));
            const emptyState = document.getElementById('admin-menu-empty');

            if (!searchInput || !links.length || !emptyState) {
                return;
            }

            const normalize = (value) => value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const filterLinks = () => {
                const query = normalize(searchInput.value);
                let visibleCount = 0;

                links.forEach((link) => {
                    const label = normalize(link.dataset.menuLabel || link.textContent || '');
                    const isVisible = query === '' || label.includes(query);

                    link.classList.toggle('hidden', !isVisible);

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
            };

            searchInput.addEventListener('input', filterLinks);
            searchInput.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();

                const firstVisibleLink = links.find((link) => !link.classList.contains('hidden'));

                if (firstVisibleLink) {
                    window.location.href = firstVisibleLink.href;
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>

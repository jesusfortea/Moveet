<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moveet - El Mundo es tu Tablero</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        moveet: {
                            primary: '#7fa8a8',
                            primaryDark: '#6b9595',
                            bg: '#f9f9f9',
                            text: '#333',
                            muted: '#666',
                            border: '#ddd',
                            accent: '#7fa8a8',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --m-primary: #7fa8a8;
            --m-primary-dark: #6b9595;
            --m-bg: #f9f9f9;
            --m-text: #333;
            --m-muted: #666;
            --m-border: #ddd;
            --m-radius: 12px;
            --m-btn-radius: 6px;
        }

        body {
            background-color: var(--m-bg);
            color: var(--m-text);
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .landing-nav {
            background: white;
            border-bottom: 1px solid var(--m-border);
        }

        .hero-section {
            background: linear-gradient(135deg, #c8e6e6 0%, #a8d0d0 100%);
            padding-top: 140px;
            padding-bottom: 100px;
        }

        .btn-moveet {
            background: var(--m-primary);
            color: white;
            padding: 12px 28px;
            border-radius: var(--m-btn-radius);
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .btn-moveet:hover {
            background: var(--m-primary-dark);
            transform: scale(0.98);
        }

        .card-moveet {
            background: white;
            border-radius: var(--m-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 32px;
            border: 1px solid var(--m-border);
            transition: all 0.3s ease;
        }

        .card-moveet:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .carousel-container {
            position: relative;
            padding: 40px 0;
            background: white;
        }

        .carousel-track {
            display: flex;
            gap: 24px;
            transition: transform 0.5s ease-in-out;
            padding: 0 20px;
        }

        .carousel-item {
            min-width: 300px;
            max-width: 400px;
            border-radius: var(--m-radius);
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--m-border);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 20px;
            line-height: 1.2;
            color: #333;
        }

        .nav-link {
            font-weight: 600;
            color: #666;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--m-primary);
        }
    </style>
</head>
<body>

    @include('layouts.navbar_landing')

    <!-- Hero -->
    <section class="hero-section">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h1 class="section-title">
                    El mundo es tu <br>
                    <span style="color: #444;">tablero de juego.</span>
                </h1>
                <p class="text-xl text-moveet-muted font-medium mb-10 max-w-lg leading-relaxed">
                    Convierte cada paso en una misión. Explora tu ciudad, completa desafíos en tiempo real y canjea tu esfuerzo por recompensas increíbles.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="btn-moveet py-4 px-10 text-lg">¡Quiero empezar ya!</a>
                    <a href="#como-funciona" class="px-8 py-4 font-bold flex items-center gap-2 hover:bg-white/50 rounded-lg transition-all">
                        Ver más <i class="fas fa-chevron-down text-xs"></i>
                    </a>
                </div>
                
                <div class="mt-12 flex items-center gap-6">
                    <div class="flex -space-x-4">
                        <img class="w-12 h-12 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=1">
                        <img class="w-12 h-12 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=2">
                        <img class="w-12 h-12 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=3">
                    </div>
                    <p class="text-sm font-bold text-moveet-muted">
                        <span class="text-moveet-text">+1,500 exploradores</span> ya están jugando
                    </p>
                </div>
            </div>
            
            <div class="relative">
                <img src="{{ asset('img/hero-new.png') }}" class="rounded-xl shadow-2xl" alt="Moveet App">
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl flex items-center gap-3 border border-moveet-border">
                    <div class="w-10 h-10 bg-moveet-primary rounded-md flex items-center justify-center text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-moveet-muted">Misión completada</p>
                        <p class="text-sm font-black">+50 Puntos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Grid -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-12">
            <h2 class="text-3xl font-bold">Vive la experiencia</h2>
            <p class="text-moveet-muted font-medium">Así es como moveet transforma tu día a día.</p>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="carousel-item">
                <img src="{{ asset('img/carousel-1.png') }}" class="w-full aspect-[4/3] object-cover">
                <div class="p-6 bg-white">
                    <h4 class="font-bold text-lg">Explora tu ciudad</h4>
                    <p class="text-sm text-moveet-muted">Nuevas rutas cada día.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/carousel-2.png') }}" class="w-full aspect-[4/3] object-cover">
                <div class="p-6 bg-white">
                    <h4 class="font-bold text-lg">Gana Premios</h4>
                    <p class="text-sm text-moveet-muted">Canjea tus puntos por café, ropa o entradas.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/carousel-3.png') }}" class="w-full aspect-[4/3] object-cover">
                <div class="p-6 bg-white">
                    <h4 class="font-bold text-lg">Compite con amigos</h4>
                    <p class="text-sm text-moveet-muted">Ranking global y chat integrado.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/hero-new.png') }}" class="w-full aspect-[4/3] object-cover">
                <div class="p-6 bg-white">
                    <h4 class="font-bold text-lg">Mejora tu Salud</h4>
                    <p class="text-sm text-moveet-muted">Caminar nunca fue tan divertido.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="como-funciona" class="py-24 bg-white border-t border-moveet-border">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="section-title">Cómo funciona</h2>
                <p class="text-moveet-muted font-medium text-lg">Tres pasos sencillos para empezar tu aventura.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center space-y-6">
                    <div class="w-20 h-20 bg-moveet-bg border border-moveet-border rounded-xl flex items-center justify-center text-3xl text-moveet-primary mx-auto">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Elige tu ruta</h3>
                    <p class="text-moveet-muted font-medium">Selecciona una misión en el mapa según tu ubicación y dificultad.</p>
                </div>
                <div class="text-center space-y-6">
                    <div class="w-20 h-20 bg-moveet-bg border border-moveet-border rounded-xl flex items-center justify-center text-3xl text-moveet-primary mx-auto">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Camina y gana</h3>
                    <p class="text-moveet-muted font-medium">Completa los puntos de control para ganar puntos y experiencia.</p>
                </div>
                <div class="text-center space-y-6">
                    <div class="w-20 h-20 bg-moveet-primary rounded-xl flex items-center justify-center text-3xl text-white mx-auto">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Disfruta premios</h3>
                    <p class="text-moveet-muted font-medium">Usa tus puntos en la tienda para conseguir recompensas reales.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="ventajas" class="py-24 bg-moveet-bg border-t border-moveet-border">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <h2 class="section-title">Diseñado para <br> <span style="color: var(--m-primary);">moverte.</span></h2>
                    <p class="text-lg text-moveet-muted font-medium">Hemos creado una plataforma robusta, justa y social para que disfrutes al máximo.</p>
                    
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="card-moveet">
                            <i class="fas fa-shield-alt text-2xl text-moveet-primary mb-4"></i>
                            <h5 class="font-bold text-lg mb-2">Anti-Cheat</h5>
                            <p class="text-xs text-moveet-muted font-bold uppercase tracking-wider">Seguridad Total</p>
                        </div>
                        <div class="card-moveet">
                            <i class="fas fa-users text-2xl text-moveet-primary mb-4"></i>
                            <h5 class="font-bold text-lg mb-2">Social</h5>
                            <p class="text-xs text-moveet-muted font-bold uppercase tracking-wider">Amigos y Chat</p>
                        </div>
                        <div class="card-moveet">
                            <i class="fas fa-bolt text-2xl text-moveet-primary mb-4"></i>
                            <h5 class="font-bold text-lg mb-2">Potenciadores</h5>
                            <p class="text-xs text-moveet-muted font-bold uppercase tracking-wider">Multiplica puntos</p>
                        </div>
                        <div class="card-moveet">
                            <i class="fas fa-trophy text-2xl text-moveet-primary mb-4"></i>
                            <h5 class="font-bold text-lg mb-2">Logros</h5>
                            <p class="text-xs text-moveet-muted font-bold uppercase tracking-wider">Colecciona medallas</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid gap-6">
                    <div class="card-moveet flex gap-6 items-center">
                        <div class="w-16 h-16 bg-moveet-bg border border-moveet-border rounded-xl flex items-center justify-center text-2xl text-moveet-primary">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Pase de Paseo</h4>
                            <p class="text-sm text-moveet-muted">Progresión por niveles con premios premium.</p>
                        </div>
                    </div>
                    <div class="card-moveet flex gap-6 items-center">
                        <div class="w-16 h-16 bg-moveet-bg border border-moveet-border rounded-xl flex items-center justify-center text-2xl text-moveet-primary">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Tienda Integrada</h4>
                            <p class="text-sm text-moveet-muted">Canje instantáneo de puntos por productos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-white text-center">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="section-title mb-8">¿Estás listo para <br> tu primera misión?</h2>
            <p class="text-xl text-moveet-muted font-medium mb-12">Únete a la comunidad y empieza a ganar premios por tus pasos hoy mismo.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="{{ route('register') }}" class="btn-moveet py-5 px-12 text-xl">Crear cuenta gratis</a>
                <a href="{{ route('login') }}" class="py-5 px-12 text-xl font-bold border border-moveet-border rounded-md hover:bg-moveet-bg transition-all">Iniciar sesión</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 bg-white border-t border-moveet-border">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid md:grid-cols-4 gap-12">
            <div class="col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('img/LogoUsarDiaDia.png') }}" class="h-8">
                    <span class="text-xl font-black">Moveet</span>
                </div>
                <p class="text-moveet-muted font-medium max-w-sm">La plataforma que convierte tu actividad física en una aventura épica con recompensas reales.</p>
            </div>
            <div>
                <h5 class="font-bold mb-6 uppercase text-xs tracking-widest">Legal</h5>
                <ul class="space-y-4 text-sm font-bold text-moveet-muted">
                    <li><a href="#" class="hover:text-moveet-text">Privacidad</a></li>
                    <li><a href="#" class="hover:text-moveet-text">Términos</a></li>
                    <li><a href="{{ route('atencion.create') }}" class="hover:text-moveet-text">Contacto</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6 uppercase text-xs tracking-widest">Síguenos</h5>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 bg-moveet-bg border border-moveet-border rounded-md flex items-center justify-center hover:text-moveet-primary transition-all"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 bg-moveet-bg border border-moveet-border rounded-md flex items-center justify-center hover:text-moveet-primary transition-all"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-10 h-10 bg-moveet-bg border border-moveet-border rounded-md flex items-center justify-center hover:text-moveet-primary transition-all"><i class="fab fa-discord"></i></a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 mt-16 pt-8 border-t border-moveet-border text-center text-xs font-bold text-moveet-muted uppercase tracking-[0.2em]">
            &copy; 2026 Moveet Team. Todos los derechos reservados.
        </div>
    </footer>

    <script>
        // Menu
        const btn = document.getElementById('mobile-menu-btn');
        const close = document.getElementById('close-menu');
        const menu = document.getElementById('mobile-menu');

        btn.onclick = () => menu.classList.remove('hidden');
        close.onclick = () => menu.classList.add('hidden');
        menu.querySelectorAll('a').forEach(a => a.onclick = () => menu.classList.add('hidden'));

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>

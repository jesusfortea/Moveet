<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moveet - El Mundo es tu Tablero</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E2A28',
                        secondary: '#8FA8A6',
                        accent: '#F59E0B',
                        dark: '#111827',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        jakarta: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: #0F172A;
            color: #F8FAFC;
            overflow-x: hidden;
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
        }

        .hero-gradient {
            background: radial-gradient(circle at 50% -20%, #1e3a38 0%, #0f172a 70%);
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #8FA8A6 0%, #1E2A28 100%);
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.15;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(-10%, -10%); }
            to { transform: translate(20%, 20%); }
        }

        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(143, 168, 166, 0.4);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(143, 168, 166, 0.3);
            transform: translateY(-10px);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .text-gradient {
            background: linear-gradient(135deg, #F8FAFC 0%, #8FA8A6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- Blobs Background -->
    <div class="blob top-[-10%] left-[-10%]"></div>
    <div class="blob bottom-[10%] right-[-10%]" style="background: linear-gradient(135deg, #1E2A28 0%, #8FA8A6 100%); animation-delay: -5s;"></div>

    <!-- Navbar -->
    <nav class="glass-nav fixed top-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/LogoUsarDiaDia.png') }}" class="h-10 w-auto" alt="Moveet">
                    <span class="text-2xl font-bold tracking-tight text-white font-jakarta">Moveet</span>
                </div>
                
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                    <a href="#features" class="hover:text-white transition-colors">Funcionalidades</a>
                    <a href="{{ route('preguntas.index') }}" class="hover:text-white transition-colors">Reseñas</a>
                    <a href="{{ route('atencion.create') }}" class="hover:text-white transition-colors">Soporte</a>
                    <div class="h-4 w-px bg-slate-700"></div>
                    <a href="{{ route('login') }}" class="text-white hover:opacity-80">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="bg-secondary text-primary px-6 py-2.5 rounded-full font-bold btn-glow transition-all">
                        Empezar gratis
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-white p-2" id="mobile-menu-btn">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Nav -->
    <div id="mobile-menu" class="fixed inset-0 bg-dark z-[60] hidden transition-all duration-300 transform translate-x-full">
        <div class="flex flex-col p-8 gap-8">
            <div class="flex justify-between items-center">
                <span class="text-2xl font-bold text-white">Moveet</span>
                <button id="close-menu" class="text-white text-2xl"><i class="fas fa-times"></i></button>
            </div>
            <a href="#features" class="text-2xl text-slate-300">Funcionalidades</a>
            <a href="{{ route('preguntas.index') }}" class="text-2xl text-slate-300">Reseñas</a>
            <a href="{{ route('atencion.create') }}" class="text-2xl text-slate-300">Soporte</a>
            <div class="flex flex-col gap-4 mt-4">
                <a href="{{ route('login') }}" class="w-full text-center border border-slate-700 py-4 rounded-xl text-white">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="w-full text-center bg-secondary py-4 rounded-xl text-primary font-bold">Registrarse</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen pt-32 pb-20 flex items-center">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-center lg:text-left space-y-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 border border-secondary/20 text-secondary text-xs font-bold uppercase tracking-wider">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                    </span>
                    Gamificación del mundo real
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-extrabold leading-[1.1] font-jakarta tracking-tight">
                    El mundo es tu <br/>
                    <span class="text-gradient">tablero de juego.</span>
                </h1>
                
                <p class="text-lg lg:text-xl text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Convierte cada paso en una aventura. Completa misiones basadas en tu ubicación, gana puntos reales y desbloquea recompensas en tu ciudad.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto bg-secondary text-primary px-10 py-4 rounded-2xl font-extrabold text-lg btn-glow transition-all flex items-center justify-center gap-2">
                        Comenzar aventura <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                    <a href="#features" class="w-full sm:w-auto bg-white/5 border border-white/10 hover:bg-white/10 px-10 py-4 rounded-2xl font-bold transition-all text-center">
                        Ver cómo funciona
                    </a>
                </div>

                <div class="pt-8 flex items-center justify-center lg:justify-start gap-4">
                    <div class="flex -space-x-3">
                        <img class="h-10 w-10 rounded-full border-2 border-dark" src="https://i.pravatar.cc/100?u=1" alt="">
                        <img class="h-10 w-10 rounded-full border-2 border-dark" src="https://i.pravatar.cc/100?u=2" alt="">
                        <img class="h-10 w-10 rounded-full border-2 border-dark" src="https://i.pravatar.cc/100?u=3" alt="">
                    </div>
                    <div class="text-sm text-slate-400">
                        <span class="text-white font-bold">+1,200</span> usuarios ya están jugando
                    </div>
                </div>
            </div>

            <div class="relative lg:block">
                <div class="relative z-10 animate-float">
                    <img src="{{ asset('img/hero-landing.png') }}" alt="Moveet App" class="rounded-3xl shadow-2xl border border-white/10">
                    
                    <!-- Floating UI elements -->
                    <div class="absolute -top-6 -left-6 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-xl hidden sm:block">
                        <div class="flex items-center gap-3">
                            <div class="bg-accent p-2 rounded-lg">
                                <i class="fas fa-trophy text-white"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Logro desbloqueado</p>
                                <p class="text-sm font-bold text-white">Explorador Urbano</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -bottom-10 -right-6 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-xl hidden sm:block">
                        <div class="flex items-center gap-3">
                            <div class="bg-secondary p-2 rounded-lg">
                                <i class="fas fa-coins text-primary"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Puntos ganados</p>
                                <p class="text-sm font-bold text-white">+250 PTS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-dark/50 border-y border-white/5 py-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap justify-center gap-12 lg:gap-24">
            <div class="text-center">
                <p class="text-3xl font-black text-white">100%</p>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-1">Gratis</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-white">50k+</p>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-1">Puntos Canjeados</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-white">200+</p>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-1">Misiones Activas</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-white">24/7</p>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-1">Diversión</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <h2 class="text-secondary font-bold uppercase tracking-widest text-sm">Características</h2>
                <p class="text-4xl lg:text-5xl font-black font-jakarta">Diseñado para moverte.</p>
                <p class="text-slate-400 text-lg">Descubre por qué miles de personas han convertido su día a día en un videojuego.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-secondary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-secondary/20 transition-all">
                        <i class="fas fa-bullseye text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Misiones Diarias</h3>
                    <p class="text-slate-400 leading-relaxed">Objetivos dinámicos que cambian cada día. Camina, explora y gana experiencia para subir de nivel.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-accent/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent/20 transition-all">
                        <i class="fas fa-location-dot text-2xl text-accent"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Eventos Globales</h3>
                    <p class="text-slate-400 leading-relaxed">Participa en eventos especiales que ocurren en lugares reales. Compite con otros usuarios en tiempo real.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500/20 transition-all">
                        <i class="fas fa-gift text-2xl text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Premios Reales</h3>
                    <p class="text-slate-400 leading-relaxed">Canjea tus puntos por artículos exclusivos, pases de paseo y beneficios en establecimientos asociados.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-500/20 transition-all">
                        <i class="fas fa-comments text-2xl text-purple-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Comunidad Social</h3>
                    <p class="text-slate-400 leading-relaxed">Chatea con amigos, añade contactos mediante código QR y comparte tus logros con el mundo.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-green-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 transition-all">
                        <i class="fas fa-shield-halved text-2xl text-green-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Anti-Cheat Avanzado</h3>
                    <p class="text-slate-400 leading-relaxed">Disfruta de una competición justa. Validamos velocidad y ubicación en tiempo real mediante GPS.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card p-8 rounded-3xl group">
                    <div class="h-14 w-14 bg-red-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-red-500/20 transition-all">
                        <i class="fas fa-bolt text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Pase de Paseo</h3>
                    <p class="text-slate-400 leading-relaxed">Un sistema de progresión por niveles que recompensa tu lealtad con premios premium únicos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer CTA -->
    <section class="py-32 border-t border-white/5">
        <div class="max-w-4xl mx-auto px-6 text-center space-y-10">
            <h2 class="text-4xl lg:text-6xl font-black font-jakarta">Empieza tu viaje hoy.</h2>
            <p class="text-xl text-slate-400">Únete a la revolución de la gamificación basada en ubicación. Es gratis y siempre lo será.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-secondary text-primary px-12 py-5 rounded-2xl font-black text-xl btn-glow transition-all">
                    Crear cuenta gratis
                </a>
                <a href="{{ route('login') }}" class="bg-white/5 border border-white/10 hover:bg-white/10 px-12 py-5 rounded-2xl font-bold transition-all text-xl">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 text-center text-slate-500 text-sm">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-center gap-8 mb-8 text-slate-400 text-lg">
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-discord"></i></a>
            </div>
            <p>&copy; 2026 Moveet. Fabricado con <i class="fas fa-heart text-red-500"></i> para exploradores urbanos.</p>
            <div class="flex justify-center gap-6 mt-4">
                <a href="#" class="hover:text-white transition-colors">Privacidad</a>
                <a href="#" class="hover:text-white transition-colors">Términos</a>
                <a href="{{ route('atencion.create') }}" class="hover:text-white transition-colors">Contacto</a>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Mobile Menu
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.onclick = () => {
            mobileMenu.classList.remove('hidden');
            setTimeout(() => {
                mobileMenu.classList.remove('translate-x-full');
            }, 10);
        };

        const hideMenu = () => {
            mobileMenu.classList.add('translate-x-full');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);
        };

        closeBtn.onclick = hideMenu;
        mobileMenu.querySelectorAll('a').forEach(a => a.onclick = hideMenu);

        // Navbar effect
        window.onscroll = () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('py-4');
                nav.classList.remove('py-6');
            } else {
                nav.classList.add('py-6');
                nav.classList.remove('py-4');
            }
        };
    </script>
</body>
</html>

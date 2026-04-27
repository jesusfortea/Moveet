<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moveet - Gamificación basada en Ubicación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8FA8A6 0%, #6b8987 100%);
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background: rgba(143, 168, 166, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid rgba(107, 137, 135, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .navbar-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .navbar-links a:hover {
            opacity: 0.8;
        }

        .btn-primary {
            background: #d0dbd9;
            color: #333;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: 2px solid #d0dbd9;
        }

        .btn-primary:hover {
            background: #9db3b0;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
            padding: 3rem 5%;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 700px;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border: 2px solid white;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #d0dbd9;
            color: #333;
            border-color: #d0dbd9;
        }

        .features {
            background: #eef4f3;
            padding: 4rem 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid #d0dbd9;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(143, 168, 166, 0.2);
            border-color: #9db3b0;
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .cta-section {
            background: linear-gradient(135deg, #8FA8A6 0%, #6b8987 100%);
            padding: 4rem 5%;
            text-align: center;
            color: white;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            background: #eef4f3;
            padding: 3rem 5%;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #8FA8A6;
        }

        .stat-label {
            color: #333;
            margin-top: 0.5rem;
            font-weight: 600;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            50% {
                transform: translateX(-50%) translateY(10px);
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-map-pin"></i> Moveet
        </div>
        <div class="navbar-links">
            <a href="#features">Características</a>
            <a href="#about">Sobre Moveet</a>
            <a href="{{ route('preguntas.index') }}">Reseñas</a>
            <a href="{{ route('atencion.create') }}">Atención al usuario</a>
            <a href="{{ route('login') }}" class="btn-primary">Iniciar Sesión</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Descubre Moveet</h1>
            <p>Gamificación basada en ubicación. Completa misiones, participa en eventos, gana puntos y canjéalos por recompensas increíbles.</p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-primary">Registrarse Gratis</a>
                <a href="{{ route('login') }}" class="btn-secondary">Ya tengo cuenta</a>
            </div>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down" style="color: white; font-size: 1.5rem;"></i>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Misiones</h3>
            <p>Completa misiones diarias y semanales para ganar puntos y experiencia. Elige tus objetivos y sigue tu progreso.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📍</div>
            <h3>Eventos Cercanos</h3>
            <p>Descubre eventos geográficos cercanos a tu ubicación. Participa, conoce gente y acumula recompensas.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🎫</div>
            <h3>Pases de Paseo</h3>
            <p>Disfruta de acceso exclusivo a lugares especiales y experiencias únicas con nuestro sistema de pases.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🏪</div>
            <h3>Tienda de Recompensas</h3>
            <p>Canjea tus puntos ganados por artículos exclusivos, descuentos y experiencias premium.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💬</div>
            <h3>Chat Comunitario</h3>
            <p>Conecta con otros usuarios, forma amistades y colabora en misiones con la comunidad Moveet.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">⭐</div>
            <h3>Gamificación Completa</h3>
            <p>Sube de nivel, desbloquea logros y compite en rankings. ¡Vive la experiencia gamificada!</p>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat-item">
            <div class="stat-number">1000+</div>
            <div class="stat-label">Usuarios Activos</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">5000+</div>
            <div class="stat-label">Misiones Completadas</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">100+</div>
            <div class="stat-label">Lugares</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">50+</div>
            <div class="stat-label">Recompensas</div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="about" class="cta-section">
        <h2>¿Listo para Moveet?</h2>
        <p>Únete a miles de usuarios que ya están disfrutando de la experiencia. ¡Comienza ahora!</p>
        <a href="{{ route('register') }}" class="btn-primary">Crear mi cuenta</a>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Moveet. Todos los derechos reservados. | Gamificación basada en ubicación</p>
    </footer>

    <script>
        // Smooth scroll para links
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

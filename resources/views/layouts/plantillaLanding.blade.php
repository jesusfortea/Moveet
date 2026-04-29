<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Moveet')</title>
    
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
            padding-top: 80px; /* Navbar height */
        }

        .btn-moveet {
            background: var(--m-primary);
            color: white;
            padding: 10px 24px;
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

        .nav-link {
            font-weight: 600;
            color: #666;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--m-primary);
        }
    </style>
    @stack('styles')
</head>
<body>

    @include('layouts.navbar_landing')

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

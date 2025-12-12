<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LocaTalk') }} - Communication d'entreprise</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN (temporaire, en attendant la compilation) -->
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            * {
                font-family: 'Inter', sans-serif;
            }

            :root {
                --color-primary: #df5526;
                --color-secondary: #fbbb2a;
            }

            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
                position: relative;
                overflow-x: hidden;
            }

            /* Animated background circles */
            body::before {
                content: '';
                position: fixed;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(223, 85, 38, 0.15) 0%, transparent 70%);
                border-radius: 50%;
                top: -200px;
                right: -200px;
                animation: float 20s ease-in-out infinite;
                z-index: 0;
            }

            body::after {
                content: '';
                position: fixed;
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, rgba(251, 187, 42, 0.1) 0%, transparent 70%);
                border-radius: 50%;
                bottom: -150px;
                left: -150px;
                animation: float 25s ease-in-out infinite reverse;
                z-index: 0;
            }

            @keyframes float {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(50px, 50px) scale(1.1); }
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            .btn-primary {
                background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(223, 85, 38, 0.4);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(223, 85, 38, 0.6);
            }

            .input-field {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: white;
                transition: all 0.3s ease;
            }

            .input-field:focus {
                background: rgba(255, 255, 255, 0.08);
                border-color: #df5526;
                outline: none;
                box-shadow: 0 0 0 3px rgba(223, 85, 38, 0.1);
            }

            .input-field::placeholder {
                color: rgba(255, 255, 255, 0.4);
            }

            .logo-glow {
                filter: drop-shadow(0 0 30px rgba(223, 85, 38, 0.6));
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 relative z-10">
            <!-- Logo with glow effect -->
            <div class="mb-8 logo-glow">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #df5526 0%, #fbbb2a 100%);">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h1 class="text-2xl font-bold text-white">LocaTalk</h1>
                        <p class="text-sm text-gray-400">Communication d'entreprise</p>
                    </div>
                </a>
            </div>

            <!-- Glass card container -->
            <div class="w-full sm:max-w-md glass-card rounded-3xl p-8">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-gray-400 text-sm">
                <p>© 2025 LocaTalk. Tous droits réservés.</p>
            </div>
        </div>
    </body>
</html>

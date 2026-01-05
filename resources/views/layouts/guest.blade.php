<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ngangkot') }} - Login</title>

        <!-- Fonts: Plus Jakarta Sans (Konsisten dengan app.blade.php) -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN (Konsisten dengan layout lainnya) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            'sans': ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            'ngangkot-blue': '#2563eb',
                            'ngangkot-dark': '#0f172a',
                        }
                    }
                }
            }
        </script>

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif; 
                background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/" class="flex items-center gap-3">
                    <div class="bg-blue-600 p-3 rounded-2xl text-white shadow-lg shadow-blue-200">
                        <i data-lucide="bus" class="w-8 h-8"></i>
                    </div>
                    <span class="text-3xl font-black tracking-tight text-slate-900">
                        Ngangkot<span class="text-blue-600">.</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white/90 backdrop-blur-xl shadow-xl border border-white/70 overflow-hidden rounded-3xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} Ngangkot - Smart City Bandung
            </p>
        </div>

        <script>
            lucide.createIcons();
        </script>
    </body>
</html>

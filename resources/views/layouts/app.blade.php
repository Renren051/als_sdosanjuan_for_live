<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SDO San Juan City - Alternative Learning System (ALS)')</title>
    <meta name="description" content="Web-Based Student Information & Android-Based Modular LMS for DepEd SDO San Juan Alternative Learning System">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon (SDO San Juan City Logo: sdosj.svg) -->
    <link rel="icon" type="image/svg+xml" href="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('favicon.svg')) }}">
    <link rel="alternate icon" href="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : asset('favicon.svg') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Critical Safeguard Reset -->
    <style>
        *, ::before, ::after { box-sizing: border-box; }
        img, svg { max-width: 100%; height: auto; vertical-align: middle; }
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <!-- Styles / Scripts (Local Vite Build with CDN fallback) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Tailwind CSS Fallback -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        },
                        colors: {
                            deped: {
                                blue: '#0038A8',
                                dark: '#002673',
                                navy: '#0b193d',
                                gold: '#FCD116',
                                amber: '#d97706',
                                red: '#CE1126',
                            },
                        },
                    },
                },
            }
        </script>
        <!-- Alpine.js Fallback -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-deped-blue selection:text-white">
    @yield('body')

    @include('components.chat-widget')

    @stack('scripts')
</body>
</html>

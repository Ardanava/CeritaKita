<!DOCTYPE html>
<html lang="id">
<head>
    <!-- =========================
        ✅ META & PAGE BASICS
    ========================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- =========================
        ✅ PAGE TITLE (Dynamic)
    ========================== -->
    <title>@yield('title', 'CeritaKita')</title>

    <!-- =========================
        ✅ TAILWIND CSS
    ========================== -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- =========================
        ✅ CSRF Token (Laravel)
    ========================== -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- =========================
        ✅ GOOGLE FONTS
    ========================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- =========================
        ✅ ICON LIBRARY (Font Awesome)
    ========================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- =========================
        ✅ BASE STYLES
    ========================== -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>

    <!-- =========================
        ✅ PAGE-LEVEL STYLES
    ========================== -->
    @stack('styles')
</head>
<body class="bg-slate-50 text-gray-800 antialiased">

    <!-- =========================
        ✅ HEADER
    ========================== -->
    @include('partials.header')

    <!-- =========================
        ✅ MAIN CONTENT
    ========================== -->
    <main>
        @yield('content')
    </main>

    <!-- =========================
        ✅ FOOTER
    ========================== -->
    @include('partials.footer')

    <!-- =========================
        ✅ GLOBAL BURGER SCRIPT (Mobile Menu)
    ========================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    const icon = mobileMenuBtn.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                });
            }
        });
    </script>

    <!-- =========================
        ✅ PAGE-LEVEL SCRIPTS
    ========================== -->
    @stack('scripts')

</body>
</html>

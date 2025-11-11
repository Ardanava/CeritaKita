<!-- START: HTML Document Wrapper -->
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- START: Meta & Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VerseGate')</title>
    <!-- END: Meta & Title -->

    <!-- START: Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- END: Tailwind CSS -->

    <!-- START: CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- END: CSRF Token -->

    <!-- START: Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- END: Google Fonts -->

    <!-- START: Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- END: Icons -->

    <!-- START: Base Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
    <!-- END: Base Styles -->

    @stack('styles')
</head>
<body class="bg-slate-50 text-gray-800 antialiased">
<!-- END: Head -->

    <!-- START: Main Content Slot -->
    <main>
        @yield('content')
    </main>
    <!-- END: Main Content Slot -->

    <!-- START: Footer Include -->
    @include('partials.footer2')
    <!-- END: Footer Include -->

    <!-- START: Mobile Menu Script -->
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
    <!-- END: Mobile Menu Script -->

    @stack('scripts')

</body>
</html>
<!-- END: HTML Document Wrapper -->

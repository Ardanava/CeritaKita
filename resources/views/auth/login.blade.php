<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CeritaKita</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icon Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex">
        <!-- Kolom Kiri (Visual) -->
        <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-blue-600 to-teal-500 items-center justify-center p-12 text-white relative overflow-hidden">
            <div class="relative z-10 text-center">
                <h1 class="text-4xl font-bold mb-4">Selamat Datang Kembali</h1>
                <p class="text-lg text-blue-100">Masuki dunia penuh imajinasi dan temukan ribuan cerita yang menanti untuk dibaca.</p>
            </div>
             <!-- Efek Latar Belakang -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="a" patternUnits="userSpaceOnUse" width="40" height="40" patternTransform="scale(2) rotate(45)"><rect x="0" y="0" width="100%" height="100%" fill="none"/><path d="M1-5l-10 10m20 0L11 5M-9 15l10 10m0-20L11 5" stroke-width="2" stroke="white"/></pattern></defs><rect width="100%" height="100%" fill="url(#a)"/></svg>
            </div>
        </div>

        <!-- Kolom Kanan (Formulir) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4">
             <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 space-y-4 sm:space-y-6">
                    <!-- Header -->
                    <div class="text-center">
                        {{-- Menggunakan route('home') alih-alih index.html --}}
                        <a href="{{ route('home') }}" class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 text-transparent bg-clip-text">
                            CeritaKita
                        </a>
                        <p class="mt-1 text-gray-600">Masuk untuk melanjutkan petualanganmu.</p>
                    </div>

                    <!-- Form Login -->
                     @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">{{ $errors->first('email') }}</span>
                        </div>
                    @endif
                    {{-- Menambahkan method, action, dan @csrf --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6">
                        @csrf

                        <!-- Input Email -->
                        <div>
                            <label for="email" class="text-sm font-semibold text-gray-700">Alamat Email</label>
                            <div class="mt-1 relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fa-solid fa-envelope text-gray-400"></i>
                                </span>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div>
                            <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                            <div class="mt-1 relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fa-solid fa-lock text-gray-400"></i>
                                </span>
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                        
                        

                        <!-- Tombol Submit -->
                        <div>
                            <button type="submit"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-transform hover:scale-105">
                                Masuk
                            </button>
                        </div>
                    </form>

                    
                </div>
            </div>
        </div>
    </div>

</body>
</html>
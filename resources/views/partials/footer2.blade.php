<!-- Footer -->
<footer class="bg-gray-800 text-white ">
    <div class="container mx-auto px-4 lg:px-8 py-10 text-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="text-2xl font-bold">
            CeritaKita
        </a>
        <p class="mt-4 text-gray-400">Tempat di mana setiap cerita menemukan rumahnya.</p>
        
        <!-- Link Footer (Placeholder) -->
        <div class="mt-6">
            <a href="#" class="text-gray-400 hover:text-white mx-3">Tentang Kami</a>
            <a href="#" class="text-gray-400 hover:text-white mx-3">Kontak</a>
            <a href="#" class="text-gray-400 hover:text-white mx-3">Ketentuan Layanan</a>
            <a href="{{ route('about.developer') }}" class="text-gray-400 hover:text-white mx-3">CV Developer</a>
        </div>
        
        <p class="mt-8 text-sm text-gray-500">&copy; {{ date('Y') }} CeritaKita. Semua Hak Cipta Dilindungi.</p>
    </div>
</footer>
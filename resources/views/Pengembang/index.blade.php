@extends('layouts.app')

@section('title', 'Web Ini Dikembangkan Oleh')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-10">
  <div class="max-w-5xl mx-auto">

    {{-- Header Page --}}
    <div class="mb-8">
      <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-gray-900">
        Web Ini Dikembangkan Oleh
      </h1>
      <p class="text-gray-600 mt-1">Profil singkat pengembang aplikasi ini.</p>
    </div>

    {{-- Kartu Profil Utama --}}
    <section class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
      <div class="p-6 sm:p-8">
        <div class="flex flex-col md:flex-row gap-6 md:gap-8">

          {{-- Foto --}}
          <div class="shrink-0">
            {{-- Ganti path sesuai lokasi file gambar --}}
            <img
              src="{{ asset('images/tttt.png') }}"
              alt="Foto Profil Galih Nulhakim"
              class="h-40 w-32 rounded-lg object-cover ring-1 ring-gray-200 shadow-sm"
            >
          </div>

          {{-- Identitas & Kontak --}}
          <div class="flex-1 min-w-0">
            <div class="flex items-center flex-wrap gap-2">
              <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Galih Nulhakim</h2>
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                Web Developer
              </span>
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                Software Engineer
              </span>
            </div>

            <p class="text-gray-600 mt-2">
              Kuningan, Jawa Barat, Indonesia
            </p>

            <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
              <a href="tel:+6283106563884" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                <i class="fa-solid fa-phone"></i> +62 831-0656-3884
              </a>
              <a href="mailto:galihnulhakim1604@gmail.com" class="inline-flex items-center gap-2 text-blue-600 hover:underline break-all">
                <i class="fa-solid fa-envelope"></i> galihnulhakim1604@gmail.com
              </a>
              <a href="http://linkedin.com/in/galih-nulhakim-378056228" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                <i class="fa-brands fa-linkedin"></i> LinkedIn
              </a>
              <a href="http://github.com/ardanava" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                <i class="fa-brands fa-github"></i> GitHub
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Ringkasan --}}
    <section class="mt-8 bg-white rounded-xl shadow-md border border-gray-200">
      <div class="p-6 sm:p-8">
        <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2 mb-4">Ringkasan Profesional</h3>
        <p class="text-gray-700 leading-relaxed">
          Software Engineer lulusan Universitas Islam Al-ihya Kuningan Prodi Teknik Informatika dengan IPK 3.34, memiliki spesialisasi dalam pengembangan aplikasi web dan desktop. Berpengalaman menangani proyek dari tahap konsepsi hingga implementasi, termasuk proyek digitalisasi untuk instansi pemerintah. Mahir dalam PHP, JavaScript dan SQL, dengan rekam jejak membangun solusi yang efisien dan skalabel untuk berbagai kebutuhan bisnis.
        </p>
      </div>
    </section>

    {{-- Pengalaman --}}
    <section class="mt-8 bg-white rounded-xl shadow-md border border-gray-200">
      <div class="p-6 sm:p-8">
        <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2 mb-4">Pengalaman Profesional</h3>

        <div class="space-y-6">
          <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
              <h4 class="font-semibold text-gray-900">Freelance Programmer</h4>
              <span class="text-sm text-gray-600">Juli 2020 – September 2021</span>
            </div>
            <p class="text-gray-700">Berbagai Klien • Kuningan, Jawa Barat</p>
            <ul class="list-disc list-inside mt-2 text-gray-700 space-y-1">
              <li><strong>Diskominfo Kab. Kuningan:</strong> Membangun 32 situs profil kecamatan (WordPress), selesai 2 minggu lebih cepat dari jadwal.</li>
              <li><strong>LPK Kilat:</strong> Aplikasi web administrasi (CodeIgniter), efisiensi proses naik ±40%.</li>
              <li><strong>Koperasi Lokal:</strong> Aplikasi desktop manajemen keuangan (Java + SQL), akurasi pelaporan hingga 98%.</li>
            </ul>
          </div>

          <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
              <h4 class="font-semibold text-gray-900">Sales Administrator</h4>
              <span class="text-sm text-gray-600">November 2021 – Mei 2023</span>
            </div>
            <p class="text-gray-700">Delora Store • Kuningan, Jawa Barat</p>
            <ul class="list-disc list-inside mt-2 text-gray-700 space-y-1">
              <li>Memproses 100+ pesanan harian (akurasi 99%) pada sistem internal.</li>
              <li>Menyusun laporan penjualan bulanan berbasis data untuk keputusan strategis.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    {{-- Proyek Teknis --}}
    <section class="mt-8 bg-white rounded-xl shadow-md border border-gray-200">
      <div class="p-6 sm:p-8">
        <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2 mb-4">Proyek Teknis</h3>

        <div class="grid md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-gray-200 p-4">
            <div class="inline-flex items-center gap-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-200 mb-2">
              <i class="fa-solid fa-diagram-project"></i> Web GIS
            </div>
            <h4 class="font-semibold text-gray-900">Sistem Informasi Persebaran Tanaman Hortikultura</h4>
            <p class="text-gray-700 mt-1">
              Sistem pemetaan geografis interaktif. <strong>Teknologi:</strong> PHP, MySQL, JavaScript (Vanilla JS).
            </p>
          </div>

          <div class="rounded-lg border border-gray-200 p-4">
            <div class="inline-flex items-center gap-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 mb-2">
              <i class="fa-solid fa-car"></i> Computer Vision
            </div>
            <h4 class="font-semibold text-gray-900">Car Detection (Deteksi Mobil)</h4>
            <p class="text-gray-700 mt-1">
              Model deteksi objek real-time untuk melacak kendaraan. <strong>Teknologi:</strong> Python, TensorFlow, Arduino.
            </p>
          </div>
        </div>
      </div>
    </section>

    {{-- Skill --}}
    <section class="mt-8 bg-white rounded-xl shadow-md border border-gray-200">
      <div class="p-6 sm:p-8">
        <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2 mb-4">Kompetensi Teknis</h3>
        <div class="flex flex-wrap -m-1">
          @foreach ([
            'PHP (CodeIgniter & Laravel)','Mikrotik','JavaScript','SQL (MySQL)','Java','Python',
            'WordPress','Git & GitHub','Web Development','Problem Solving'
          ] as $skill)
            <span class="m-1 px-3 py-1 rounded-full text-sm font-medium
              {{ in_array($skill,['Web Development','Problem Solving']) ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
              {{ $skill }}
            </span>
          @endforeach
        </div>
      </div>
    </section>

    {{-- Pendidikan --}}
    <section class="mt-8 bg-white rounded-xl shadow-md border border-gray-200">
      <div class="p-6 sm:p-8">
        <h3 class="font-bold text-lg text-slate-800 border-b border-slate-200 pb-2 mb-4">Latar Belakang Pendidikan</h3>
        <div class="space-y-3">
          <div>
            <h4 class="font-semibold text-gray-900">Universitas Islam Al-Ihya Kuningan</h4>
            <p class="text-gray-700">S.Kom, Teknik Informatika • IPK 3.34 • 2021–2024</p>
          </div>
        </div>
      </div>
    </section>

  </div>
</div>
@endsection

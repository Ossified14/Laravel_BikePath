<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bikepath - Sistem Navigasi dan Pelaporan Pesepeda</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-xl shadow-lg shadow-emerald-500/20">
                    🚴
                </div>
                <span class="font-extrabold text-2xl tracking-tight text-white">
                    Bike<span class="text-emerald-400">path</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm transition">
                            Buka Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 font-semibold text-sm transition">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm shadow-md shadow-emerald-500/20 transition">
                                Daftar Gratis
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 lg:py-28">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-xs font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Navigasi & Pelaporan Pesepeda Kota Terintegrasi
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white max-w-4xl mx-auto leading-tight">
                Gowes Lebih Aman dengan <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Rute Cerdas</span> & Peringatan Bahaya Realtime
            </h1>

            <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Aplikasi Bikepath memadukan peta interaktif open-source (Leaflet & OpenStreetMap) dengan sistem pelaporan halangan jalan via AJAX yang diverifikasi oleh administrator demi keselamatan setiap pesepeda.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ route('map.index') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold text-base shadow-xl shadow-emerald-500/25 transition">
                        Buka Peta & Rute Sepeda
                    </a>
                    <a href="{{ route('reports.index') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-base border border-slate-700 transition">
                        Laporkan Halangan Jalan (AJAX)
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold text-base shadow-xl shadow-emerald-500/25 transition">
                        Daftar Sebagai Rider Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-base border border-slate-700 transition">
                        Masuk ke Akun
                    </a>
                @endauth
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 pt-10 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">100%</div>
                    <div class="text-xs text-slate-400 mt-1">Open-Source & Gratis</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">OSRM</div>
                    <div class="text-xs text-slate-400 mt-1">Mesin Perutean Sepeda</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $verifiedReportsCount ?? 0 }}+</div>
                    <div class="text-xs text-slate-400 mt-1">Halangan Terverifikasi</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $totalRiders ?? 0 }}+</div>
                    <div class="text-xs text-slate-400 mt-1">Komunitas Pesepeda</div>
                </div>
            </div>

        </div>
    </section>

    <!-- Key Modules Section -->
    <section class="py-16 bg-slate-950 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-white">Arsitektur Modul HMVC & Fitur Utama</h2>
                <p class="text-slate-400 text-sm mt-2">Dibangun dengan standar modularitas nwidart/laravel-modules & autentikasi Laravel Breeze</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Modul 1 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-2xl mb-4 font-bold">
                        🗺️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Modul Map & Routing</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Navigasi komprehensif menggunakan Leaflet.js, tile OpenStreetMap & CyclOSM, serta perhitungan rute otomatis dan petunjuk arah belokan dari OSRM.
                    </p>
                </div>

                <!-- Modul 2 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl mb-4 font-bold">
                        🚨
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Modul Report (CRUD AJAX)</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pelaporan jalan rusak, pohon tumbang, atau banjir secara instan dengan unggah foto FormData via AJAX dan verifikasi status oleh Admin.
                    </p>
                </div>

                <!-- Modul 3 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-2xl mb-4 font-bold">
                        👥
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Modul UserManagement</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pengelolaan seluruh data pengguna (CRUD Synchronous) khusus Admin dengan kontrol hak akses berbasis Role Middleware (Admin & Rider).
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 bg-slate-950 border-t border-slate-900 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Bikepath — Sistem Navigasi dan Pelaporan Pesepeda. Ditenagai oleh Laravel 13, Leaflet.js & nwidart/laravel-modules.
    </footer>

</body>
</html>

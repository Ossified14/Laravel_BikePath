<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Bikepath') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat datang kembali, <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>!
                </p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' }}">
                    Role: {{ auth()->user()->role }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            {{ auth()->user()->isAdmin() ? 'Total Pengguna' : 'Laporan Saya' }}
                        </span>
                        <h3 class="text-3xl font-extrabold text-gray-900 mt-1">
                            {{ auth()->user()->isAdmin() ? $totalUsers : $myReportsCount }}
                        </h3>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">
                            {{ auth()->user()->isAdmin() ? 'Akun Admin & Rider' : 'Total laporan Anda kirim' }}
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        👥
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Laporan</span>
                        <h3 class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalReports }}</h3>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">Seluruh bahaya jalan</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        📋
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Telah Terverifikasi</span>
                        <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $verifiedReports }}</h3>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">Ditampilkan di peta</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl font-bold">
                        ✅
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menunggu Verifikasi</span>
                        <h3 class="text-3xl font-extrabold text-amber-500 mt-1">{{ $pendingReports }}</h3>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">Status pending</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        ⏳
                    </div>
                </div>
            </div>

            <!-- Main Feature Quick Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Peta & Navigasi -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl mb-4">
                            🗺️
                        </div>
                        <h3 class="text-xl font-bold mb-2">Peta & Navigasi Rute</h3>
                        <p class="text-xs opacity-90 leading-relaxed mb-6">
                            Akses peta jalur sepeda open-source (Leaflet & OpenStreetMap) dengan perhitungan rute otomatis dari OSRM dan pemantauan marker halangan jalan.
                        </p>
                    </div>
                    <a href="{{ route('map.index') }}" class="w-full py-2.5 px-4 bg-white hover:bg-gray-100 text-emerald-800 font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-1.5">
                        Buka Peta Navigasi &rarr;
                    </a>
                </div>

                <!-- Card 2: Pelaporan Halangan AJAX -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl mb-4">
                            🚨
                        </div>
                        <h3 class="text-xl font-bold mb-2">Pelaporan Bahaya (AJAX)</h3>
                        <p class="text-xs opacity-90 leading-relaxed mb-6">
                            Kirim laporan kerusakan jalan atau rintangan dengan unggah foto instan tanpa reload halaman, serta pelacakan status verifikasi realtime.
                        </p>
                    </div>
                    <a href="{{ route('reports.index') }}" class="w-full py-2.5 px-4 bg-white hover:bg-gray-100 text-blue-800 font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-1.5">
                        {{ auth()->user()->isAdmin() ? 'Verifikasi Laporan' : 'Kelola Laporan Saya' }} &rarr;
                    </a>
                </div>

                <!-- Card 3: Manajemen Pengguna (Admin) or Profil (Rider) -->
                @if(auth()->user()->isAdmin())
                    <div class="bg-gradient-to-br from-purple-700 to-violet-800 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl mb-4">
                                ⚙️
                            </div>
                            <h3 class="text-xl font-bold mb-2">Manajemen Pengguna</h3>
                            <p class="text-xs opacity-90 leading-relaxed mb-6">
                                Pengelolaan akun pengguna, pengaturan hak akses Admin dan Rider, serta pencarian pengguna secara Synchronous CRUD.
                            </p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="w-full py-2.5 px-4 bg-white hover:bg-gray-100 text-purple-800 font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-1.5">
                            Kelola Akun Pengguna &rarr;
                        </a>
                    </div>
                @else
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl mb-4">
                                👤
                            </div>
                            <h3 class="text-xl font-bold mb-2">Profil Pesepeda</h3>
                            <p class="text-xs opacity-90 leading-relaxed mb-6">
                                Perbarui data pribadi Anda, nomor WhatsApp aktif, dan alamat tempat tinggal untuk kelancaran pelaporan.
                            </p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="w-full py-2.5 px-4 bg-white hover:bg-gray-100 text-gray-900 font-bold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-1.5">
                            Edit Profil Saya &rarr;
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

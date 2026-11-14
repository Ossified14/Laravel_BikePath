<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Pelaporan Halangan Jalur Sepeda (Report Module)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ auth()->user()->isAdmin() ? 'Panel Admin: Verifikasi dan pantau laporan bahaya jalan raya dari pesepeda.' : 'Panel Rider: Laporkan jalan berlubang, pohon tumbang, atau bahaya lainnya via AJAX.' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="btnOpenCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Laporan Bahaya
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Toast Notification Container -->
            <div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

            <!-- Role Information Banner -->
            <div class="bg-gradient-to-r {{ auth()->user()->isAdmin() ? 'from-purple-900 to-indigo-800 text-white' : 'from-emerald-800 to-teal-700 text-white' }} p-5 rounded-xl shadow-md">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-white/10 backdrop-blur flex items-center justify-center text-2xl font-bold">
                            @if(auth()->user()->isAdmin())
                                🛡️
                            @else
                                🚴
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-white/20">
                                    Role: {{ auth()->user()->role }}
                                </span>
                                <span class="text-xs opacity-75">Login sebagai: {{ auth()->user()->name }}</span>
                            </div>
                            <h3 class="text-lg font-bold mt-0.5">
                                @if(auth()->user()->isAdmin())
                                    Mode Manajemen & Verifikasi Admin
                                @else
                                    Mode Pelaporan Rider (AJAX CRUD)
                                @endif
                            </h3>
                            <p class="text-xs opacity-90 mt-1 max-w-2xl">
                                @if(auth()->user()->isAdmin())
                                    Anda dapat melihat semua laporan pengguna dan melakukan verifikasi status (Verified/Rejected).
                                @else
                                    Unggah foto halangan via AJAX (FormData). Laporan berstatus 'Pending' dapat diedit atau dihapus.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('map.index') }}" class="px-3.5 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-xs font-semibold backdrop-blur transition flex items-center">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            Buka Peta
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label for="filterSearch" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Pencarian Laporan</label>
                        <div class="relative">
                            <input type="text" id="filterSearch" placeholder="Cari jenis bahaya atau deskripsi halangan..." class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="filterStatus" class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Verifikasi</label>
                        <select id="filterStatus" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Semua Status --</option>
                            <option value="pending">Pending (Menunggu)</option>
                            <option value="verified">Verified (Diverifikasi)</option>
                            <option value="rejected">Rejected (Ditolak)</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="button" id="btnRefreshData" class="w-full py-2 px-4 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition flex items-center justify-center">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Muat Ulang AJAX
                        </button>
                    </div>
                </div>
            </div>

            <!-- AJAX Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h4 class="font-bold text-gray-800 text-sm">Daftar Data Laporan Halangan (Live AJAX Table)</h4>
                    </div>
                    <span id="reportCountBadge" class="text-xs font-semibold px-2.5 py-1 rounded bg-gray-100 text-gray-700">0 Data</span>
                </div>

                <div class="overflow-x-auto relative min-h-[250px]">
                    <!-- Loading Spinner Overlay -->
                    <div id="tableLoading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10 hidden">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-emerald-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-medium text-gray-600">Mengambil data dari server...</span>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3.5">ID</th>
                                <th class="px-5 py-3.5">Foto Bukti</th>
                                <th class="px-5 py-3.5">Jenis Bahaya & Deskripsi</th>
                                <th class="px-5 py-3.5">Pelapor</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody" class="divide-y divide-gray-100">
                            <!-- Populated dynamically via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CREATE REPORT (AJAX MULTIPART/FORM-DATA WITH FILE UPLOAD)        -->
    <!-- ========================================================================= -->
    <div id="createModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-emerald-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-bold">Laporkan Halangan Jalan Baru</h3>
                </div>
                <button type="button" class="btn-close-modal text-white/80 hover:text-white text-2xl font-bold leading-none">&times;</button>
            </div>

            <form id="createReportForm" enctype="multipart/form-data" class="p-6 space-y-4">
                <!-- Validation Error Box -->
                <div id="createErrorAlert" class="p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-xs hidden">
                    <ul id="createErrorList" class="list-disc pl-4 space-y-1"></ul>
                </div>

                <!-- Hazard Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Halangan / Bahaya *</label>
                    <select name="hazard_type" id="createHazardType" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">-- Pilih Jenis Bahaya --</option>
                        <option value="Jalan Rusak / Berlubang">Jalan Rusak / Berlubang</option>
                        <option value="Pohon Tumbang / Ranting Besar">Pohon Tumbang / Ranting Besar</option>
                        <option value="Genangan Air Tinggi">Genangan Air Tinggi / Banjir</option>
                        <option value="Sisa Material Proyek">Sisa Material Proyek / Galian</option>
                        <option value="Jalan Licin / Tumpahan Oli">Jalan Licin / Tumpahan Solar & Oli</option>
                        <option value="Tutup Selokan Hilang">Tutup Selokan Hilang / Rusak</option>
                        <option value="Parkir Liar di Jalur Sepeda">Parkir Liar di Jalur Sepeda</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deskripsi Kondisi / Lokasi Halangan *</label>
                    <textarea name="description" id="createDescription" rows="3" required placeholder="Jelaskan lokasi patokan, kondisi halangan, dan tingkat keparahannya..." class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <!-- Photo Upload with Live Preview (AJAX File Upload) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Foto Bukti Halangan (Wajib Upload AJAX) *</label>
                    <input type="file" name="hazard_photo" id="createHazardPhoto" accept="image/*" required class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    
                    <!-- Image Preview Container -->
                    <div id="createPhotoPreviewContainer" class="mt-3 relative rounded-lg overflow-hidden border border-gray-200 w-48 h-32 bg-gray-50 hidden">
                        <img id="createPhotoPreview" src="#" alt="Preview Foto" class="w-full h-full object-cover">
                        <button type="button" id="btnRemoveCreatePhoto" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Upload Progress Bar -->
                <div id="createProgressBarContainer" class="hidden">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Mengunggah data & foto via AJAX...</span>
                        <span id="createProgressPercentage">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div id="createProgressBar" class="bg-emerald-600 h-2 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-close-modal px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitCreate" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow transition flex items-center">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Kirim Laporan (AJAX)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT REPORT (AJAX UPDATE)                                       -->
    <!-- ========================================================================= -->
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-amber-500 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <h3 class="text-lg font-bold">Edit Laporan Halangan #<span id="editReportIdText"></span></h3>
                </div>
                <button type="button" class="btn-close-modal text-white/80 hover:text-white text-2xl font-bold leading-none">&times;</button>
            </div>

            <form id="editReportForm" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" id="editReportId" name="report_id">

                <div id="editErrorAlert" class="p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-xs hidden">
                    <ul id="editErrorList" class="list-disc pl-4 space-y-1"></ul>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Halangan / Bahaya *</label>
                    <select name="hazard_type" id="editHazardType" required class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="Jalan Rusak / Berlubang">Jalan Rusak / Berlubang</option>
                        <option value="Pohon Tumbang / Ranting Besar">Pohon Tumbang / Ranting Besar</option>
                        <option value="Genangan Air Tinggi">Genangan Air Tinggi / Banjir</option>
                        <option value="Sisa Material Proyek">Sisa Material Proyek / Galian</option>
                        <option value="Jalan Licin / Tumpahan Oli">Jalan Licin / Tumpahan Solar & Oli</option>
                        <option value="Tutup Selokan Hilang">Tutup Selokan Hilang / Rusak</option>
                        <option value="Parkir Liar di Jalur Sepeda">Parkir Liar di Jalur Sepeda</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deskripsi Kondisi *</label>
                    <textarea name="description" id="editDescription" rows="3" required class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Ganti Foto Bukti (Opsional)</label>
                    <input type="file" name="hazard_photo" id="editHazardPhoto" accept="image/*" class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    
                    <div class="mt-2 flex items-center gap-3">
                        <div class="w-24 h-16 rounded border overflow-hidden bg-gray-50">
                            <img id="editCurrentPhotoPreview" src="#" alt="Foto Saat Ini" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs text-gray-500">Foto saat ini (akan diganti jika memilih file baru)</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-close-modal px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitEdit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-lg shadow transition">
                        Simpan Perubahan (AJAX)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: DETAIL REPORT (AJAX VIEW)                                       -->
    <!-- ========================================================================= -->
    <div id="detailModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-gray-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-bold">Rincian Laporan #<span id="detailId"></span></h3>
                </div>
                <button type="button" class="btn-close-modal text-white/80 hover:text-white text-2xl font-bold leading-none">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <!-- Large Photo Preview -->
                <div class="rounded-xl overflow-hidden bg-gray-900 max-h-72 flex items-center justify-center relative group cursor-pointer" id="detailPhotoContainer">
                    <img id="detailPhoto" src="#" alt="Foto Halangan" class="w-full h-72 object-cover transition duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-semibold">
                        <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        Klik untuk memperbesar (Full Lightbox)
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-400 uppercase font-semibold">Jenis Halangan</span>
                        <p id="detailHazardType" class="text-sm font-bold text-gray-900 mt-0.5"></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-400 uppercase font-semibold">Status Verifikasi</span>
                        <div id="detailStatusBadge" class="mt-1"></div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-400 uppercase font-semibold">Pelapor (Rider)</span>
                        <p id="detailReporter" class="text-sm font-semibold text-gray-900 mt-0.5"></p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-400 uppercase font-semibold">Tanggal Kirim</span>
                        <p id="detailDate" class="text-sm text-gray-700 mt-0.5"></p>
                    </div>
                </div>

                <div>
                    <span class="text-xs text-gray-500 uppercase font-semibold">Deskripsi Kejadian</span>
                    <p id="detailDescription" class="mt-1 text-sm text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg"></p>
                </div>

                <!-- Admin Notes Section -->
                <div id="detailAdminNotesSection" class="p-3 bg-purple-50 border border-purple-100 rounded-lg hidden">
                    <span class="text-xs text-purple-700 uppercase font-bold">Catatan Verifikasi Admin</span>
                    <p id="detailAdminNotes" class="mt-1 text-xs text-purple-900 leading-relaxed"></p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" class="btn-close-modal px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: ADMIN VERIFICATION (AJAX PATCH)                                 -->
    <!-- ========================================================================= -->
    <div id="verifyModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-purple-700 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <h3 class="text-lg font-bold">Verifikasi Laporan Halangan #<span id="verifyReportIdText"></span></h3>
                </div>
                <button type="button" class="btn-close-modal text-white/80 hover:text-white text-2xl font-bold leading-none">&times;</button>
            </div>

            <form id="verifyReportForm" class="p-6 space-y-4">
                <input type="hidden" id="verifyReportId">

                <div class="p-3 bg-gray-50 rounded-lg text-xs">
                    <div class="font-semibold text-gray-800" id="verifyHazardTypeSummary"></div>
                    <div class="text-gray-500 mt-1 line-clamp-2" id="verifyDescriptionSummary"></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Pilih Status Verifikasi *</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex flex-col items-center p-3 rounded-lg border-2 border-emerald-400 bg-emerald-50/50 cursor-pointer hover:bg-emerald-100/50 transition">
                            <input type="radio" name="verification_status" value="verified" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold text-emerald-800 mt-1">Verified</span>
                            <span class="text-[10px] text-emerald-600 text-center">Diverifikasi</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-lg border-2 border-red-400 bg-red-50/50 cursor-pointer hover:bg-red-100/50 transition">
                            <input type="radio" name="verification_status" value="rejected" class="text-red-600 focus:ring-red-500">
                            <span class="text-xs font-bold text-red-800 mt-1">Rejected</span>
                            <span class="text-[10px] text-red-600 text-center">Ditolak / Selesai</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-lg border-2 border-amber-400 bg-amber-50/50 cursor-pointer hover:bg-amber-100/50 transition">
                            <input type="radio" name="verification_status" value="pending" class="text-amber-600 focus:ring-amber-500">
                            <span class="text-xs font-bold text-amber-800 mt-1">Pending</span>
                            <span class="text-[10px] text-amber-600 text-center">Tinjau Kembali</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Tindak Lanjut Admin (Opsional)</label>
                    <textarea id="verifyAdminNotes" rows="3" placeholder="Contoh: Petugas Dishub / PPSU telah dikirim untuk menangani bahaya jalan..." class="w-full text-sm rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-close-modal px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitVerify" class="px-5 py-2 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-sm rounded-lg shadow transition">
                        Simpan Verifikasi (AJAX)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: FULL-SIZE PHOTO LIGHTBOX                                         -->
    <!-- ========================================================================= -->
    <div id="lightboxModal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 hidden" onclick="closeLightbox()">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="#" alt="Full Preview" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl object-contain">
            <div class="mt-3 flex items-center justify-between w-full px-2 text-white">
                <span id="lightboxCaption" class="text-xs opacity-80"></span>
                <button type="button" onclick="closeLightbox()" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded transition">Tutup (ESC)</button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- JAVASCRIPT: FULL AJAX LOGIC & EVENT HANDLERS                              -->
    <!-- ========================================================================= -->
    <script>
        // Global variables & CSRF Setup
        const csrfToken = '{{ csrf_token() }}';
        const currentUserId = {{ auth()->id() }};
        const isUserAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
        let currentReportsList = [];

        // DOM elements
        const tableBody = document.getElementById('reportsTableBody');
        const tableLoading = document.getElementById('tableLoading');
        const reportCountBadge = document.getElementById('reportCountBadge');
        const filterSearch = document.getElementById('filterSearch');
        const filterStatus = document.getElementById('filterStatus');
        const btnRefreshData = document.getElementById('btnRefreshData');

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-600 text-white' : (type === 'error' ? 'bg-red-600 text-white' : 'bg-gray-800 text-white');
            
            toast.className = `p-4 rounded-xl shadow-lg text-xs font-semibold flex items-center gap-2 transform transition-all duration-300 pointer-events-auto ${bgClass} translate-y-2 opacity-0`;
            toast.innerHTML = `
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>${message}</span>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // =========================================================================
        // AJAX 1: FETCH DATA & RENDER TABLE
        // =========================================================================
        async function fetchReportsData() {
            tableLoading.classList.remove('hidden');

            const params = new URLSearchParams();
            if (filterSearch.value.trim()) params.append('search', filterSearch.value.trim());
            if (filterStatus.value) params.append('status', filterStatus.value);

            try {
                const response = await fetch(`{{ route('reports.data') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (result.success) {
                    currentReportsList = result.data;
                    renderTable(result.data);
                } else {
                    showToast('Gagal memuat data laporan: ' + (result.message || 'Error server'), 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan saat mengambil data laporan.', 'error');
            } finally {
                tableLoading.classList.add('hidden');
            }
        }

        function renderTable(reports) {
            reportCountBadge.textContent = `${reports.length} Data`;

            if (reports.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-semibold">Belum ada laporan halangan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Gunakan tombol 'Buat Laporan Bahaya' di atas untuk menambah laporan baru.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = reports.map(report => {
                // Status badge
                let statusBadge = '';
                if (report.verification_status === 'verified') {
                    statusBadge = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Verified
                    </span>`;
                } else if (report.verification_status === 'rejected') {
                    statusBadge = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Rejected
                    </span>`;
                } else {
                    statusBadge = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                        <svg class="w-3 h-3 me-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Pending
                    </span>`;
                }

                // Action buttons based on role & status
                // Rider can only edit/delete if pending
                const canEditDelete = isUserAdmin || (report.user_id === currentUserId && report.verification_status === 'pending');

                let actionHtml = `
                    <div class="inline-flex items-center space-x-1.5">
                        <button type="button" onclick="openDetailModal(${report.id})" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition" title="Lihat Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                `;

                if (canEditDelete) {
                    actionHtml += `
                        <button type="button" onclick="openEditModal(${report.id})" class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-md transition" title="Edit Laporan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button type="button" onclick="confirmDeleteReport(${report.id})" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition" title="Hapus Laporan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    `;
                }

                // If admin, show verification button
                if (isUserAdmin) {
                    actionHtml += `
                        <button type="button" onclick="openVerifyModal(${report.id})" class="p-1.5 text-purple-700 hover:text-purple-900 hover:bg-purple-50 rounded-md transition" title="Ubah Verifikasi (Admin)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </button>
                    `;
                }

                actionHtml += `</div>`;

                // Safe photo url
                const photoUrl = report.photo_url || (report.hazard_photo ? `/storage/${report.hazard_photo}` : '#');

                return `
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-5 py-3.5 font-mono text-gray-500 text-xs">#${report.id}</td>
                        <td class="px-5 py-3.5">
                            <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 cursor-pointer shadow-xs hover:opacity-90 transition relative group" onclick="openLightbox('${photoUrl}', '${escapeHtml(report.hazard_type)}')">
                                <img src="${photoUrl}" alt="Thumbnail" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 max-w-sm">
                            <div class="font-bold text-gray-900">${escapeHtml(report.hazard_type)}</div>
                            <div class="text-xs text-gray-500 line-clamp-2 mt-0.5">${escapeHtml(report.description)}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-xs font-semibold text-gray-900">${report.user ? escapeHtml(report.user.name) : '-'}</div>
                            <div class="text-[11px] text-gray-400">${report.user ? escapeHtml(report.user.role) : ''}</div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            ${actionHtml}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // =========================================================================
        // AJAX 2: CREATE REPORT (AJAX MULTIPART FILE UPLOAD)
        // =========================================================================
        const createModal = document.getElementById('createModal');
        const createForm = document.getElementById('createReportForm');
        const btnOpenCreateModal = document.getElementById('btnOpenCreateModal');
        const createPhotoInput = document.getElementById('createHazardPhoto');
        const createPhotoPreviewContainer = document.getElementById('createPhotoPreviewContainer');
        const createPhotoPreview = document.getElementById('createPhotoPreview');
        const btnRemoveCreatePhoto = document.getElementById('btnRemoveCreatePhoto');
        const createProgressBarContainer = document.getElementById('createProgressBarContainer');
        const createProgressBar = document.getElementById('createProgressBar');
        const createProgressPercentage = document.getElementById('createProgressPercentage');
        const btnSubmitCreate = document.getElementById('btnSubmitCreate');
        const createErrorAlert = document.getElementById('createErrorAlert');
        const createErrorList = document.getElementById('createErrorList');

        btnOpenCreateModal.addEventListener('click', () => {
            createForm.reset();
            createPhotoPreviewContainer.classList.add('hidden');
            createProgressBarContainer.classList.add('hidden');
            createErrorAlert.classList.add('hidden');
            createModal.classList.remove('hidden');
        });

        // Live Photo Preview
        createPhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    createPhotoPreview.src = evt.target.result;
                    createPhotoPreviewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        btnRemoveCreatePhoto.addEventListener('click', () => {
            createPhotoInput.value = '';
            createPhotoPreview.src = '#';
            createPhotoPreviewContainer.classList.add('hidden');
        });

        // Submit Create Form via AJAX XMLHttpRequest (to track progress)
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            createErrorAlert.classList.add('hidden');
            createErrorList.innerHTML = '';

            const formData = new FormData(createForm);

            btnSubmitCreate.disabled = true;
            createProgressBarContainer.classList.remove('hidden');
            createProgressBar.style.width = '0%';
            createProgressPercentage.textContent = '0%';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('reports.store') }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');

            // Upload progress
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    createProgressBar.style.width = percent + '%';
                    createProgressPercentage.textContent = percent + '%';
                }
            };

            xhr.onload = function() {
                btnSubmitCreate.disabled = false;
                if (xhr.status === 201 || xhr.status === 200) {
                    const res = JSON.parse(xhr.responseText);
                    showToast(res.message || 'Laporan berhasil dikirim via AJAX!');
                    createModal.classList.add('hidden');
                    fetchReportsData(); // Refresh live table
                } else {
                    createProgressBarContainer.classList.add('hidden');
                    try {
                        const errRes = JSON.parse(xhr.responseText);
                        if (errRes.errors) {
                            createErrorList.innerHTML = Object.values(errRes.errors)
                                .map(errs => `<li>${errs.join(', ')}</li>`)
                                .join('');
                            createErrorAlert.classList.remove('hidden');
                        } else {
                            createErrorList.innerHTML = `<li>${errRes.message || 'Terjadi kesalahan saat menyimpan.'}</li>`;
                            createErrorAlert.classList.remove('hidden');
                        }
                    } catch (parseErr) {
                        createErrorList.innerHTML = `<li>Terjadi kesalahan jaringan atau server (${xhr.status}).</li>`;
                        createErrorAlert.classList.remove('hidden');
                    }
                }
            };

            xhr.onerror = function() {
                btnSubmitCreate.disabled = false;
                createProgressBarContainer.classList.add('hidden');
                showToast('Terjadi kesalahan jaringan saat upload.', 'error');
            };

            xhr.send(formData);
        });

        // =========================================================================
        // AJAX 3: DETAIL REPORT MODAL
        // =========================================================================
        const detailModal = document.getElementById('detailModal');
        async function openDetailModal(id) {
            try {
                const response = await fetch(`/reports/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();

                if (res.success) {
                    const r = res.data;
                    document.getElementById('detailId').textContent = r.id;
                    document.getElementById('detailHazardType').textContent = r.hazard_type;
                    document.getElementById('detailReporter').textContent = r.user ? `${r.user.name} (${r.user.email})` : 'Anonim';
                    document.getElementById('detailDate').textContent = new Date(r.created_at).toLocaleString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });
                    document.getElementById('detailDescription').textContent = r.description;

                    const photoUrl = r.photo_url || `/storage/${r.hazard_photo}`;
                    document.getElementById('detailPhoto').src = photoUrl;
                    document.getElementById('detailPhotoContainer').onclick = () => openLightbox(photoUrl, r.hazard_type);

                    // Status badge
                    const badgeContainer = document.getElementById('detailStatusBadge');
                    if (r.verification_status === 'verified') {
                        badgeContainer.innerHTML = '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Verified (Disetujui)</span>';
                    } else if (r.verification_status === 'rejected') {
                        badgeContainer.innerHTML = '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rejected (Ditolak)</span>';
                    } else {
                        badgeContainer.innerHTML = '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Pending (Menunggu Verifikasi)</span>';
                    }

                    // Admin notes
                    const adminNotesSection = document.getElementById('detailAdminNotesSection');
                    if (r.admin_notes) {
                        document.getElementById('detailAdminNotes').textContent = r.admin_notes;
                        adminNotesSection.classList.remove('hidden');
                    } else {
                        adminNotesSection.classList.add('hidden');
                    }

                    detailModal.classList.remove('hidden');
                } else {
                    showToast(res.message || 'Gagal memuat rincian laporan.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', 'error');
            }
        }

        // =========================================================================
        // AJAX 4: EDIT REPORT MODAL
        // =========================================================================
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editReportForm');
        const editErrorAlert = document.getElementById('editErrorAlert');
        const editErrorList = document.getElementById('editErrorList');

        async function openEditModal(id) {
            editErrorAlert.classList.add('hidden');
            editForm.reset();

            try {
                const response = await fetch(`/reports/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();

                if (res.success) {
                    const r = res.data;
                    document.getElementById('editReportId').value = r.id;
                    document.getElementById('editReportIdText').textContent = r.id;
                    document.getElementById('editHazardType').value = r.hazard_type;
                    document.getElementById('editDescription').value = r.description;
                    document.getElementById('editCurrentPhotoPreview').src = r.photo_url || `/storage/${r.hazard_photo}`;

                    editModal.classList.remove('hidden');
                } else {
                    showToast(res.message || 'Tidak dapat mengedit laporan ini.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Gagal memuat data laporan untuk diedit.', 'error');
            }
        }

        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            editErrorAlert.classList.add('hidden');
            const id = document.getElementById('editReportId').value;
            const formData = new FormData(editForm);

            try {
                const response = await fetch(`/reports/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const res = await response.json();

                if (response.ok && res.success) {
                    showToast(res.message || 'Laporan berhasil diperbarui.');
                    editModal.classList.add('hidden');
                    fetchReportsData();
                } else {
                    if (res.errors) {
                        editErrorList.innerHTML = Object.values(res.errors)
                            .map(errs => `<li>${errs.join(', ')}</li>`)
                            .join('');
                        editErrorAlert.classList.remove('hidden');
                    } else {
                        editErrorList.innerHTML = `<li>${res.message || 'Gagal memperbarui laporan.'}</li>`;
                        editErrorAlert.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', 'error');
            }
        });

        // =========================================================================
        // AJAX 5: DELETE REPORT
        // =========================================================================
        async function confirmDeleteReport(id) {
            if (!confirm(`Apakah Anda yakin ingin menghapus laporan #${id}? Data tidak dapat dikembalikan.`)) {
                return;
            }

            try {
                const response = await fetch(`/reports/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const res = await response.json();

                if (response.ok && res.success) {
                    showToast(res.message || 'Laporan berhasil dihapus.');
                    fetchReportsData();
                } else {
                    showToast(res.message || 'Gagal menghapus laporan.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', 'error');
            }
        }

        // =========================================================================
        // AJAX 6: ADMIN VERIFY REPORT MODAL & ACTION
        // =========================================================================
        const verifyModal = document.getElementById('verifyModal');
        const verifyForm = document.getElementById('verifyReportForm');

        function openVerifyModal(id) {
            const report = currentReportsList.find(r => r.id === id);
            if (!report) return;

            document.getElementById('verifyReportId').value = report.id;
            document.getElementById('verifyReportIdText').textContent = report.id;
            document.getElementById('verifyHazardTypeSummary').textContent = report.hazard_type;
            document.getElementById('verifyDescriptionSummary').textContent = report.description;
            document.getElementById('verifyAdminNotes').value = report.admin_notes || '';

            // Set radio
            const radios = document.getElementsByName('verification_status');
            for (let r of radios) {
                r.checked = (r.value === report.verification_status);
            }

            verifyModal.classList.remove('hidden');
        }

        verifyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('verifyReportId').value;
            const selectedStatus = document.querySelector('input[name="verification_status"]:checked')?.value || 'verified';
            const notes = document.getElementById('verifyAdminNotes').value;

            try {
                const response = await fetch(`/reports/${id}/verify`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        verification_status: selectedStatus,
                        admin_notes: notes
                    })
                });
                const res = await response.json();

                if (response.ok && res.success) {
                    showToast(res.message || 'Status laporan berhasil diperbarui!');
                    verifyModal.classList.add('hidden');
                    fetchReportsData();
                } else {
                    showToast(res.message || 'Gagal mengubah status verifikasi.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', 'error');
            }
        });

        // =========================================================================
        // LIGHTBOX PREVIEW MODAL
        // =========================================================================
        const lightboxModal = document.getElementById('lightboxModal');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCaption = document.getElementById('lightboxCaption');

        function openLightbox(url, caption = '') {
            lightboxImage.src = url;
            lightboxCaption.textContent = caption;
            lightboxModal.classList.remove('hidden');
        }

        function closeLightbox() {
            lightboxModal.classList.add('hidden');
            lightboxImage.src = '#';
        }

        // Close modals on clicking backdrop or close buttons
        document.querySelectorAll('.btn-close-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                createModal.classList.add('hidden');
                editModal.classList.add('hidden');
                detailModal.classList.add('hidden');
                verifyModal.classList.add('hidden');
                closeLightbox();
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                createModal.classList.add('hidden');
                editModal.classList.add('hidden');
                detailModal.classList.add('hidden');
                verifyModal.classList.add('hidden');
                closeLightbox();
            }
        });

        // Helpers
        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }

        // Filter events
        let searchTimeout;
        filterSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(fetchReportsData, 400);
        });

        filterStatus.addEventListener('change', fetchReportsData);
        btnRefreshData.addEventListener('click', fetchReportsData);

        // Initial Data Fetch on Load
        document.addEventListener('DOMContentLoaded', fetchReportsData);
    </script>
</x-app-layout>

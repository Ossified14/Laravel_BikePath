<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail Pengguna: ') }} {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap akun dan riwayat aktivitas pelaporan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- User Profile Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 pb-6 border-b border-gray-100">
                    <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-3xl shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                            @if ($user->role === 'Admin')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    Admin
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    Rider
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Bergabung sejak: {{ $user->created_at->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Nomor Telepon</span>
                        <p class="text-gray-900 font-medium mt-1">{{ $user->phone_number ?? '-' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg md:col-span-2">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Alamat Domisili</span>
                        <p class="text-gray-900 font-medium mt-1">{{ $user->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- User's Hazard Reports -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-amber-500 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Riwayat Laporan Bahaya Pengguna ({{ $user->reports->count() }})
                </h4>

                @if ($user->reports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Jenis Bahaya</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Status Verifikasi</th>
                                    <th class="px-4 py-3">Tanggal Lapor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($user->reports as $report)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">#{{ $report->id }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $report->hazard_type }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">{{ $report->description }}</td>
                                        <td class="px-4 py-3">
                                            @if ($report->verification_status === 'verified')
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">Verified</span>
                                            @elseif ($report->verification_status === 'rejected')
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">Rejected</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Pengguna ini belum pernah mengirimkan laporan bahaya jalan.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Report\Models\Report;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@bikepath.test'],
            [
                'name' => 'Admin Utama Bikepath',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'phone_number' => '081234567890',
                'address' => 'Gedung Pengelola Transportasi Jakarta, Jl. Sudirman No. 1, Jakarta Pusat',
            ]
        );

        $admin2 = User::firstOrCreate(
            ['email' => 'eko.admin@bikepath.test'],
            [
                'name' => 'Eko Prasetyo (Admin Dishub)',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'phone_number' => '085612345678',
                'address' => 'Jl. Diponegoro No. 8, Menteng, Jakarta Pusat',
            ]
        );

        // 2. Rider Users
        $rider1 = User::firstOrCreate(
            ['email' => 'rider@bikepath.test'],
            [
                'name' => 'Budi Santoso (Rider)',
                'password' => Hash::make('password'),
                'role' => 'Rider',
                'phone_number' => '089876543210',
                'address' => 'Jl. Senayan Bawah No. 24, Kebayoran Baru, Jakarta Selatan',
            ]
        );

        $rider2 = User::firstOrCreate(
            ['email' => 'siti@bikepath.test'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'role' => 'Rider',
                'phone_number' => '081399887766',
                'address' => 'Jl. MH Thamrin No. 10, Jakarta Pusat',
            ]
        );

        $rider3 = User::firstOrCreate(
            ['email' => 'doni@bikepath.test'],
            [
                'name' => 'Doni Pratama',
                'password' => Hash::make('password'),
                'role' => 'Rider',
                'phone_number' => '081211223344',
                'address' => 'Jl. Gatot Subroto No. 55, Jakarta Selatan',
            ]
        );

        $rider4 = User::firstOrCreate(
            ['email' => 'rina@bikepath.test'],
            [
                'name' => 'Rina Wijaya',
                'password' => Hash::make('password'),
                'role' => 'Rider',
                'phone_number' => '081755667788',
                'address' => 'Jl. HR Rasuna Said No. 12, Kuningan, Jakarta Selatan',
            ]
        );

        // 3. Sample Hazard Reports (CRUD Sederhana tanpa koordinat)
        $reports = [
            [
                'user_id' => $rider1->id,
                'hazard_type' => 'Jalan Rusak / Berlubang',
                'description' => 'Lubang sedalam 10cm di jalur sepeda terproteksi Dukuh Atas, sangat berbahaya terutama malam hari.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'verified',
                'admin_notes' => 'Telah diverifikasi oleh Dishub. Petugas perbaikan jalan sudah dikonfirmasi.',
            ],
            [
                'user_id' => $rider1->id,
                'hazard_type' => 'Pohon Tumbang / Ranting Besar',
                'description' => 'Dahan pohon trembesi patah di Gatot Subroto dan menutupi sebagian badan jalur sepeda.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'pending',
                'admin_notes' => null,
            ],
            [
                'user_id' => $rider2->id,
                'hazard_type' => 'Genangan Air Tinggi',
                'description' => 'Genangan air setinggi 20cm pasca hujan lebat di Bundaran HI, menutupi marka pemisah jalur sepeda.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'verified',
                'admin_notes' => 'Drainase sedang dibersihkan oleh petugas PPSU.',
            ],
            [
                'user_id' => $rider3->id,
                'hazard_type' => 'Sisa Material Proyek',
                'description' => 'Tumpukan pasir dan batu koral proyek galian kabel berserakan di lintasan sepeda Rasuna Said.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'pending',
                'admin_notes' => null,
            ],
            [
                'user_id' => $rider4->id,
                'hazard_type' => 'Jalan Licin / Tumpahan Oli',
                'description' => 'Terdapat ceceran solar/oli di tikungan jalur sepeda arah flyover Kuningan, sangat licin.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'rejected',
                'admin_notes' => 'Setelah dicek oleh petugas lapangan, lokasi telah bersih dan aman dilewati.',
            ],
            [
                'user_id' => $rider1->id,
                'hazard_type' => 'Tutup Selokan Hilang',
                'description' => 'Grill besi penutup selokan hilang di tikungan lampu merah Tomang, ban sepeda bisa terperosok.',
                'hazard_photo' => 'reports/sample-hazard.jpg',
                'verification_status' => 'verified',
                'admin_notes' => 'Tutup sementara kayu telah dipasang penanda oleh tim patroli.',
            ],
        ];

        foreach ($reports as $data) {
            Report::create($data);
        }
    }
}

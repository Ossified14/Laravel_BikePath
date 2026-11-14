<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Report\Models\Report;
use Tests\TestCase;

class BikepathModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure module migrations are run in in-memory test database
        $this->artisan('migrate');
    }

    public function test_root_and_map_accessible_before_login(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('bikepathMap');
        $this->get('/map')->assertStatus(200)->assertSee('bikepathMap');
    }

    public function test_guest_can_access_map_before_login(): void
    {
        $response = $this->get('/map');
        $response->assertStatus(200)
            ->assertSee('bikepathMap')
            ->assertSee('Masuk')
            ->assertSee('Daftar');
    }

    public function test_guest_cannot_access_protected_pages(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_rider_cannot_access_user_management(): void
    {
        $rider = User::factory()->create(['role' => 'Rider']);

        $this->actingAs($rider)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    public function test_admin_can_access_and_crud_users(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // 1. Index
        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('Manajemen Pengguna');

        // 2. Create & Store (Synchronous)
        $userData = [
            'name' => 'Pesepeda Baru',
            'email' => 'pesepeda.baru@bikepath.test',
            'password' => 'password123',
            'role' => 'Rider',
            'phone_number' => '081234567899',
            'address' => 'Jl. Kebon Jeruk No. 15',
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'pesepeda.baru@bikepath.test', 'role' => 'Rider']);

        $newUser = User::where('email', 'pesepeda.baru@bikepath.test')->first();

        // 3. Show & Edit
        $this->actingAs($admin)->get("/admin/users/{$newUser->id}")->assertStatus(200);
        $this->actingAs($admin)->get("/admin/users/{$newUser->id}/edit")->assertStatus(200);

        // 4. Update
        $this->actingAs($admin)
            ->put("/admin/users/{$newUser->id}", [
                'name' => 'Pesepeda Baru Updated',
                'email' => 'pesepeda.baru@bikepath.test',
                'role' => 'Rider',
                'phone_number' => '08999999999',
                'address' => 'Jl. Kebon Jeruk Baru',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['name' => 'Pesepeda Baru Updated']);

        // 5. Destroy
        $this->actingAs($admin)
            ->delete("/admin/users/{$newUser->id}")
            ->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_rider_can_create_report_with_ajax_file_upload_without_coordinate(): void
    {
        Storage::fake('public');
        $rider = User::factory()->create(['role' => 'Rider']);

        $photo = UploadedFile::fake()->image('hazard.jpg', 600, 400);

        $response = $this->actingAs($rider)
            ->postJson('/reports', [
                'hazard_type' => 'Jalan Rusak / Berlubang',
                'description' => 'Lubang aspal berbahaya di lajur kiri dekat halte.',
                'hazard_photo' => $photo,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'hazard_type' => 'Jalan Rusak / Berlubang',
                    'verification_status' => 'pending',
                ],
            ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => $rider->id,
            'hazard_type' => 'Jalan Rusak / Berlubang',
            'verification_status' => 'pending',
        ]);
    }

    public function test_rider_can_only_edit_and_delete_pending_report_owned_by_them(): void
    {
        $rider1 = User::factory()->create(['role' => 'Rider']);
        $rider2 = User::factory()->create(['role' => 'Rider']);

        $pendingReport = Report::create([
            'user_id' => $rider1->id,
            'hazard_type' => 'Pohon Tumbang',
            'description' => 'Dahan jatuh',
            'hazard_photo' => 'reports/sample-hazard.jpg',
            'verification_status' => 'pending',
        ]);

        $verifiedReport = Report::create([
            'user_id' => $rider1->id,
            'hazard_type' => 'Banjir',
            'description' => 'Genangan',
            'hazard_photo' => 'reports/sample-hazard.jpg',
            'verification_status' => 'verified',
        ]);

        // Rider 2 cannot edit Rider 1's report
        $this->actingAs($rider2)
            ->postJson("/reports/{$pendingReport->id}", [
                'hazard_type' => 'Pohon Tumbang Edit',
                'description' => 'Dahan jatuh diedit',
            ])
            ->assertStatus(403);

        // Rider 1 cannot edit verified report
        $this->actingAs($rider1)
            ->postJson("/reports/{$verifiedReport->id}", [
                'hazard_type' => 'Banjir Edit',
                'description' => 'Genangan diedit',
            ])
            ->assertStatus(403);

        // Rider 1 CAN edit their own pending report
        $this->actingAs($rider1)
            ->postJson("/reports/{$pendingReport->id}", [
                'hazard_type' => 'Pohon Tumbang Diperbarui',
                'description' => 'Dahan besar jatuh menutupi jalur',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Rider 1 cannot delete verified report
        $this->actingAs($rider1)
            ->deleteJson("/reports/{$verifiedReport->id}")
            ->assertStatus(403);

        // Rider 1 CAN delete their pending report
        $this->actingAs($rider1)
            ->deleteJson("/reports/{$pendingReport->id}")
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('reports', ['id' => $pendingReport->id]);
    }

    public function test_admin_can_verify_report_via_ajax_and_rider_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $rider = User::factory()->create(['role' => 'Rider']);

        $report = Report::create([
            'user_id' => $rider->id,
            'hazard_type' => 'Jalan Berlubang',
            'description' => 'Lubang jalan',
            'hazard_photo' => 'reports/sample-hazard.jpg',
            'verification_status' => 'pending',
        ]);

        // Rider cannot verify
        $this->actingAs($rider)
            ->patchJson("/reports/{$report->id}/verify", [
                'verification_status' => 'verified',
            ])
            ->assertStatus(403);

        // Admin CAN verify
        $this->actingAs($admin)
            ->patchJson("/reports/{$report->id}/verify", [
                'verification_status' => 'verified',
                'admin_notes' => 'Petugas Dishub sudah diberitahu.',
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'verification_status' => 'verified',
                    'admin_notes' => 'Petugas Dishub sudah diberitahu.',
                ],
            ]);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'verification_status' => 'verified',
            'admin_notes' => 'Petugas Dishub sudah diberitahu.',
        ]);
    }
}

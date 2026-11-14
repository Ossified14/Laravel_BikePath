<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Report\Models\Report;

class ReportController extends Controller
{
    /**
     * Display the main interactive report page.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('report::index', compact('user'));
    }

    /**
     * Fetch reports data via AJAX.
     */
    public function getData(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Report::with('user:id,name,email,role,phone_number');

        // Role restriction: Rider only sees their own reports
        if ($user->isRider()) {
            $query->where('user_id', $user->id);
        }

        // Filter by verification status
        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        // Filter by hazard type
        if ($request->filled('hazard_type')) {
            $query->where('hazard_type', $request->input('hazard_type'));
        }

        // Search in description, hazard_type, or reporter name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('hazard_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->latest()->get();

        return response()->json([
            'success' => true,
            'is_admin' => $user->isAdmin(),
            'current_user_id' => $user->id,
            'data' => $reports,
        ]);
    }

    /**
     * Store a newly created report via AJAX (FormData with File Upload).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hazard_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'hazard_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
        ], [
            'hazard_photo.required' => 'Foto bukti halangan jalan wajib diunggah.',
            'hazard_photo.image' => 'File harus berupa gambar (JPEG, PNG, JPG, atau WebP).',
            'hazard_photo.max' => 'Ukuran foto maksimal adalah 5MB.',
        ]);

        // Handle AJAX file upload
        if ($request->hasFile('hazard_photo')) {
            $photoPath = $request->file('hazard_photo')->store('reports', 'public');
            $validated['hazard_photo'] = $photoPath;
        }

        $validated['user_id'] = Auth::id();
        $validated['verification_status'] = 'pending';

        $report = Report::create($validated);
        $report->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Laporan halangan berhasil dikirim via AJAX dan sedang menunggu verifikasi admin.',
            'data' => $report,
        ], 201);
    }

    /**
     * Show single report details via AJAX.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        $report = Report::with('user:id,name,email,phone_number')->findOrFail($id);

        if ($user->isRider() && $report->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat melihat rincian laporan milik sendiri.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Update report via AJAX.
     * Rider can only edit if status is 'pending'.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);

        // Check ownership and pending status for Rider
        if ($user->isRider()) {
            if ($report->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Laporan ini bukan milik Anda.',
                ], 403);
            }

            if ($report->verification_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan yang telah diverifikasi atau ditolak tidak dapat diubah lagi.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'hazard_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'hazard_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        // If new photo is uploaded
        if ($request->hasFile('hazard_photo')) {
            // Delete old photo if not default sample
            if ($report->hazard_photo && ! str_contains($report->hazard_photo, 'sample-hazard.jpg')) {
                Storage::disk('public')->delete($report->hazard_photo);
            }
            $validated['hazard_photo'] = $request->file('hazard_photo')->store('reports', 'public');
        }

        $report->update($validated);
        $report->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui.',
            'data' => $report,
        ]);
    }

    /**
     * Delete report via AJAX.
     * Rider can only delete if status is 'pending'.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);

        if ($user->isRider()) {
            if ($report->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Laporan ini bukan milik Anda.',
                ], 403);
            }

            if ($report->verification_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan yang sudah diverifikasi tidak dapat dihapus.',
                ], 403);
            }
        }

        // Remove photo from storage if not default sample
        if ($report->hazard_photo && ! str_contains($report->hazard_photo, 'sample-hazard.jpg')) {
            Storage::disk('public')->delete($report->hazard_photo);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan halangan berhasil dihapus.',
        ]);
    }

    /**
     * Admin action to verify or reject report via AJAX.
     */
    public function verifyStatus(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Admin yang berhak memverifikasi laporan.',
            ], 403);
        }

        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'verification_status' => ['required', Rule::in(['pending', 'verified', 'rejected'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update($validated);
        $report->load('user:id,name,email');

        $statusText = match ($validated['verification_status']) {
            'verified' => 'Diverifikasi (Verified)',
            'rejected' => 'Ditolak (Rejected)',
            default => 'Pending',
        };

        return response()->json([
            'success' => true,
            'message' => "Status laporan #{$report->id} berhasil diubah menjadi: {$statusText}.",
            'data' => $report,
        ]);
    }
}

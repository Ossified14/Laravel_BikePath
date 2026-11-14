<?php

namespace Modules\Report\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hazard_type',
        'description',
        'hazard_photo',
        'verification_status',
        'admin_notes',
    ];

    protected $appends = [
        'photo_url',
    ];

    /**
     * Relationship to author (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get accessible photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->hazard_photo) {
            return null;
        }

        if (str_starts_with($this->hazard_photo, 'http://') || str_starts_with($this->hazard_photo, 'https://')) {
            return $this->hazard_photo;
        }

        return Storage::url($this->hazard_photo);
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }
}

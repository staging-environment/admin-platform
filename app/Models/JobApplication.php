<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_offer_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_description',
        'cv_path',
        'cover_letter',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relationship to the job offer.
     */
    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    /**
     * Generate a temporary signed URL for the stored CV.
     */
    public function getCvUrlAttribute(): string
    {
        return Storage::disk('private_cvs')->temporaryUrl($this->cv_path, now()->addHours(2));
    }
}
?>

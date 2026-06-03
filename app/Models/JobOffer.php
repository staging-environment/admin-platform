<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'salary',
        'location',
        'expires_at',
        'active',
        'min_experience',
        'salary_range',
    ];

    /**
     * Get the applications for the job offer.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
?>

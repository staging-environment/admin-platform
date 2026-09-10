<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    protected $table = 'page_views';

    // Disable default timestamps, we only need created_at
    public $timestamps = false;

    protected $fillable = [
        'url',
        'path',
        'ip_address',
        'user_agent',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope query to only include public portal visits (exclude admin and logged-in employee visits).
     */
    public function scopePublicPortal(Builder $query): Builder
    {
        return $query->whereNull('user_id')
            ->where('path', 'not like', '/admin%')
            ->where('path', 'not like', 'admin%');
    }

    /**
     * Get the user who visited the page, if authenticated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'title_en', 'content', 'content_en',
        'club_id', 'district_id', 'target', 'publish_date',
        'expiration_date', 'is_pinned', 'status', 'attachments', 'created_by',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expiration_date' => 'datetime',
        'is_pinned' => 'boolean',
        'attachments' => 'array',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('publish_date')->orWhere('publish_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expiration_date')->orWhere('expiration_date', '>=', now());
            });
    }
}

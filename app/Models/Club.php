<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'name_en', 'club_code', 'district_id',
        'president_id', 'secretary_id', 'treasurer_id',
        'address', 'city', 'phone', 'email',
        'line_official_account', 'logo', 'website', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function president(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'president_id');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'secretary_id');
    }

    public function treasurer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'treasurer_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function officerTerms(): HasMany
    {
        return $this->hasMany(OfficerTerm::class);
    }
}

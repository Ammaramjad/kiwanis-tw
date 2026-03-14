<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'name_en', 'district_code', 'chairperson_id',
        'office_address', 'phone', 'email', 'line_official_account', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function chairperson(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'chairperson_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}

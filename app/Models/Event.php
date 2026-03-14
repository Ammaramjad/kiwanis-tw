<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'title_en', 'description', 'description_en',
        'club_id', 'district_id', 'venue', 'address', 'city',
        'lat', 'lng', 'start_time', 'end_time',
        'contact_person', 'contact_phone', 'max_participants',
        'registration_required', 'status', 'visibility', 'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'registration_required' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_registrations')
            ->withPivot(['status', 'notes', 'qr_code', 'checked_in_at'])
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsFullAttribute(): bool
    {
        if (!$this->max_participants) return false;
        return $this->registrations()->where('status', 'registered')->count() >= $this->max_participants;
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->lat && $this->lng) {
            return "https://maps.google.com/?q={$this->lat},{$this->lng}";
        }
        if ($this->address) {
            return 'https://maps.google.com/?q=' . urlencode($this->address);
        }
        return null;
    }
}

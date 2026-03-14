<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id', 'user_id', 'club_id', 'district_id',
        'name_zh', 'name_en', 'profile_photo', 'phone', 'email',
        'line_id', 'address', 'city', 'profession', 'company',
        'membership_type', 'join_date', 'is_active', 'bio',
        'emergency_contact_name', 'emergency_contact_phone',
    ];

    protected $casts = [
        'join_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_registrations')
            ->withPivot(['status', 'notes', 'qr_code', 'checked_in_at'])
            ->withTimestamps();
    }

    public function officerTerms(): HasMany
    {
        return $this->hasMany(OfficerTerm::class);
    }

    public function currentOfficerTerms(): HasMany
    {
        return $this->hasMany(OfficerTerm::class)->where('is_current', true);
    }

    public function sentContactLogs(): HasMany
    {
        return $this->hasMany(ContactLog::class, 'sender_id');
    }

    public function receivedContactLogs(): HasMany
    {
        return $this->hasMany(ContactLog::class, 'receiver_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_zh . ($this->name_en ? ' (' . $this->name_en . ')' : '');
    }

    public function getLineDeepLinkAttribute(): ?string
    {
        return $this->line_id ? 'https://line.me/ti/p/' . $this->line_id : null;
    }

    public function getPhoneE164Attribute(): ?string
    {
        if (!$this->phone) return null;
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '886' . substr($phone, 1);
        }
        return '+' . $phone;
    }
}

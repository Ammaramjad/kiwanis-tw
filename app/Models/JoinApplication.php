<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'city', 'profession',
        'preferred_club_id', 'message', 'status', 'admin_notes',
        'reviewed_by', 'reviewed_at', 'converted_member_id',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function preferredClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'preferred_club_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function convertedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'converted_member_id');
    }
}

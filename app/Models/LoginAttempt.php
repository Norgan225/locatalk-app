<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'device_fingerprint',
        'ip_address',
        'status',
        'reason',
        'device_info',
        'attempted_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'attempted_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }
}

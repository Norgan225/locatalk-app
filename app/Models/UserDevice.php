<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_name',
        'ip_address',
        'browser',
        'os',
        'user_agent',
        'is_authorized',
        'first_login_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_authorized' => 'boolean',
        'first_login_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

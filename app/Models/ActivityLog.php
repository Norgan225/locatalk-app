<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'action',
        'description',
        'ip_address',
        'device_fingerprint',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Helper method pour créer un log
    public static function log($action, $description = null, $metadata = [])
    {
        return self::create([
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()?->organization_id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'device_fingerprint' => request()->header('X-Device-Fingerprint'),
            'metadata' => $metadata,
        ]);
    }
}

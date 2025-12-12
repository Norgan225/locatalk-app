<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'meeting_link',
        'status',
        'start_time',
        'end_time',
        'is_recorded',
        'recording_url',
        'ai_summary',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_recorded' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meeting) {
            // Daily.co room creation will be handled in the controller
            // Don't auto-generate meeting_link here
        });
    }

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'meeting_participants')
            ->withPivot(['joined_at', 'left_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())->where('status', 'scheduled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function start()
    {
        $this->update(['status' => 'ongoing']);
    }

    public function end()
    {
        $this->update([
            'status' => 'completed',
            'end_time' => now(),
        ]);
    }

    public function getDurationAttribute()
    {
        if (!$this->end_time) return null;
        return $this->start_time->diffInMinutes($this->end_time);
    }
}

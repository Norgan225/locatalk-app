<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'department_id',
        'name',
        'description',
        'status',
        'progress',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function completedTasks()
    {
        return $this->tasks()->where('status', 'completed');
    }

    public function updateProgress()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            $this->progress = 0;
        } else {
            $completedTasks = $this->completedTasks()->count();
            $this->progress = round(($completedTasks / $totalTasks) * 100);
        }
        $this->save();
    }

    public function isOverdue()
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'completed';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'priority',
        'status',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class)->orderBy('created_at', 'desc');
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class)->orderBy('created_at', 'desc');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    // Scopes
    public function scopeTodo($query)
    {
        return $query->where('status', 'todo');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
        $this->project->updateProgress();
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }
}

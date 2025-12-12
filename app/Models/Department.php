<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'color',
        'head_user_id',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Methods
    public function membersCount()
    {
        return $this->users()->count();
    }

    public function activeProjects()
    {
        return $this->projects()->where('status', 'active');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'plan',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'subscription_end_date',
        'max_users',
        'allow_remote_access',
        'email',
        'phone',
        'website',
        'address',
        'status',
        'branding',
        'settings',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'subscription_end_date' => 'date',
        'allow_remote_access' => 'boolean',
        'branding' => 'array',
        'settings' => 'array',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Methods
    public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active';
    }

    public function canAddMoreUsers()
    {
        return $this->users()->count() < $this->max_users;
    }

    public function remainingUserSlots()
    {
        return $this->max_users - $this->users()->count();
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'department_id',
        'name',
        'email',
        'password',
        'temp_password',
        'password_changed',
        'role',
        'avatar',
        'notifications_enabled',
        'notification_sound',
        'notification_sound_enabled',
        'phone',
        'status',
        'language',
        'last_login_at',
        'last_logout_at',
        'last_ip_address',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'temp_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notifications_enabled' => 'boolean',
        'notification_sound_enabled' => 'boolean',
    ];
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

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class)->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('role')->withTimestamps();
    }

    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function calls()
    {
        return $this->hasMany(Call::class, 'caller_id');
    }

    public function receivedCalls()
    {
        return $this->hasMany(Call::class, 'receiver_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants')
            ->withPivot(['joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function createdMeetings()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function status()
    {
        return $this->hasOne(UserStatus::class);
    }

    public function currentStatus()
    {
        return $this->hasOne(UserStatus::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeResponsables($query)
    {
        return $query->where('role', 'responsable');
    }

    public function scopeEmployes($query)
    {
        return $query->where('role', 'employe');
    }

    // Methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isResponsable()
    {
        return $this->role === 'responsable';
    }

    public function isEmploye()
    {
        return $this->role === 'employe';
    }

    public function canManageUsers()
    {
        return in_array($this->role, ['super_admin', 'owner', 'admin']);
    }

    public function canManageDepartments()
    {
        return in_array($this->role, ['super_admin', 'owner']);
    }

    public function canSeeAllOrganizations()
    {
        return $this->role === 'super_admin';
    }

    public function isOnline()
    {
        // Considéré en ligne si dernière activité < 5 minutes
        return $this->last_login_at &&
               (!$this->last_logout_at || $this->last_login_at > $this->last_logout_at) &&
               $this->last_login_at->diffInMinutes(now()) < 5;
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Channel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'department_id',
        'name',
        'description',
        'type',
        'created_by',
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
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class); // Pour compatibilité avec l'ancien système
    }

    public function channelMessages()
    {
        return $this->hasMany(ChannelMessage::class);
    }

    public function encryptionKey()
    {
        return $this->hasOne(ChannelEncryptionKey::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('type', 'private');
    }

    public function scopeDepartment($query)
    {
        return $query->where('type', 'department');
    }

    // Accessors & Mutators
    public function getDisplayNameAttribute()
    {
        $prefix = match($this->type) {
            'public' => '#',
            'private' => '🔒',
            'department' => '#',
            default => '#'
        };

        return $prefix . $this->name;
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'public' => 'Public',
            'private' => 'Privé',
            'department' => 'Département',
            default => 'Inconnu'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'public' => '#4ade80',
            'private' => '#f87171',
            'department' => '#fbbb2a',
            default => '#6b7280'
        };
    }

    // Helper methods
    public function isPublic()
    {
        return $this->type === 'public';
    }

    public function isPrivate()
    {
        return $this->type === 'private';
    }

    public function isDepartment()
    {
        return $this->type === 'department';
    }

    public function canUserJoin(User $user)
    {
        // Public channels: anyone can join
        if ($this->isPublic()) {
            return true;
        }

        // Department channels: only users from the same department
        if ($this->isDepartment()) {
            return $user->department_id === $this->department_id;
        }

        // Private channels: only by invitation
        return false;
    }

    public function canUserManage(User $user)
    {
        return $user->canManageUsers() ||
               $user->isOwner() ||
               $user->id === $this->created_by;
    }

    public function addMember(User $user)
    {
        if (!$this->users()->where('users.id', $user->id)->exists()) {
            $this->users()->attach($user->id);
            return true;
        }
        return false;
    }

    public function removeMember(User $user)
    {
        $this->users()->detach($user->id);
        return true;
    }

    public function isMember(User $user)
    {
        return $this->users()->where('users.id', $user->id)->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'last_seen',
        'last_activity',
        'custom_message',
        'is_invisible',
        'device_type',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'last_activity' => 'datetime',
        'is_invisible' => 'boolean',
    ];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online')
                     ->where('is_invisible', false);
    }

    public function scopeAway($query)
    {
        return $query->where('status', 'away');
    }

    public function scopeBusy($query)
    {
        return $query->where('status', 'busy');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['online', 'away', 'busy'])
                     ->where('is_invisible', false);
    }

    public function scopeRecentlyActive($query, $minutes = 5)
    {
        return $query->where('last_activity', '>=', Carbon::now()->subMinutes($minutes));
    }

    /**
     * Helper methods
     */
    public function setOnline(string $deviceType = 'web'): self
    {
        $this->update([
            'status' => 'online',
            'last_seen' => now(),
            'last_activity' => now(),
            'device_type' => $deviceType,
        ]);

        return $this;
    }

    public function setOffline(): self
    {
        $this->update([
            'status' => 'offline',
            'last_seen' => now(),
        ]);

        return $this;
    }

    public function setAway(): self
    {
        $this->update([
            'status' => 'away',
            'last_activity' => now(),
        ]);

        return $this;
    }

    public function setBusy(string $customMessage = null): self
    {
        $this->update([
            'status' => 'busy',
            'custom_message' => $customMessage,
            'last_activity' => now(),
        ]);

        return $this;
    }

    public function setDoNotDisturb(string $customMessage = null): self
    {
        $this->update([
            'status' => 'do_not_disturb',
            'custom_message' => $customMessage,
            'last_activity' => now(),
        ]);

        return $this;
    }

    public function setInvisible(bool $invisible = true): self
    {
        $this->update([
            'is_invisible' => $invisible,
        ]);

        return $this;
    }

    public function updateActivity(): self
    {
        $this->update([
            'last_activity' => now(),
        ]);

        return $this;
    }

    /**
     * Vérifier si l'utilisateur est considéré comme en ligne
     */
    public function isOnline(): bool
    {
        if ($this->is_invisible) {
            return false;
        }

        return $this->status === 'online' &&
               $this->last_activity &&
               $this->last_activity->diffInMinutes(now()) <= 5;
    }

    /**
     * Vérifier si l'utilisateur est inactif
     */
    public function isInactive(int $minutes = 10): bool
    {
        if (!$this->last_activity) {
            return true;
        }

        return $this->last_activity->diffInMinutes(now()) > $minutes;
    }

    /**
     * Obtenir le temps depuis la dernière activité
     */
    public function getTimeSinceActivity(): ?string
    {
        if (!$this->last_activity) {
            return null;
        }

        $diff = $this->last_activity->diffForHumans();
        return $diff;
    }

    /**
     * Obtenir le statut visible (en tenant compte du mode invisible)
     */
    public function getVisibleStatus(): string
    {
        if ($this->is_invisible) {
            return 'offline';
        }

        return $this->status;
    }

    /**
     * Obtenir le statut avec couleur pour l'UI
     */
    public function getStatusWithColor(): array
    {
        $colors = [
            'online' => '#10b981',         // green
            'offline' => '#6b7280',        // gray
            'away' => '#f59e0b',           // orange
            'busy' => '#ef4444',           // red
            'do_not_disturb' => '#8b5cf6', // purple
        ];

        $status = $this->getVisibleStatus();

        return [
            'status' => $status,
            'color' => $colors[$status] ?? '#6b7280',
            'label' => $this->getStatusLabel($status),
            'custom_message' => $this->custom_message,
        ];
    }

    /**
     * Obtenir le label traduit du statut
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'online' => 'En ligne',
            'offline' => 'Hors ligne',
            'away' => 'Absent',
            'busy' => 'Occupé',
            'do_not_disturb' => 'Ne pas déranger',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Créer ou mettre à jour le statut d'un utilisateur
     */
    public static function updateOrCreateForUser(int $userId, array $attributes = []): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId],
            array_merge($attributes, ['last_activity' => now()])
        );
    }

    /**
     * Obtenir tous les utilisateurs en ligne
     * Logique stricte : seulement les utilisateurs avec activité récente (2-5 minutes)
     * pour éviter de compter des statuts obsolètes
     */
    public static function getOnlineUsers()
    {
        return static::where('last_activity', '>=', Carbon::now()->subMinutes(5))
                     ->where('is_invisible', false)
                     ->whereNotNull('last_activity')
                     ->with('user')
                     ->get()
                     ->unique('user_id'); // Éviter les doublons
    }

    /**
     * Obtenir le statut d'un utilisateur ou créer un statut par défaut
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'status' => 'offline',
                'last_seen' => now(),
            ]
        );
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasUuid;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles, HasTeams {
        HasTeams::teams insteadof HasRoles;
        HasRoles::teams as permissionTeams;
    }
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country',
        'organization',
        'registration_reason',
        'bio',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'admin_guide_completed_at' => 'datetime',
            'is_active' => 'boolean',
            'is_platform_owner' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function threads()
    {
        return $this->hasMany(ForumThread::class, 'user_id');
    }

    public function actualites()
    {
        return $this->hasMany(Actualite::class, 'user_id');
    }

    public function ressources()
    {
        return $this->hasMany(Resource::class, 'user_id');
    }

    public function canValidateRegistrations(): bool
    {
        if ($this->hasRole(['super_admin', 'moderateur'])) {
            return true;
        }

        return $this->can('valider-inscriptions') || $this->can('administrer-utilisateurs');
    }

    public function canAccessBackOffice(): bool
    {
        if ($this->hasRole(['super_admin', 'moderateur'])) {
            return true;
        }

        $backOfficePermissions = [
            'publier-bibliotheque',
            'moderer-forum',
            'gerer-carte',
            'soumettre-acteur',
            'creer-evenements',
            'gerer-rss',
            'publier-actualites',
            'administrer-utilisateurs',
            'acceder-statistiques',
            'contribuer-multimedia',
            'gerer-newsletter',
            'gerer-formations',
        ];

        return $this->hasAnyPermission($backOfficePermissions);
    }

    public function isPlatformOwner(): bool
    {
        return (bool) $this->is_platform_owner;
    }

    /** Compte fondateur masqué aux autres administrateurs. */
    public function scopeVisibleToAdmin($query, User $actor)
    {
        if ($actor->isPlatformOwner()) {
            return $query;
        }

        return $query->where('is_platform_owner', false);
    }

    public function visibleToAdmin(User $actor): bool
    {
        if ($this->isPlatformOwner() && ! $actor->isPlatformOwner()) {
            return false;
        }

        return true;
    }

    public function canAdminEdit(User $actor): bool
    {
        if (! $this->visibleToAdmin($actor)) {
            return false;
        }

        if ($this->hasRole('super_admin') && ! $actor->isPlatformOwner()) {
            return $actor->id === $this->id;
        }

        return true;
    }

    public function canAdminToggle(User $actor): bool
    {
        if ($actor->id === $this->id) {
            return false;
        }

        if ($this->isPlatformOwner()) {
            return false;
        }

        if ($this->hasRole('super_admin')) {
            return $actor->isPlatformOwner();
        }

        return $this->visibleToAdmin($actor);
    }

    public function canAdminDelete(User $actor): bool
    {
        return $this->canAdminToggle($actor);
    }
}

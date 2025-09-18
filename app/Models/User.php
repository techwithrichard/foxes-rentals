<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;
use Spatie\WelcomeNotification\ReceivesWelcomeNotification;

class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable, ReceivesWelcomeNotification, HasRoles;
    use SoftDeletes;
    use HasUuids;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'identity_no',
        'identity_document',
        'occupation_status',
        'occupation_place',
        'kin_name',
        'kin_identity',
        'kin_phone',
        'kin_relationship',
        'emergency_name',
        'emergency_contact',
        'emergency_email',
        'emergency_relationship',
        'address',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    protected $appends = ['initials'];


//    protected function initials(): Attribute
//    {
//        return Attribute::make(
//            get: function () {
//                $name = $this->name;
//                $words = explode(' ', $name);
//                $initials = '';
//                foreach ($words as $w) {
//                    $initials .= $w[0];
//                }
//
//                return strtoupper($initials);
//            });
//    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id', 'id');
    }

    public function all_leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id', 'id')->withTrashed()->oldest('deleted_at');

    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'landlord_id', 'id');
    }

    public function houses(): HasMany
    {
        return $this->hasMany(House::class, 'landlord_id', 'id');
    }

    //tenant has one overpayment
    public function overpayment(): HasOne
    {
        return $this->hasOne(Overpayment::class, 'tenant_id', 'id');
    }

    //user has many login activities
    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class, 'user_id', 'id');
    }

    //user has many identities
    public function identities(): HasMany
    {
        return $this->hasMany(IdentityDocument::class, 'tenant_id', 'id');
    }

    public function getInitialsAttribute(): string
    {
        $name = explode(' ', $this->name);
        if (count($name) == 1) {
            return strtoupper(mb_substr($name[0], 0, 1));
        }
        return strtoupper(mb_substr($name[0], 0, 1) . mb_substr($name[1], 0, 1));
    }

    //get preferred locale attribute
    public function getPreferredUserLocaleAttribute(): string
    {
        return $this->attributes['preferred_locale'] ?? 'en';
    }


    public function preferredLocale()
    {

        return $this->preferred_user_locale;
    }

    /**
     * Get the user's profile
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's preferences
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Get user's full display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->profile ? $this->profile->display_name : $this->name;
    }

    /**
     * Get user's profile completion percentage
     */
    public function getProfileCompletionAttribute(): int
    {
        return $this->profile ? $this->profile->completion_percentage : 0;
    }

    /**
     * Get user's last activity
     */
    public function getLastActivityAttribute(): ?UserActivity
    {
        return $this->activities()->latest()->first();
    }

    /**
     * Get user's activity count for a period
     */
    public function getActivityCount(int $days = 30): int
    {
        return $this->activities()->where('created_at', '>=', now()->subDays($days))->count();
    }

    /**
     * Scope to get active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to get users with profiles
     */
    public function scopeWithProfile($query)
    {
        return $query->has('profile');
    }

    /**
     * Scope to get users with preferences
     */
    public function scopeWithPreferences($query)
    {
        return $query->has('preferences');
    }

    /**
     * Scope to get recently active users
     */
    public function scopeRecentlyActive($query, int $days = 7)
    {
        return $query->whereHas('activities', function ($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days));
        });
    }
}

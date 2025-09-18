<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone_secondary',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'bio',
        'profile_picture',
        'national_id',
        'passport_number',
        'driver_license',
        'occupation',
        'company',
        'website',
        'linkedin',
        'twitter',
        'facebook',
        'instagram',
        'preferred_language',
        'timezone',
        'created_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this profile
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full name
     */
    public function getFullNameAttribute(): string
    {
        $name = trim($this->first_name . ' ' . $this->last_name);
        if ($this->middle_name) {
            $name = trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
        }
        return $name ?: $this->user->name ?? 'Unknown';
    }

    /**
     * Get the display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->user->name ?? 'Unknown User';
    }

    /**
     * Get the age
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Get the formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $addressParts) ?: 'No address provided';
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if (!$this->profile_picture) {
            return null;
        }

        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        return asset('storage/' . $this->profile_picture);
    }

    /**
     * Get the initials
     */
    public function getInitialsAttribute(): string
    {
        $initials = '';
        
        if ($this->first_name) {
            $initials .= strtoupper(substr($this->first_name, 0, 1));
        }
        
        if ($this->last_name) {
            $initials .= strtoupper(substr($this->last_name, 0, 1));
        }

        return $initials ?: 'U';
    }

    /**
     * Get the gender display name
     */
    public function getGenderDisplayNameAttribute(): string
    {
        $genders = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            'prefer_not_to_say' => 'Prefer not to say'
        ];

        return $genders[$this->gender] ?? ucfirst($this->gender ?? 'Not specified');
    }

    /**
     * Get the emergency contact formatted
     */
    public function getEmergencyContactFormattedAttribute(): ?string
    {
        if (!$this->emergency_contact_name || !$this->emergency_contact_phone) {
            return null;
        }

        $contact = $this->emergency_contact_name;
        if ($this->emergency_contact_relationship) {
            $contact .= " ({$this->emergency_contact_relationship})";
        }
        $contact .= " - {$this->emergency_contact_phone}";

        return $contact;
    }

    /**
     * Get social media links
     */
    public function getSocialMediaLinksAttribute(): array
    {
        return array_filter([
            'website' => $this->website,
            'linkedin' => $this->linkedin,
            'twitter' => $this->twitter,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram
        ]);
    }

    /**
     * Get identification documents
     */
    public function getIdentificationDocumentsAttribute(): array
    {
        return array_filter([
            'national_id' => $this->national_id,
            'passport_number' => $this->passport_number,
            'driver_license' => $this->driver_license
        ]);
    }

    /**
     * Check if profile is complete
     */
    public function getIsCompleteAttribute(): bool
    {
        $requiredFields = [
            'first_name',
            'last_name',
            'phone_secondary',
            'address',
            'city',
            'state',
            'postal_code'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get profile completion percentage
     */
    public function getCompletionPercentageAttribute(): int
    {
        $allFields = [
            'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender',
            'address', 'city', 'state', 'postal_code', 'country',
            'phone_secondary', 'emergency_contact_name', 'emergency_contact_phone',
            'bio', 'profile_picture', 'national_id', 'occupation', 'company'
        ];

        $filledFields = 0;
        foreach ($allFields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / count($allFields)) * 100);
    }

    /**
     * Scope to get profiles with specific gender
     */
    public function scopeGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope to get profiles in specific city
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope to get profiles in specific state
     */
    public function scopeInState($query, string $state)
    {
        return $query->where('state', 'like', "%{$state}%");
    }

    /**
     * Scope to get profiles in specific country
     */
    public function scopeInCountry($query, string $country)
    {
        return $query->where('country', 'like', "%{$country}%");
    }

    /**
     * Scope to get profiles with emergency contacts
     */
    public function scopeWithEmergencyContacts($query)
    {
        return $query->whereNotNull('emergency_contact_name')
                    ->whereNotNull('emergency_contact_phone');
    }

    /**
     * Scope to get profiles with social media
     */
    public function scopeWithSocialMedia($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('website')
              ->orWhereNotNull('linkedin')
              ->orWhereNotNull('twitter')
              ->orWhereNotNull('facebook')
              ->orWhereNotNull('instagram');
        });
    }

    /**
     * Get suggested countries
     */
    public static function getSuggestedCountries(): array
    {
        return [
            'Kenya' => 'Kenya',
            'Uganda' => 'Uganda',
            'Tanzania' => 'Tanzania',
            'Rwanda' => 'Rwanda',
            'Ethiopia' => 'Ethiopia',
            'Ghana' => 'Ghana',
            'Nigeria' => 'Nigeria',
            'South Africa' => 'South Africa',
            'United States' => 'United States',
            'United Kingdom' => 'United Kingdom',
            'Canada' => 'Canada',
            'Australia' => 'Australia'
        ];
    }

    /**
     * Get suggested genders
     */
    public static function getSuggestedGenders(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            'prefer_not_to_say' => 'Prefer not to say'
        ];
    }

    /**
     * Get suggested relationships
     */
    public static function getSuggestedRelationships(): array
    {
        return [
            'spouse' => 'Spouse',
            'parent' => 'Parent',
            'child' => 'Child',
            'sibling' => 'Sibling',
            'friend' => 'Friend',
            'colleague' => 'Colleague',
            'other' => 'Other'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($profile) {
            if (!$profile->created_by && auth()->check()) {
                $profile->created_by = auth()->id();
            }
        });
    }
}

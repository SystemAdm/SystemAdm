<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasPermissions, Billable, LaravelPermissionToVueJS;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'given_name',
        'additional_name',
        'family_name',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'display_name',
        'sort_name'     // for sorting (family_name, given_name)
    ];

    /**
     * Get user's full name (given + additional + family)
     */
    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->given_name,
            $this->additional_name,
            $this->family_name
        ])));
    }

    /**
     * Get user's display name (given + family)
     */
    public function getDisplayNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->given_name,
            $this->family_name
        ])));
    }

    /**
     * Get sortable name format (family, given additional)
     */
    public function getSortNameAttribute(): string
    {
        $givenAndAdditional = trim(implode(' ', array_filter([
            $this->given_name,
            $this->additional_name
        ])));

        return trim(implode(', ', array_filter([
            $this->family_name,
            $givenAndAdditional
        ])));
    }

    /**
     * Set the user's given name with capitalized first letter
     */
    protected function givenName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(trim($value))
        );
    }

    /**
     * Set the user's additional name with capitalized first letter
     */
    protected function additionalName(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? ucfirst(trim($value)) : null
        );
    }

    /**
     * Set the user's family name with capitalized first letter
     */
    protected function familyName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(trim($value))
        );
    }

    /**
     * Get all phone numbers associated with the user
     *
     * @return BelongsToMany<Phone>
     */
    public function phones(): BelongsToMany
    {
        return $this->belongsToMany(Phone::class);
    }

    /**
     * Get all email addresses associated with the user
     *
     * @return BelongsToMany<Email>
     */
    public function emails(): BelongsToMany
    {
        return $this->belongsToMany(Email::class);
    }

    /**
     * Get the user's username
     *
     * @return HasOne<Username>
     */
    public function username(): HasOne
    {
        return $this->hasOne(Username::class);
    }

    /**
     * Get all memberships associated with the user
     *
     * @return HasMany<Membership>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the user's profile
     *
     * @return HasOne<Profile>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's guardians/parents
     *
     * @return BelongsToMany<User>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardians', 'user_id', 'guardian_id');
    }

    /**
     * Get the user's children/wards
     *
     * @return BelongsToMany<User>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardians', 'guardian_id', 'user_id');
    }

    /**
     * Get the user's highest ranked role
     *
     * @return object|null The highest ranked role or null if no roles exist
     */
    public function getRankAttribute(): ?object
    {
        return $this->roles()->orderBy('rank')->first() ?? null;
    }

    /**
     * Generer en QR-kode for brukeren
     *
     * @return string SVG QR code
     */
    public function generateQrCode(): string
{
    $encodedId = $this->id * 35;
    $qrText = "SpL{$encodedId}";

    return QrCode::format('svg')
        ->size(200)
        ->margin(1)
        ->errorCorrection('H')
        ->generate($qrText);
}

    /**
     * Dekod en QR-kode tekst for å finne bruker ID
     *
     * @param string $qrText
     * @return int|null
     */
    public static function decodeQrText(string $qrText): ?int
    {
        // Sjekk om teksten starter med SpL
        if (!str_starts_with($qrText, 'SpL')) {
            return null;
        }

        // Hent ut tallet etter SpL
        $encodedId = substr($qrText, 3);

        // Sjekk om det er et gyldig tall
        if (!is_numeric($encodedId)) {
            return null;
        }

        // Del på 35 for å få original ID
        return (int)($encodedId / 35);
    }

}

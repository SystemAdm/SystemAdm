<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Phone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country',  // Landskode
        'number',   // Telefonnummer
    ];
    protected $appends = ['full_phone'];

    /**
     * Get the users associated with this phone number
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'phone_user')
            ->withPivot('primary', 'verified_by', 'verified_at') // Pivot-feltene
            ->withTimestamps(); // Timestamp for opprettelse og oppdatering
    }

    function getFullPhoneAttribute(): string
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            // Fjern '+' fra landkode om nødvendig
            $countryCode = ltrim($this->country, '+');

            // Parse telefonnummeret med landkode
            $phone = $phoneUtil->parse($this->number, null); // Ingen hardkodet region
            $phone->setCountryCode((int)$countryCode);

            // Formater til internasjonalt format
            return $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException $e) {
            // Returner et fallback (eller kast unntak videre)
            return $this->country . ' ' . $this->number;
        }
    }
    protected function country(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => preg_replace('/\D/', '', $value) // Behold kun tall
        );
    }
    protected function number(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => preg_replace('/\D/', '', $value) // Bare tall
        );
    }
}

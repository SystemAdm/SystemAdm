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

            // Hvis "country" ikke er oppgitt (eller feil format), bruk 'ZZ' (ukjent region)
            $countryCode = ltrim($this->country ?? '', '+');
            $phone = $phoneUtil->parse($this->number, null);

            // Hvis landkode ikke er satt riktig, oppdaterer vi den
            if (!$phone->hasCountryCode() && is_numeric($countryCode)) {
                $phone->setCountryCode((int)$countryCode);
            }

            // Formater telefonnumre til internasjonalt format
            return $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException $e) {
            // Feilhåndtering, logg unntaket og gi en fallback
            \Log::error('Feil ved parsing av telefonnummer: ' . $e->getMessage());
            return trim($this->country . ' ' . $this->number); // Returner det rå formatet som en fallback
        }
    }

    protected function country(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => preg_replace('/\D/', '', $value) // Behold kun tall
        );
    }

    // Valider telefonnummer før lagring
    protected function number(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $defaultCountry = '47'; // Default til Norge

                try {
                    // Prøv å parse nummeret
                    $phone = $phoneUtil->parse($value, $this->country ?? $defaultCountry);

                    // Formater til E164 format
                    return $phoneUtil->format($phone, PhoneNumberFormat::E164);
                } catch (NumberParseException $e) {
                    throw new \InvalidArgumentException("Ugyldig nummer: {$value}");
                }
            }
        );
    }
}

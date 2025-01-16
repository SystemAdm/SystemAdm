<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
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
            $defaultCountry = '47'; // Standard landkode for Norge
            $countryCode = ltrim($this->country ?? $defaultCountry, '+'); // Rens "+" fra country
            $regionCode = $countryCode === '47' ? 'NO' : null; // Standard regionkode for Norge

            // Valider telefonnummer og parse
            if (!$this->number || !is_string($this->number)) {
                throw new \InvalidArgumentException("Telefonnummer mangler eller er ugyldig.");
            }
            $phone = $phoneUtil->parse($this->number, $regionCode);

            // Sett landkoden manuelt hvis den mangler
            if (!$phone->hasCountryCode() && is_numeric($countryCode)) {
                $phone->setCountryCode((int) $countryCode);
            }

            return $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException $e) {
            Log::error('Feil ved parsing av telefonnummer', [
                'country' => $this->country,
                'number' => $this->number,
                'error' => $e->getMessage(),
            ]);
            return $this->country && $this->number
                ? trim($this->country . ' ' . $this->number)
                : 'Ugyldig telefonnummer';
        } catch (\InvalidArgumentException $e) {
            // Dette fanger opp tilfeller der input i `number` eller `country` mangler
            Log::error('Ugyldig input for telefonnummer', [
                'country' => $this->country,
                'number' => $this->number,
                'error' => $e->getMessage(),
            ]);
            return 'Ugyldig telefonnummer';
        }
    }

    protected function country(): Attribute
    {
        return Attribute::make(
            set: function (string $value): string {
                $country = preg_replace('/\D/', '', $value);
                if (strlen($country) < 1 || strlen($country) > 3) {
                    throw new \InvalidArgumentException("Ugyldig landskode: {$value}");
                }
                return $country;
            }
        );
    }

    // Valider telefonnummer før lagring
    protected function number(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $countryToRegionMap = [
                    '47' => 'NO', // Norge
                    // Legg til flere landkoder hvis nødvendig
                ];

                // Sett standard regionkode, og konverter '+' hvis det eksisterer
                $regionCode = $countryToRegionMap[$this->country ?? '47'] ?? 'NO';
                $countryCode = ltrim($this->country ?? '47', '+');

                try {
                    // Vurder om fullstendig parsing er nødvendig
                    $phone = $phoneUtil->parse($value, $regionCode);

                    // Kontroller og legg til landkode for tall uten kode
                    if (!$phone->hasCountryCode() && is_numeric($countryCode)) {
                        $phone->setCountryCode((int) $countryCode);
                    }

                    // Returner alltid i E164-format
                    return $phoneUtil->format($phone, PhoneNumberFormat::E164);
                } catch (NumberParseException $e) {
                    Log::warning('Ugyldig nummer oppgitt', [
                        'value' => $value,
                        'country' => $this->country,
                        'region' => $regionCode,
                        'error' => $e->getMessage()
                    ]);

                    throw new \InvalidArgumentException("Ugyldig nummer: {$value}");
                }
            }
        );
    }
}

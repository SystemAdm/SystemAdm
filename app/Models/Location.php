<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'street_name',
        'street_number',
        'phone_id',
        'postal_id',
        'lat',
        'lng',
        'zoom',
    ];

    protected $appends = ['images'];

    /**
     * En lokasjon kan ha mange tilknyttede events.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * En lokasjon tilhører en postnummer (postal).
     */
    public function postal(): BelongsTo
    {
        return $this->belongsTo(Postal::class, 'postal_id');
    }

    /**
     * En lokasjon har en tilknyttet telefon via relasjonen til Phone::class.
     */
    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class, 'phone_id');
    }

    public function getImagesAttribute(): Response
    {
        $defaultImagePath = public_path('images/logos/spillhuset-logo-black.png');
        $imagePath = $this->image
            ? storage_path('app/public/images/locations/' . $this->image)
            : $defaultImagePath;

        if (!file_exists($imagePath)) {
            $imagePath = $defaultImagePath; // Bruk standardbildet hvis bildet ikke finnes
        }

        $mimeType = mime_content_type($imagePath) ?? 'image/png';
        $imageContent = base64_encode(file_get_contents($imagePath));

        return response($imageContent)->header('Content-Type', $mimeType);
    }

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}

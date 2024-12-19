<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    function postal(): BelongsTo
    {
        return $this->belongsTo(Postal::class, 'code');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'active', 'pending'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}

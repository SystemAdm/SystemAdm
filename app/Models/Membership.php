<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Membership extends Model
{
    use SoftDeletes,Billable;
    protected $fillable = ['online','valid_to'];
    protected $appends = ['valid','validDate'];
    function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    function verified(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'verified_id');
    }

    function getValidAttribute()
    {
        return Carbon::create($this->valid_to) > Carbon::now();
    }
    function getValidDateAttribute() {
        return Carbon::create($this->valid_to)->format('d.m.Y');
    }
}

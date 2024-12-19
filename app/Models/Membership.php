<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Membership extends Model
{
    use SoftDeletes, Billable;

    protected $with = ['user', 'verified'];
    protected $fillable = ['online', 'valid_to'];
    protected $casts = [
        'valid_to' => 'datetime',
        'online' => 'boolean',
    ];
    protected $appends = ['valid', 'validDate'];

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function verified(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_id');
    }

    function getValidAttribute(): bool
    {
        return optional($this->valid_to)->greaterThan(Carbon::now()) ?? false;
    }

    function getValidDateAttribute(): ?string
    {
        return optional($this->valid_to)->format('d.m.Y');
    }

    public function scopeValid($query)
    {
        return $query->where('valid_to', '>', Carbon::now());
    }

    public function extendValidity(int $months = 1): void
    {
        $this->valid_to = Carbon::parse($this->valid_to)->addMonths($months);
        $this->save();
    }

    function toArray(): array
    {
        $array = parent::toArray();
        $array['valid_to_formatted'] = $this->validDate;
        return $array;
    }
}

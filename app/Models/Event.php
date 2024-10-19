<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use softDeletes;

    private array $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    private array $dager = ['Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør', 'Søn'];
    private array $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    private array $maneder = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'];
    protected $appends = [
        'event_begin_date',
        'event_end_date',
        'event_began',
        'event_ended',
        'signup_begin_date',
        'signup_end_date',
        'signup_began',
        'signup_ended',
        'seats_available',
    ];

    function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    function getEventBeginDateAttribute(): array|string
    {
        return $this->date($this->attributes['event_begin']);
    }

    function getEventEndDateAttribute(): array|string
    {
        return $this->date($this->attributes['event_end']);
    }

    function getSignupBeginDateAttribute(): array|string
    {
        return $this->date($this->attributes['signup_begin']);
    }

    function getSignupEndDateAttribute(): array|string
    {
        return $this->date($this->attributes['signup_end']);
    }

    function getSignupBeganAttribute(): bool
    {
        return $this->signup_start < Carbon::now();
    }

    function getSignupEndedAttribute(): bool
    {
        $now = Carbon::now();
        return $this->signup_start < $now && $this->signup_end < $now;
    }

    function getEventBeganAttribute(): bool
    {
        return $this->event_begin > Carbon::now() && $this->event_end < Carbon::now();
    }

    function getEventEndedAttribute(): bool
    {
        return $this->event_end > Carbon::now();
    }

    function getSeatsAvailableAttribute()
    {
        if ($this->seats == -1) return false;
        if ($this->seats == 0) return true;
        else  return $this->seats - $this->reservations()->count();
    }

    function reservations(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    function date($date): array|string
    {
        $carbon = new Carbon($date);
        $string = $carbon->format('D, d M Y H:i');
        $string = str_replace($this->days, $this->dager, $string);
        return str_replace($this->months, $this->maneder, $string);
    }
}

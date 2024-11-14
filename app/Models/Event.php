<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use softDeletes;

    protected $fillable = [
        'event_begin',
        'event_end',
        'title',
        'seats',
        'description',
        'attending',
        'registration_needed',
        'image',
        'location_id'
    ];
    private array $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    private array $dager = ['Man.', 'Tir.', 'Ons.', 'Tor.', 'Fre.', 'Lør.', 'Søn.'];
    private array $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    private array $maneder = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'Mai', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Des.'];
    protected $appends = [
        'images',
        'event_begin_date',
        'event_end_date',
        'event_began',
        'event_ended',
        'signup_begin_date',
        'signup_end_date',
        'signup_began',
        'signup_ended',
        'seats_available',
        'duration_hours',
        'duration_days',
        'event_time_end',
        'event_time_start',
        'event_date',
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
        else  return $this->seats - $this->registered->count();
    }

    function registered(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered')->withTimestamps();
    }

    function registeredCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered_crew')->withTimestamps();
    }

    function attending(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending')->withTimestamps();
    }

    function attendingCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending_crew')->withTimestamps();
    }

    function insider(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside')->withTimestamps();
    }

    function insiderCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside_crew')->withTimestamps();
    }

    function getDurationDaysAttribute()
    {
        $begin = new Carbon($this->event_begin);
        $end = new Carbon($this->event_end);
        return $begin->diff($end)->d;
    }
    function getDurationHoursAttribute()
    {
        $begin = new Carbon($this->event_begin);
        $end = new Carbon($this->event_end);
        return $begin->diff($end)->hours;
    }

    function getEventDateAttribute(){
        $carbon = new Carbon($this->event_begin);
        $string = $carbon->format('D d M Y');
        $string = str_replace($this->days, $this->dager, $string);
        return ucfirst(strtolower(str_replace($this->months, $this->maneder, $string)));
    }

    function getEventTimeStartAttribute()
    {
        $begin = new Carbon($this->event_begin);
        return $begin->format('H:i');
    }

    function getEventTimeEndAttribute()
    {
        $end = new Carbon($this->event_end);
        return $end->format('H:i');
    }

    function date($date): array|string
    {
        $carbon = new Carbon($date);
        $string = $carbon->format('D d M Y H:i');
        $string = str_replace($this->days, $this->dager, $string);
        return ucfirst(strtolower(str_replace($this->months, $this->maneder, $string)));
    }
    public function getImagesAttribute()
    {
        $image = null;
        $mimeType = 'image/png'; // Default MIME type

        if ($this->image == null) {
            // Use default image when $this->image is null
            $imagePath = public_path('images/logos/spillhuset-logo-black.png');
        } else {
            // Use image from storage
            $imagePath = storage_path('app/public/images/events/' . $this->image);
        }

        // Check if the file exists to avoid errors
        if (file_exists($imagePath)) {
            // Determine the MIME type (supports PNG and JPEG)
            $mimeType = mime_content_type($imagePath); // Detects the actual MIME type
            $image = base64_encode(file_get_contents($imagePath));
        }

        // Return the base64-encoded image with the detected MIME type
        return response($image)->header('Content-Type', $mimeType);
    }
}

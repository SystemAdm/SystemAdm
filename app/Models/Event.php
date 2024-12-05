<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use SoftDeletes;

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

    protected $casts = [
        'event_begin' => 'datetime',
        'event_end' => 'datetime',
        'signup_begin' => 'datetime',
        'signup_end' => 'datetime',
        'deleted_at' => 'datetime',
        'registration_needed' => 'boolean',
    ];

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

    private const DAYS = [
        'Mon' => 'Man.',
        'Tue' => 'Tir.',
        'Wed' => 'Ons.',
        'Thu' => 'Tor.',
        'Fri' => 'Fre.',
        'Sat' => 'Lør.',
        'Sun' => 'Søn.'
    ];

    private const MONTHS = [
        'Jan' => 'Jan.',
        'Feb' => 'Feb.',
        'Mar' => 'Mar.',
        'Apr' => 'Apr.',
        'May' => 'Mai',
        'Jun' => 'Jun.',
        'Jul' => 'Jul.',
        'Aug' => 'Aug.',
        'Sep' => 'Sep.',
        'Oct' => 'Okt.',
        'Nov' => 'Nov.',
        'Dec' => 'Des.'
    ];

    /**
     * Get the location associated with the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Location>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get users registered for the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function registered(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered')->withTimestamps();
    }

    /**
     * Get crew members registered for the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function registeredCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered_crew')->withTimestamps();
    }

    /**
     * Get users attending the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function attending(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending')->withTimestamps();
    }

    /**
     * Get crew members attending the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function attendingCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending_crew')->withTimestamps();
    }

    /**
     * Get insider users for the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function insider(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside')->withTimestamps();
    }

    /**
     * Get insider crew members for the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function insiderCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside_crew')->withTimestamps();
    }

    private function formatDate(?string $date): string
    {
        if ($date === null) {
            return '';
        }

        $carbon = new Carbon($date);
        $string = $carbon->format('D d M Y H:i');
        $string = strtr($string, self::DAYS);
        return ucfirst(strtolower(strtr($string, self::MONTHS)));
    }

    /**
     * Get formatted event begin date
     */
    public function getEventBeginDateAttribute(): string
    {
        return $this->formatDate($this->event_begin);
    }

    /**
     * Get formatted event end date
     */
    public function getEventEndDateAttribute(): string
    {
        return $this->formatDate($this->event_end);
    }

    /**
     * Get formatted signup begin date
     */
    public function getSignupBeginDateAttribute(): string
    {
        return $this->formatDate($this->signup_begin);
    }

    /**
     * Get formatted signup end date
     */
    public function getSignupEndDateAttribute(): string
    {
        return $this->formatDate($this->signup_end);
    }

    /**
     * Check if signup period has started
     */
    public function getSignupBeganAttribute(): bool
    {
        return Carbon::parse($this->signup_begin)->isPast();
    }

    /**
     * Check if signup period has ended
     */
    public function getSignupEndedAttribute(): bool
    {
        return Carbon::parse($this->signup_end)->isPast();
    }

    /**
     * Check if event has started
     */
    public function getEventBeganAttribute(): bool
    {
        return Carbon::parse($this->event_begin)->isPast()
            && Carbon::parse($this->event_end)->isFuture();
    }

    /**
     * Check if event has ended
     */
    public function getEventEndedAttribute(): bool
    {
        return Carbon::parse($this->event_end)->isPast();
    }

    /**
     * Get number of available seats
     * Returns false if unlimited seats (-1)
     * Returns true if no seats (0)
     * Returns number of remaining seats otherwise
     */
    public function getSeatsAvailableAttribute(): bool|int
    {
        if ($this->seats === -1) return false;
        if ($this->seats === 0) return true;
        return $this->seats - $this->registered->count();
    }

    /**
     * Get event duration in days
     */
    public function getDurationDaysAttribute(): int
    {
        return (int)round(Carbon::parse($this->event_begin)
            ->diffInDays(Carbon::parse($this->event_end), false));
    }

    /**
     * Get event duration in hours
     */
    public function getDurationHoursAttribute(): int
    {
        return (int)round(Carbon::parse($this->event_begin)
            ->diffInHours(Carbon::parse($this->event_end), false));
    }

    public function getEventDateAttribute(): string
    {
        $carbon = new Carbon($this->event_begin);
        $string = $carbon->format('D d M Y');
        $string = strtr($string, self::DAYS);
        return ucfirst(strtolower(strtr($string, self::MONTHS)));
    }

    /**
     * Get event start time in H:i format
     */
    public function getEventTimeStartAttribute(): string
    {
        return Carbon::parse($this->event_begin)->format('H:i');
    }

    /**
     * Get event end time in H:i format
     */
    public function getEventTimeEndAttribute(): string
    {
        return Carbon::parse($this->event_end)->format('H:i');
    }

    /**
     * Get event images with proper MIME type
     */
    public function getImagesAttribute(): \Illuminate\Http\Response
    {
        $image = null;
        $mimeType = 'image/png';

        $imagePath = $this->image
            ? storage_path('app/public/images/events/' . $this->image)
            : public_path('images/logos/spillhuset-logo-black.png');

        if (file_exists($imagePath)) {
            $mimeType = mime_content_type($imagePath);
            $image = base64_encode(file_get_contents($imagePath));
        }

        return response($image)->header('Content-Type', $mimeType);
    }
}

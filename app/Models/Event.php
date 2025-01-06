<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;
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
        'location_id',
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
        'event_end_time',
        'event_begin_time',
        'event_date',
    ];

    private const DAYS = [
        'Mon' => 'Man.',
        'Tue' => 'Tir.',
        'Wed' => 'Ons.',
        'Thu' => 'Tor.',
        'Fri' => 'Fre.',
        'Sat' => 'Lør.',
        'Sun' => 'Søn.',
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
        'Dec' => 'Des.',
    ];

    /**
     * Get the location associated with the event
     *
     * @return BelongsTo<Location>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get users registered for the event
     *
     * @return BelongsToMany<User>
     */
    public function registered(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered')->withTimestamps();
    }

    /**
     * Get crew members registered for the event
     *
     * @return BelongsToMany<User>
     */
    public function registeredCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registered_crew')->withTimestamps();
    }

    /**
     * Get users attending the event
     *
     * @return BelongsToMany<User>
     */
    public function attending(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending')->withTimestamps();
    }

    /**
     * Get crew members attending the event
     *
     * @return BelongsToMany<User>
     */
    public function attendingCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attending_crew')->withTimestamps();
    }

    /**
     * Get insider users for the event
     *
     * @return BelongsToMany<User>
     */
    public function insider(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside')->withTimestamps();
    }

    /**
     * Get insider crew members for the event
     *
     * @return BelongsToMany<User>
     */
    public function insiderCrew(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_inside_crew')->withTimestamps();
    }

    private function formatDate(?Carbon $date): string
    {
        if (!$date) {
            return '';
        }

        $formatted = $date->format('D d M Y');
        $formatted = strtr($formatted, self::DAYS);
        return ucfirst(strtolower(strtr($formatted, self::MONTHS)));
    }

    /**
     * Hent formattert startdato for arrangementet.
     */
    public function getEventBeginDateAttribute(): string
    {
        return $this->formatDate($this->event_begin);
    }

    /**
     * Hent formattert sluttdato for arrangementet.
     */
    public function getEventEndDateAttribute(): string
    {
        return $this->formatDate($this->event_end);
    }

    /**
     * Hent formattert registreringsstart-dato.
     */
    public function getSignupBeginDateAttribute(): string
    {
        return $this->formatDate($this->signup_begin);
    }

    /**
     * Hent formattert registreringsslutt-dato.
     */
    public function getSignupEndDateAttribute(): string
    {
        return $this->formatDate($this->signup_end);
    }

    /**
     * Sjekk om registreringsperioden har startet.
     */
    public function getSignupBeganAttribute(): bool
    {
        return optional($this->signup_begin)->isPast() ?? false;
    }

    /**
     * Sjekk om registreringsperioden har sluttet.
     */
    public function getSignupEndedAttribute(): bool
    {
        return optional($this->signup_end)->isPast() ?? false;
    }

    /**
     * Sjekk om arrangementet har startet.
     */
    public function getEventBeganAttribute(): bool
    {
        return optional($this->event_begin)->isPast() && optional($this->event_end)->isFuture();
    }

    /**
     * Sjekk om arrangementet har sluttet.
     */
    public function getEventEndedAttribute(): bool
    {
        return optional($this->event_end)->isPast() ?? false;
    }

    /**
     * Hent antall tilgjengelige seter.
     *
     * @return bool|int False hvis ubegrenset, true hvis ingen seter, eller antall ellers.
     */
    public function getSeatsAvailableAttribute(): bool|int
    {
        if ($this->seats === -1) {
            return false; // Ubegrenset seter
        }

        if ($this->seats === 0) {
            return true; // Ingen seter tilgjengelig
        }

        return $this->seats - $this->registered->count();
    }

    /**
     * Hent arrangementets varighet i dager.
     */
    public function getDurationDaysAttribute(): int
    {
        if (!$this->event_begin || !$this->event_end) {
            return 0; // Returner 0 hvis event_start eller event_end er null
        }

        return $this->event_begin->diffInDays($this->event_end, false);
    }

    /**
     * Hent arrangementets varighet i timer.
     */
    public function getDurationHoursAttribute(): int
    {
        if (!$this->event_begin || !$this->event_end) {
            return 0; // Returner 0 hvis event_start eller event_end er null
        }

        return $this->event_begin->diffInHours($this->event_end, false);
    }

    /**
     * Hent formatert dato for arrangementet.
     */
    public function getEventDateAttribute(): string
    {
        return $this->formatDate($this->event_begin);
    }

    /**
     * Hent starttid for arrangementet i H:i-format.
     */
    public function getEventBeginTimeAttribute(): string
    {
        return optional($this->event_begin)->format('H:i') ?? '';
    }

    /**
     * Hent slutttid for arrangementet i H:i-format.
     */
    public function getEventEndTimeAttribute(): string
    {
        return optional($this->event_end)->format('H:i') ?? '';
    }

    /**
     * Hent bilder med korrekt MIME-type.
     *
     * @return Response
     */
    public function getImagesAttribute(): Response
    {
        $defaultImagePath = public_path('images/logos/spillhuset-logo-black.png');
        $imagePath = $this->image
            ? storage_path('app/public/images/events/' . $this->image)
            : $defaultImagePath;

        if (!file_exists($imagePath)) {
            $imagePath = $defaultImagePath; // Bruk standardbildet hvis bildet ikke finnes
        }

        $mimeType = mime_content_type($imagePath) ?? 'image/png';
        $imageContent = base64_encode(file_get_contents($imagePath));

        return response($imageContent)->header('Content-Type', $mimeType);
    }
}

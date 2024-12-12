<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Phone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country', 'number'
    ];
    protected $appends = ['full_phone'];

    /**
     * Get the users associated with this phone number
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function getFullPhoneAttribute(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phone = $phoneUtil->parse($this->number,'NO');
        $phone->setCountryCode($this->country);
        return $phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = ['user_id', 'avatar', 'birthday', 'citizen', 'bank_account', 'postal_code'];
    protected $appends = ['pegi', 'esrb', 'age', 'image'];
    protected $hidden = ['citizen', 'bank_account'];

    public function getImageAttribute()
    {
        if ($this->avatar == null) {
            return asset('images/silhouette.png');
        } else {
            return asset($this->avatar);
        }
    }

    public function getPegiAttribute(): string
    {
        if ($this->user->active) {
            if ($this->age >= 18) {
                return asset('images/pegi/age-18.jpg');
            }
            if ($this->age >= 16) {
                return asset('images/pegi/age-16.jpg');
            }
            if ($this->age >= 12) {
                return asset('images/pegi/age-12.jpg');
            }
            if ($this->age >= 7) {
                return asset('images/pegi/age-7.jpg');
            } else {
                return asset('images/pegi/age-3.jpg');
            }
        } else {
            return asset('images/pegi/age-3.jpg');
        }
    }

    public function getEsrbAttribute()
    {
        if ($this->user->active) {
            if ($this->age >= 18) {
                return asset('images/esrb/AO.svg');
            }
            if ($this->age >= 17) {
                return asset('images/esrb/M.svg');
            }
            if ($this->age >= 13) {
                return asset('images/esrb/T.svg');
            }
            if ($this->age >= 10) {
                return asset('images/esrb/E10plus.svg');
            } else {
                return asset('images/esrb/E.svg');
            }
        } else {
            return asset('images/esrb/E.svg');
        }
    }

    public function getAgeAttribute(): int
    {
        if ($this->birthday != null) {
            return Carbon::parse($this->birthday)->age;
        } else {
            return 3;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postal(): BelongsTo
    {
        return $this->belongsTo(Postal::class, 'postal_code', 'code');
    }
}

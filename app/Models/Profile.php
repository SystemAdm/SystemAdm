<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = ['user_id', 'avatar', 'birthday', 'citizen', 'bank_account', 'postal_code'];
    protected $appends = ['pegi', 'esrb', 'age', 'image'];
    protected $hidden = ['citizen', 'bank_account'];

    public function getImageAttribute(): string
    {
        return asset($this->avatar ?? 'images/silhouette.png');
    }

    private function getAgeBasedImage(array $ratings): string
    {
        foreach ($ratings as $age => $image) {
            if ($this->age >= $age) {
                return asset($image);
            }
        }
        return asset(end($ratings)); // Default bildestien
    }

    public function getPegiAttribute(): string
    {
        return $this->getAgeBasedImage(config('profile.pegi'));
    }

    public function getEsrbAttribute(): string
    {
        return $this->getAgeBasedImage(config('profile.esrb'));
    }

    public function getAgeAttribute(): ?int
    {
        try {
            return $this->birthday ? Carbon::parse($this->birthday)->age : null;
        } catch (Exception $e) {
            return null;
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

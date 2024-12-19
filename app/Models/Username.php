<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Username extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'username', 'verified_at', 'verified_by', 'primary'];

    protected $casts = ['verified_at' => 'datetime', 'deleted_at' => 'datetime', 'updated_by' => 'datetime', 'created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAsPrimary(): void
    {
        // Fjern primærstatus for tidligere brukernavn
        self::where('user_id', $this->user_id)->update(['primary' => false]);

        // Sett dette brukernavnet som primært
        $this->primary = true;
        $this->save();
    }

    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->verified_at);
    }

    public function setPrimary(bool $primary): void
    {
        if ($primary) {
            $this->setAsPrimary(); // Riktig kontroll for primært brukernavn
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'tld'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the users associated with this email address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the full email address
     *
     * @return string
     */
    public function getFullEmailAttribute(): string
    {
        return "{$this->name}@{$this->domain}.{$this->tld}";
    }
}

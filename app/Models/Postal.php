<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Postal extends Model
{
    use SoftDeletes;

    protected $table = 'postal';
    protected $primaryKey = 'code';

    protected $fillable = ['code', 'name', 'municipality', 'county', 'country'];

    public static function findByCodeOrName(string $query): ?Postal
    {
        return Cache::remember("postal.find.{$query}", now()->addHours(1), function () use ($query) {
            return self::where('code', $query)
                ->orWhereRaw('LOWER(name) like ?', ['%' . strtolower($query) . '%'])
                ->first();
        });
    }
}

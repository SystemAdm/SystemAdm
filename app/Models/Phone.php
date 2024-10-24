<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    function users()
    {
        return $this->belongsToMany(User::class);
    }
}

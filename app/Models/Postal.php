<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postal extends Model
{
    use SoftDeletes;

    protected $table = 'postal';
    protected $primaryKey = 'code';
}

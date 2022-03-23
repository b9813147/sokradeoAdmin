<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'id', 'type', 'expire_date',
    ];
    protected $keyType = 'string';
    public $incrementing = false;
}

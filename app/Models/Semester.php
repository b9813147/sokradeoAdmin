<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['group_id', 'semester_id', 'year', 'month', 'day', 'guid'];
}

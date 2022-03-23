<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupLang extends Model
{
    protected $fillable = ['name', 'description', 'groups_id', 'locales_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['user_id', 'group_id', 'tba_id', 'comment', 'score_data', 'flag', 'type'];
}

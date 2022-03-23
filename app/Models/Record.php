<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'records';
    protected $fillable = [
        'type',
        'user_id',
        'tba_id',
        'district_user_id',
        'group_subject_field_id',
        'district_group_subject_id',
        'district_subject_id',
        'district_channel_content_id',
        'rating_id',
        'group_id',
        'user',
        'district_id',
    ];
}

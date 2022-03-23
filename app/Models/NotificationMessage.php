<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    protected $fillable = [
        'content', 'status', 'validity', 'user_id', 'type', 'group_id'
    ];
}

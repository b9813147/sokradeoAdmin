<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['group_id', 'title'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tbas()
    {
        return $this->morphedByMany('App\Models\Tba', 'content', 'group_channel_contents')
            ->whereNotNull('division_id')
            ->withPivot('content_status', 'content_public')
            ->withTimestamps();
    }

}

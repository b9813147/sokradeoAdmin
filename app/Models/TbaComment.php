<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbaComment extends Model
{
    protected $fillable = [
        'nick_name', 'tag_id', 'tba_id', 'user_id', 'comment_type', 'public', 'time_point', 'text', 'group_id'
    ];

    //
    public function tba(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tba::class);
    }

    //
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //
    public function tag(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    //
    public function tbaCommentAttaches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TbaCommentAttache::class);
    }
}

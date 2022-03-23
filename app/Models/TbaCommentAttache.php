<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbaCommentAttache extends Model
{
    protected $fillable = [
        'name', 'ext', 'image_url', 'preview_url', 'tba_comment_id',
    ];

    //
    public function TbaComment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TbaComment::class);
    }
}

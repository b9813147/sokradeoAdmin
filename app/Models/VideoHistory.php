<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoHistory
 *
 * @property int $user_id
 * @property int $video_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoHistory whereVideoId($value)
 * @mixin \Eloquent
 */
class VideoHistory extends Model
{
    public $timestamps = false;
    
    protected $dates = ['updated_at'];
    
    //
    protected $fillable = [
            'user_id', 'video_id', 'updated_at',
    ];
    
    //
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}

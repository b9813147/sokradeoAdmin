<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoMarkStatistic
 *
 * @property int $video_id
 * @property int $type
 * @property int $time
 * @property int $count
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoMarkStatistic whereVideoId($value)
 * @mixin \Eloquent
 */
class VideoMarkStatistic extends Model
{
    //
    protected $fillable = [
            'video_id', 'type', 'time', 'count',
    ];
    
    //
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}

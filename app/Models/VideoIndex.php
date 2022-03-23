<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoIndex
 *
 * @property int $id
 * @property int $video_id
 * @property string $name
 * @property int $time
 * @property string|null $thumbnail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoIndex whereVideoId($value)
 * @mixin \Eloquent
 */
class VideoIndex extends Model
{
    //
    protected $fillable = [
            'video_id', 'name', 'time', 'thumbnail',
    ];
    
    //
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}

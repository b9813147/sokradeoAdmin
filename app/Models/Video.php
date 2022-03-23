<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video
 *
 * @property int $id
 * @property int $user_id
 * @property int $resource_id
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $hits
 * @property string|null $encoder
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $author
 * @property string|null $copyright
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCopyright($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereEncoder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUserId($value)
 * @mixin \Eloquent
 */
class Video extends Model
{
    protected $fillable = [
        'user_id', 'resource_id', 'name', 'description', 'thumbnail', 'author', 'copyright', 'encoder'
    ];

    //
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    //
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }

    //
    public function videoIndices()
    {
        return $this->hasMany('App\Models\VideoIndex');
    }

    //
    public function videoMarkStatistics()
    {
        return $this->hasMany('App\Models\VideoMarkStatistic');
    }

    //
    public function tbas()
    {
        return $this->belongsToMany('App\Models\Tba')->withPivot('tbavideo_order')->withTimestamps();
    }

    //
    public function tbaVideoMaps()
    {
        return $this->hasMany('App\Models\TbaVideoMap');
    }

    //
    public function groupChannels()
    {
        return $this->morphToMany('App\Models\GroupChannel', 'content', 'group_channel_contents')->withPivot('content_status')->withTimestamps();
    }
}


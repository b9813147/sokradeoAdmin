<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Uri
 *
 * @property int $id
 * @property int $resource_id
 * @property string $url
 * @property int|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Uri whereUrl($value)
 * @mixin \Eloquent
 */
class Uri extends Model
{
    //
    protected $fillable = [
            'resource_id', 'url',
    ];
    
    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
}

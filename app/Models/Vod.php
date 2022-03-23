<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vod
 *
 * @property int $id
 * @property int $resource_id
 * @property string $type
 * @property string $rid
 * @property string $rstatus
 * @property string|null $rdata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereRdata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereRid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereRstatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vod extends Model
{
    //
    protected $fillable = [
        'resource_id', 'type', 'rid', 'rstatus', 'rdata',
    ];

    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaAnnex
 *
 * @property int $id
 * @property int $tba_id
 * @property int $resource_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resources
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnnex whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaAnnex extends Model
{
    protected $fillable = ['resource_id', 'type', 'tba_id', 'size'];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}

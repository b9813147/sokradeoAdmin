<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaFeature
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFeature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaFeature extends Model
{
    //
    protected $fillable = [
            'type'
    ];
}

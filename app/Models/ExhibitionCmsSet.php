<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExhibitionCmsSet
 *
 * @property int $cms_id
 * @property string $cms_type
 * @property string $type
 * @property int|null $order
 * @property string|null $thumbnail 圖片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereCmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereCmsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionCmsSet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExhibitionCmsSet extends Model
{
    //
    protected $fillable = [
            'cms_id', 'cms_type', 'type',
    ];
}

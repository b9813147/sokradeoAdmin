<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictGroup
 *
 * @property int $id
 * @property int $districts_id
 * @property int $groups_id
 * @property int|null $list_order 排序
 * @property int|null $list_top 置頂
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereListOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereListTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictGroup extends Model
{
    protected $table = 'district_groups';
    protected $primaryKey = 'id';
    protected $fillable = ['list_order', 'list_top', 'districts_id', 'groups_id'];

    public function districts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Districts::class);
    }

    public function groups()
    {
        return $this->belongsTo(Group::class);
    }

    public function districtLang()
    {
        return $this->hasOne(DistrictLang::class, 'districts_id', 'districts_id');
    }
}

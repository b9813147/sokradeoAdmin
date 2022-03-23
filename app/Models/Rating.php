<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rating
 *
 * @property int $id
 * @property int|null $groups_id
 * @property int|null $districts_id
 * @property int|null $type 評比等級
 * @property string|null $name 評比名稱
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DistrictChannelContent $districtChannelContent
 * @property-read \App\Models\GroupChannelContent $groupChannelContent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rating extends Model
{
    protected $fillable = ['groups_id', 'type', 'name','districts_id'];

    public function groupChannelContent()
    {
        return $this->hasOne(GroupChannelContent::class, 'ratings_id', 'id');
    }

    public function districtChannelContent()
    {
        return $this->hasOne(DistrictChannelContent::class, 'ratings_id', 'id');
    }
}

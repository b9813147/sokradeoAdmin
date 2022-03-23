<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictUser
 *
 * @property int $id
 * @property int $districts_id
 * @property int $user_id
 * @property int $member_status
 * @property string $member_duty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Districts $district
 * @property-read \App\Models\DistrictLang $districtLang
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereMemberDuty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereMemberStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictUser whereUserId($value)
 * @mixin \Eloquent
 */
class DistrictUser extends Model
{
    protected $fillable = ['user_id', 'districts_id', 'member_status', 'member_duty'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function district()
    {
        return $this->hasOne(Districts::class, 'id', 'districts_id');
    }

    public function districtLang()
    {
        return $this->hasOne(DistrictLang::class, 'districts_id', 'districts_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictChannelContent
 *
 * @property int $id
 * @property int $content_id
 * @property int $districts_id
 * @property int $groups_id
 * @property int $ratings_id
 * @property int|null $grades_id
 * @property int|null $group_subject_fields_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Districts $district
 * @property-read \App\Models\DistrictGroupSubject $districtGroupSubject
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictSubject[] $districtSubject
 * @property-read int|null $district_subject_count
 * @property-read \App\Models\Grade $grade
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\GroupSubjectField $groupSubjectField
 * @property-read \App\Models\Rating $rating
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereGroupSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereRatingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictChannelContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictChannelContent extends Model
{
    protected $fillable = ['ratings_id', 'group_subject_fields_id', 'groups_id', 'grades_id', 'content_id', 'districts_id'];

    public function district()
    {
        return $this->hasOne(Districts::class, 'id', 'districts_id');
    }

    public function grade()
    {
        return $this->hasOne(Grade::class, 'id', 'grades_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'id', 'ratings_id');
    }

    public function districtGroupSubject()
    {
        return $this->hasOne(DistrictGroupSubject::class, 'group_subject_fields_id', 'group_subject_fields_id');
    }

    public function tba()
    {
        return $this->hasOne(Tba::class, 'id', 'content_id');
    }

    public function groupSubjectField()
    {
        return $this->hasOne(GroupSubjectField::class, 'id', 'group_subject_fields_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'groups_id');
    }

    public function districtSubject()
    {
        return $this->hasMany(DistrictSubject::class, 'districts_id', 'districts_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Districts
 *
 * @property int $id
 * @property string|null $abbr 學區代碼
 * @property string|null $school_code 學校代碼
 * @property string|null $thumbnail 縮圖名稱
 * @property int|null $status 狀態
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictGroup[] $districtGroups
 * @property-read int|null $district_groups_count
 * @property-read \App\Models\DistrictLang $districtLang
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictSubjectField[] $districtSubjectField
 * @property-read int|null $district_subject_field_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictTbaInfo[] $districtTbaInfo
 * @property-read int|null $district_tba_info_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereSchoolCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Districts extends Model
{
    protected $table = 'districts';
    protected $primaryKey = 'id';
    protected $fillable = ['abbr', 'school_code', 'status', 'thumbnail'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'district_groups', 'districts_id', 'groups_id');
    }

    public function districtUser()
    {
        return $this->hasMany(DistrictUser::class, 'districts_id', 'id');
    }

    public function districtLang()
    {
        return $this->hasOne(DistrictLang::class);
    }
    public function districtLangs()
    {
        return $this->hasMany(DistrictLang::class);
    }

    public function districtSubjectField()
    {
        return $this->hasMany(DistrictSubjectField::class);
    }

    public function districtTbaInfo()
    {
        return $this->hasMany(DistrictTbaInfo::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'districts_id', 'id');
    }

    public function districtSubjects()
    {
        return $this->hasMany(DistrictSubject::class, 'districts_id', 'id');
    }

    public function districtChannelContents()
    {
        return $this->hasMany(DistrictChannelContent::class, 'districts_id', 'id');
    }
}

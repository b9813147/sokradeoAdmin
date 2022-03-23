<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictGroupSubject extends Model
{
    protected $fillable = ['district_subjects_id', 'group_subject_fields_id'];

    public function districtSubject(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DistrictSubject::class, 'id', 'district_subjects_id');
    }

    public function groupSubjectField(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GroupSubjectField::class, 'id', 'group_subject_fields_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSubjectField extends Model
{
    protected $fillable = ['id', 'subject', 'groups_id', 'subject_fields_id', 'order', 'alias'];

    public function subjectFields(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubjectField::class, 'id', 'subject_fields_id');
    }

    public function groupChannelContent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GroupChannelContent::class, 'group_subject_fields_id', 'id');
    }

    public function districtChannelContent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DistrictChannelContent::class, 'group_subject_fields_id', 'id');
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Group::class, 'id', 'groups_id');
    }

    public function districtGroupSubject(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DistrictGroupSubject::class, 'group_subject_fields_id', 'id');
    }
}

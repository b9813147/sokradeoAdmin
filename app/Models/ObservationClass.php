<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservationClass extends Model
{
    protected $fillable = [
        'user_id',
        'binding_number',
        'classroom_code',
        'pin_code',
        'duration',
        'status',
        'timestamp',
        'group_id',
        'name',
        'content_public',
        'teacher',
        'habook_id',
        'rating_id',
        'group_subject_field_id',
        'grade_id',
        'lecture_date',
        'locale_id',
    ];

    public function observationUsers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ObservationUser::class, 'observation_class_id', 'id');
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}

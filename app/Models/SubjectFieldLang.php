<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectFieldLang
 *
 * @property int $subject_fields_id
 * @property int $locales_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectFieldLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubjectFieldLang extends Model
{
    protected $fillable = ['subject_fields_id', 'locales_id', 'name'];
}

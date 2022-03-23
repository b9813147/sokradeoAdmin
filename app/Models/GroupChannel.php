<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupChannel
 *
 * @property int $id
 * @property int $group_id
 * @property string $cms_type
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $status
 * @property int $public
 * @property int $upload_limit 限制頻道課例上傳 1=啟用 0=停用
 * @property string|null $upload_ended_at 頻道課例上傳截止時間
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tba[] $tbas
 * @property-read int|null $tbas_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereCmsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereUploadEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannel whereUploadLimit($value)
 * @mixin \Eloquent
 */
class GroupChannel extends Model
{
    protected $fillable = ['group_id', 'cms_type', 'name', 'description', 'thumbnail', 'status', 'public', 'stage', 'notification_message_data'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'content', 'group_channel_contents')->withPivot('content_status')->withTimestamps();
    }

    public function tbas()
    {
        return $this->morphedByMany(Tba::class, 'content', 'group_channel_contents')->withPivot('content_status', 'content_public', 'group_subject_fields_id', 'grades_id', 'ratings_id', 'author_id', 'division_id','share_status')->withTimestamps();
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'group_id', 'group_id');
    }

    public function groupSubjectFields()
    {
        return $this->hasMany(GroupSubjectField::class, 'groups_id', 'group_id');
    }

}

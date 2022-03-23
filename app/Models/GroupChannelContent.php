<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupChannelContent
 *
 * @property int $group_id
 * @property int $group_channel_id
 * @property int $content_id
 * @property string $content_type
 * @property int $content_status
 * @property int $content_public
 * @property int|null $group_subject_fields_id
 * @property int|null $grades_id
 * @property int|null $ratings_id
 * @property int|null $author_id 最後修改作者
 * @property int $content_update_limit 限制課例上傳的更新 1=啟用 0=停用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $content
 * @property-read \App\Models\GradeLang $gradesLang
 * @property-read \App\Models\GroupSubjectField $groupSubjectField
 * @property-read \App\Models\Rating $rating
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereContentPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereContentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereContentUpdateLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereGroupChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereGroupSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereRatingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupChannelContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupChannelContent extends Model
{
    protected $fillable = [
        'group_id', 'group_channel_id', 'content_id', 'content_type', 'content_status', 'group_subject_fields_id', 'grades_id', 'ratings_id', 'author_id', 'share_status'
    ];

    public function content()
    {
        return $this->morphTo('content');
    }

    public function groupSubjectField()
    {
        return $this->hasOne(GroupSubjectField::class, 'id', 'group_subject_fields_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'id', 'ratings_id');
    }

    public function gradesLang()
    {
        return $this->hasOne(GradeLang::class, 'grades_id', 'grades_id');
    }
}

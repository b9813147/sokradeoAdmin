<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $school_code
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $status
 * @property int $public
 * @property string $review_status 審核狀態 1=啟用 0=停用
 * @property int $public_note_status 公開點評權限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GroupChannel $channels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group wherePublicNoteStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereReviewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereSchoolCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    protected $fillable = [
        'school_code',
        'name',
        'description',
        'thumbnail',
        'status',
        'public',
        'review_status',
        'public_note_status',
        'event_data',
        'school_upload_status',
        'country_code',
        'notify_status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('member_status', 'member_duty')->withTimestamps();
    }

    public function channels()
    {
        return $this->hasOne(GroupChannel::class);
    }

    public function groupLangs()
    {
        return $this->hasMany(GroupLang::class, 'groups_id', 'id');
    }

    public function groupSubjectFields()
    {
        return $this->hasMany(GroupSubjectField::class, 'groups_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'groups_id', 'id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'group_id', 'id');
    }

    public function notificationMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NotificationMessage::class);
    }

    public function Districts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Districts::class, 'district_groups', 'groups_id', 'districts_id');
    }

    public function tagTypes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TagType::class, 'group_id', 'id');
    }

    public function bbLicenses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(BbLicense::class)->wherePivot('status', 1)->withPivot('storage', 'status', 'deadline');
    }
}

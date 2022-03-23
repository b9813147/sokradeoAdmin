<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tba
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $hits
 * @property int $playlisted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $teacher
 * @property string|null $habook_id 授課老師
 * @property int|null $subject_field_id
 * @property string|null $subject
 * @property int|null $educational_stage_id
 * @property int|null $grade
 * @property int|null $lecture_type
 * @property string|null $lecture_date
 * @property int|null $locale_id
 * @property string|null $mark
 * @property-read \App\Models\EducationalStage|null $educationalStage
 * @property-read \App\Models\GroupChannelContent $groupChannelContent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupChannel[] $groupChannels
 * @property-read int|null $group_channels_count
 * @property-read \App\Models\Locale|null $locale
 * @property-read \App\Models\SubjectField|null $subjectField
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaAnalysisEvent[] $tbaAnalysisEvents
 * @property-read int|null $tba_analysis_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaAnnex[] $tbaAnnexs
 * @property-read int|null $tba_annexs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaEvaluateEvent[] $tbaEvaluateEvents
 * @property-read int|null $tba_evaluate_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaEvaluateUser[] $tbaEvaluateUsers
 * @property-read int|null $tba_evaluate_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaFeature[] $tbaFeatures
 * @property-read int|null $tba_features_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaPlaylistTrack[] $tbaPlaylistTracks
 * @property-read int|null $tba_playlist_tracks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaStatistic[] $tbaStatistics
 * @property-read int|null $tba_statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaVideoMap[] $tbaVideoMaps
 * @property-read int|null $tba_video_maps_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba member($id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereEducationalStageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereHabookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereLectureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereLectureType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba wherePlaylisted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereSubjectFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereTeacher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tba whereUserId($value)
 * @mixin \Eloquent
 */
class Tba extends Model
{
    protected $fillable = [
        'user_id', 'name', 'teacher', 'description', 'thumbnail', 'playlisted',
        'subject_field_id', 'subject', 'educational_stage_id', 'grade',
        'lecture_type', 'lecture_date', 'locale_id', 'mark', 'habook_id',
        'double_green_light_status', 'course_core', 'observation_focus',
        'student_count', 'irs_count', 'binding_number', 'observation_offset'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    //
    public function tbaPlaylistTracks()
    {
        return $this->hasMany('App\Models\TbaPlaylistTrack');
    }

    //
    public function tbaAnalysisEvents()
    {
        return $this->hasMany('App\Models\TbaAnalysisEvent');
    }

    //
    public function tbaEvaluateUsers()
    {
        return $this->hasMany('App\Models\TbaEvaluateUser');
    }

    //
    public function tbaEvaluateEvents()
    {
        return $this->hasMany('App\Models\TbaEvaluateEvent');
    }

    //
    public function tbaStatistics()
    {
        return $this->hasMany('App\Models\TbaStatistic');
    }

    //
    public function tbaFeatures()
    {
        return $this->belongsToMany('App\Models\TbaFeature')->withTimestamps();
    }

    //
    public function tbaAnnexs()
    {
        return $this->hasMany('App\Models\TbaAnnex');
    }

    //
    public function subjectField()
    {
        return $this->belongsTo('App\Models\SubjectField');
    }

    //
    public function educationalStage()
    {
        return $this->belongsTo('App\Models\EducationalStage');
    }

    //
    public function locale()
    {
        return $this->belongsTo('App\Models\Locale');
    }

    //
    public function videos()
    {
        return $this->belongsToMany('App\Models\Video')->withPivot('tbavideo_order')->withTimestamps();
    }

    //
    public function tbaVideoMaps()
    {
        return $this->hasMany('App\Models\TbaVideoMap');
    }

    //
    public function groupChannels()
    {
        return $this->morphToMany('App\Models\GroupChannel', 'content', 'group_channel_contents')->withPivot('content_status', 'content_public')->withTimestamps();
    }

    public function scopeMember($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function groupChannelContent()
    {
        return $this->hasOne(GroupChannelContent::class, 'content_id', 'id');
    }
}

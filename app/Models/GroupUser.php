<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupUser
 *
 * @property int $group_id
 * @property int $user_id
 * @property int $member_status
 * @property string|null $member_duty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereMemberDuty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereMemberStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupUser whereUserId($value)
 * @mixin \Eloquent
 */
class GroupUser extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    protected $table = 'group_user';
    protected $fillable = [
        'group_id',
        'user_id',
        'member_status',
        'member_duty',
    ];

}

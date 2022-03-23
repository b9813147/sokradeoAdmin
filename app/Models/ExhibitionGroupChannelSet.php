<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExhibitionGroupChannelSet
 *
 * @property int $group_channel_id
 * @property string $type
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet whereGroupChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExhibitionGroupChannelSet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExhibitionGroupChannelSet extends Model
{
    //
    protected $fillable = [
            'group_channel_id', 'type', 'order',
    ];
}

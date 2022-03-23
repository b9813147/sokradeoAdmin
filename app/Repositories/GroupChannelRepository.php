<?php

namespace App\Repositories;

use App\Models\GroupChannel;
use Illuminate\Database\Query\Builder;

class GroupChannelRepository extends BaseRepository
{
    /**
     * @var GroupChannel
     * @var $model Builder
     */
    protected $model;

    public function __construct(GroupChannel $groupChannel)
    {
        $this->model = $groupChannel;
    }

    /**
     * 取得頻道下的所有影片
     *
     * @param int $channel_id
     * @return GroupChannel|GroupChannel[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getGroupChannelByTba(int $channel_id)
    {
        return $this->model->query()->with([
            'groupSubjectFields' => function ($q) {
                $q->select('id', 'alias', 'groups_id')->orderBy('order');
            },
            'group'              => function ($q) {
                $q->with([
                    'bbLicenses' => function ($q) {
                        $q->where('code', 'LY9AJ6NY');
                    }
                ]);
            },
            'semesters'          => function ($q) {
                $q->select('semester_id', 'month', 'day', 'group_id', 'id')->orderBy('id');
            },
            'tbas'               => function ($q) {
                $q->orderBy('id');
                $q->with([
                    'tbaPlaylistTracks' => function ($q) {
                        $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                            ->orderBy('tba_playlist_tracks.list_order');
                    },
                    'videos'            => function ($q) {
                        $q->select('resource_id', 'id', 'encoder')->orderBy('id');
                        $q->with([
                            'resource' => function ($q) {
                                $q->select('id')->orderBy('id');
                                $q->with([
                                    'vod' => function ($q) {
                                        $q->select('resource_id', 'rdata->file_size as file_size');
                                        $q->orderBy('resource_id');
                                    }
                                ]);
                            }
                        ]);
                    },
//                    'tbaStatistics'     => function ($q) {
//                        $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T
//                            ,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P
//                            ,tba_statistics.tba_id
//                            ,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
//                            ->groupBy('tba_statistics.tba_id');
//                    },
                    'tbaAnnexs'         => function ($q) {
                        $q->select('tba_id', 'id', 'type', 'size', 'resource_id')->orderBy('tba_id');
                        $q->with([
                            'resource' => function ($q) {
                                $q->select('id')->orderBy('id');
                            }
                        ]);

                    }
                ]);
            }
        ])->findOrFail($channel_id);
    }

    /**
     * 取得頻道成員
     * @param array $channel_ids
     * @return \Illuminate\Support\Collection
     */
    public function getChannelForUser(array $channel_ids): ?\Illuminate\Support\Collection
    {
        return $this->model->query()
            ->join('groups', 'groups.id', 'group_channels.group_id')
            ->join('group_user', 'group_user.group_id', 'group_channels.group_id')
            ->whereIn('group_channels.id', $channel_ids)->distinct('group_user.user_id')->pluck('group_user.user_id');
    }

    /**
     * @param int $notification_message_id
     * @param string $vale
     * @return bool|int
     */
    public function updateByNotificationId(int $notification_message_id, string $vale)
    {
        return $this->model->query()->whereJsonContains('notification_message_data->notification_message_id', $notification_message_id)->update(['notification_message_data' => $vale]);
    }
}

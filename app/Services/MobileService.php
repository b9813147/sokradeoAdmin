<?php

namespace App\Services;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\Tba;
use App\Models\TbaAnnex;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\TbaFavorite;
use App\Models\TbaHistory;
use App\Models\User;
use App\Repositories\TbaCommentRepository;
use App\Types\Tba\AnnexType;

class MobileService
{

    protected $tbaCommentRepository;

    public function __construct(TbaCommentRepository $tbaCommentRepository)
    {
        $this->tbaCommentRepository = $tbaCommentRepository;
    }


    /**
     * 統計學區
     * @param int $userId
     * @return array
     */
    public function getUserVideoCount(int $userId): array
    {
        $user    = User::query()->findOrFail($userId);
        $tba_ids = Tba::query()->where('habook_id', $user->habook)->pluck('id');

        // 全頻道公開
        $publicTotal = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [1], [1]);

        //  影片總數
        $total = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [0, 1], [1, 2]);

        // 個人影片
        $contentIds = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [0, 1], [1, 2]);

        // For tbaStatistics Sql
        $select = 'MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T
                  ,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P
                  ,tba_statistics.tba_id
                  ,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C';

        $mapTba = Tba::query()->whereIn('id', explode(',', $contentIds->tba_ids))->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) use ($select) {
                $q->selectRaw($select)
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');
        // 雙綠燈
        $doubleGreenLightTotal            = collect();
        $doubleGreenLightTotal['tba_ids'] = collect();
        $doubleGreenLightTotal['total']   = $mapTba->get()->filter(function ($tba) use ($doubleGreenLightTotal) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    $doubleGreenLightTotal['tba_ids']->push($tba->tbaStatistics->first()->tba_id);
                    return $tba->tbaStatistics;
                }
            }
        })->count();


        // Comments
        $publicMark        = (object)[];
        $publicMark->total = number_format($this->getPublicCommentsQueryByUser($userId)->count());
//        $publicMark->tba_ids = $this->getPublicCommentsQueryByUser($userId)->pluck('tba_id');

        // 本人標記
        $privateMark        = (object)[];
        $privateMark->total = number_format($this->getPrivateCommentsQueryByUser($userId)->count());
//        $privateMark->tba_ids = $this->getPrivateCommentsQueryByUser($userId)->pluck('tba_id');
        // 個人評論數
        $personalComment        = (object)[];
        $personalComment->total = number_format($this->getPersonalByPoint($userId));


        // 影片公開 標記數點評的數量
        $tbaEvaluateEventUserId = TbaEvaluateUser::query()->whereIn('tba_id', explode(',', $contentIds->tba_ids))->where('identity', '!=', 'G')->pluck('id');
        $isMark                 = (object)[];
        $isMark->total          = number_format(TbaEvaluateEvent::query()->whereIn('tba_evaluate_user_id', $tbaEvaluateEventUserId)->count());
//        $isMark->tba_ids        = TbaEvaluateEvent::query()->whereIn('tba_evaluate_user_id', $tbaEvaluateEventUserId)->pluck('tba_id');

        // 公開影片數
        $public = (object)[];

        $public->total = number_format($publicTotal->total);
//        $public->tba_ids = explode(',', $publicTotal->tba_ids);

        // 影片總數
        $all        = (object)[];
        $all->total = number_format($total->total);
//        $all->tba_ids = explode(',', $total->tba_ids);

        // 雙綠燈
        $doubleGreenLight        = (object)[];
        $doubleGreenLight->total = number_format($doubleGreenLightTotal->get('total'));
//        $doubleGreenLight->tba_ids = $doubleGreenLightTotal->get('tba_ids')->toArray();

        // 點閱數
        $hits_total = (object)[];
//        $hits_total->tba_ids = $mapTba->where('hits', '!=', 0)->pluck('id');
        $hits_total->total = number_format((int)$mapTba->sum('hits'));

        // Annex file
        $material          = $this->tbaAnnex($tba_ids, AnnexType::Material);
        $material->total   = number_format((int)$material->total);
        $lessonPlan        = $this->tbaAnnex($tba_ids, AnnexType::LessonPlan);
        $lessonPlan->total = number_format((int)$lessonPlan->total);

        // Favorite Videos
        $favoriteVideos        = (object)[];
        $favoriteVideos->total = number_format(TbaFavorite::query()->where('user_id', $userId)->count());

        // Watch History
        $watchHistory        = (object)[];
        $watchHistory->total = number_format(TbaHistory::query()->where('user_id', $userId)->where('url', '!=', null)->count());

        return [
            'public_total'           => $public,
            'hits_total'             => $hits_total,
            'all_total'              => $all,
            'doubleGreenLight_total' => $doubleGreenLight,
            'private_mark'           => $privateMark,
            'personal_comment'       => $personalComment,
            'total_mark'             => $isMark,
            'public_mark'            => $publicMark,
            'material'               => $material,
            'lessonPlan'             => $lessonPlan,
            'favoriteVideos'         => $favoriteVideos,
            'watchHistory'           => $watchHistory,
        ];
    }

    /**
     * 指定附件 類別 統計 及 細目
     * @param $tba_ids
     * @param string $AnnexType
     * @return mixed
     */
    private function tbaAnnex($tba_ids, string $AnnexType)
    {
        $select = "COUNT(CASE WHEN type = '$AnnexType' THEN tba_id END)   AS total";
        return TbaAnnex::query()->selectRaw($select)->where('type', "$AnnexType")
            ->whereIn('tba_id', $tba_ids)->get()->first();
    }

    private function getPublicCommentsQueryByUser($userId)
    {
        $distinctSql                   = $this->getDistinctSqlForComments();
        $tbaEvaluateEventUserIdByOwner = TbaEvaluateUser::query()
            ->selectRaw($distinctSql)
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tba_evaluate_users.tba_id')
            ->where('user_id', $userId)
            ->pluck('id');
        $publicMark                    = TbaEvaluateEvent::query()->whereIn('tba_evaluate_user_id', $tbaEvaluateEventUserIdByOwner);
        return $publicMark;
    }

    private function getDistinctSqlForComments(): string
    {
        return 'distinct group_channel_contents.content_id, id';
    }

    private function getPrivateCommentsQueryByUser($userId)
    {
        $privateMark = TbaEvaluateEvent::query()->where('user_id', $userId);
        return $privateMark;
    }

    /**
     * 取個人點評數
     *
     * @param int $user_id
     * @return int
     */
    public function getPersonalByPoint(int $user_id): int
    {
        return $this->tbaCommentRepository->personalByPoint($user_id);
    }
}

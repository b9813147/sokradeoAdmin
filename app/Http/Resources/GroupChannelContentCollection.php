<?php

namespace App\Http\Resources;

use App\Models\GradeLang;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupChannelContentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($groupChannelContentInfo) {
            $status = null;
            $alias  = null;
            $grade  = null;
            $rating = null;
            if ($groupChannelContentInfo->groupChannelContent instanceof GroupChannelContent) {
                $groupChannelContent = $groupChannelContentInfo->groupChannelContent;

                if ($groupChannelContent->content_status === 1 && $groupChannelContent->content_public === 1) {
                    $status = 3; // 全平台分享  share (1, 1) 3
                } elseif ($groupChannelContent->content_status === 1 && $groupChannelContent->content_public === 0) {
                    $status = 2; // 頻道內觀摩  valid (1, 0)  2
                } elseif ($groupChannelContent->content_status === 2 && $groupChannelContent->content_public === 0) {
                    $status = 4; // 尚待審核中  pending (2, 0) 4
                } else {
                    $status = 1; // 無效不顯示  invalid (0, 0) 1
                }
                // 學科
                if ($groupChannelContent->groupSubjectField instanceof GroupSubjectField) {
                    $alias = [
                        'text'  => $groupChannelContent->groupSubjectField->alias,
                        'value' => $groupChannelContent->groupSubjectField->id
                    ];
                }
                // 年級
                if ($groupChannelContent->gradesLang instanceof GradeLang) {
                    $grade = [
                        'text'  => $groupChannelContent->gradesLang->name,
                        'value' => $groupChannelContent->gradesLang->grades_id
                    ];
                }
                // 增加評比
                if ($groupChannelContent->rating instanceof Rating) {
                    $rating = [
                        'text'  => $groupChannelContent->rating->name ?: \Lang::get("rating." . $groupChannelContent->rating->type),
                        'value' => $groupChannelContent->rating->id,
                    ];
                }
            }


            return [
                'id'                => $groupChannelContentInfo->id,
                'name'              => $groupChannelContentInfo->name,
                'status'            => $status,
                'thumbnail'         => $groupChannelContentInfo->thumbnail,
                'date'              => $groupChannelContentInfo->date,
                'alias'             => $alias,
                'rating'            => $rating,
                'grade'             => $grade,
                'description'       => $groupChannelContentInfo->description,
                'teacher'           => $groupChannelContentInfo->teacher,
                'tba_statistics'    => $groupChannelContentInfo->tbaStatistics,
                'habook_id'         => $groupChannelContentInfo->habook_id,
                'course_core'       => $groupChannelContentInfo->course_core,
                'observation_focus' => $groupChannelContentInfo->observation_focus,
                'share_status'      => $groupChannelContentInfo->groupChannelContent->share_status,
            ];
        });
    }
}

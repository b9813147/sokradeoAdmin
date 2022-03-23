<?php

namespace App\Http\Controllers\Api\V1\App\School;


use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Models\Group;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use App\Models\Tag;
use App\Models\TagType;

class ObservationController extends Controller
{

    /**
     * @param string $school_shortcode
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $school_shortcode): \Illuminate\Http\JsonResponse
    {

        $group_id = GlobalPlatform::convertAbbrToGroupId($school_shortcode) ?? null;
        if (!$group_id) {
            return $this->setStatus('404')->fail('School shortcode does not exist');
        }
        $groupInfo = Group::query()->find($group_id);

        $result = collect();
        $result->put('public_level', (int)$groupInfo->review_status === 1 ? 1 : 3);
        $result->put('available_storage', 1000000);
        // Ratings
        $ratings = Rating::query()->where('groups_id', $group_id)->select('id', 'name', 'type')->orderBy('id')->get()->toArray();
        $result->put('study_level', $ratings);
        // Subject
        $subjects = GroupSubjectField::query()->where('groups_id', $group_id)->select('id', 'alias')->orderBy('order', 'desc')->get()->toArray();
        $result->put('subjects', $subjects);

        // School TagType
        $school['tableType'] = 'school';
        $school['types']     = TagType::query()->where('status',1)->where('group_id', $group_id)->orderBy('order')->get()->map(function ($tagType) {
            $tags = Tag::query()->where('status',1)->where('type_id', $tagType->id)->select('id', 'content')->orderBy('id')->get()->map(function ($tag) {
                $tagContents = collect(json_decode($tag->content, true));
                $tagContents->put('id', $tag->id);
                return $tagContents->toArray();
            })->toArray();
            return [
                'typeId'   => $tagType->id,
                'order'    => $tagType->order,
                'typeName' => json_decode($tagType->content, true),
                'tagList'  => $tags,
            ];
        })->toArray();
        // Tmd TagType
        $tmd['tableType'] = 'tmd';
        $tmd['types']     = TagType::query()->where('status',1)->whereNull('group_id')->whereNull('user_id')->orderBy('order', 'ASC')->get()->map(function ($tagType) {
            $tags = Tag::query()->where('status',1)->where('type_id', $tagType->id)->select('id', 'content')->orderBy('id')->get()->map(function ($tag) {
                $tagContents = collect(json_decode($tag->content, true));
                $tagContents->put('id', $tag->id);
                return $tagContents->toArray();
            })->toArray();
            return [
                'typeId'   => $tagType->id,
                'order'    => $tagType->order,
                'typeName' => json_decode($tagType->content, true),
                'tagList'  => $tags,

            ];
        })->toArray();

        $tags = [
            $school,
            $tmd,
        ];
        $result->put('tags', $tags);

        return $this->success($result);
    }
}

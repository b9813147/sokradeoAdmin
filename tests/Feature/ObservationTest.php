<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use App\Models\Tag;
use App\Models\TagType;
use Tests\TestCase;

class ObservationTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_get()
    {

        $groupChannelData = [
            'ratings_id'   => 1,
//            'group_subject_fields_id' => $info->get('subject'),
            'grades_id'    => 2,
            'group_id'     => 3,
            'author_id'    => 9,
            'share_status' => 1
        ];

        $content_public   = ['status' => 1, 'public' => 2];
        $subject          = [''];
        $groupChannelData = array_merge($groupChannelData, $content_public, $subject);
        dd($groupChannelData);
        $school_shortcode = 'HBEN';
        $group_id         = GlobalPlatform::convertAbbrToGroupId($school_shortcode);
        $result           = collect();
        $result->put('public_level', 3);
        $result->put('available_storage', 1000000);
        // Ratings
        $ratings = Rating::query()->where('groups_id', $group_id)->select('id', 'name')->orderBy('id')->get()->toArray();
        $result->put('study_level', $ratings);
        // Subject
        $subjects = GroupSubjectField::query()->where('groups_id', $group_id)->select('id', 'name')->orderBy('id')->get()->toArray();
        $result->put('subjects', $subjects);
        $tags = [];
        // TagType
        $tagType = TagType::query()->where('group_id', $group_id)->orderBy('id')->get()->map(function ($q) use ($tags) {
            $tag    = Tag::query()->where('type_id', $q->id)->select('id', 'content')->orderBy('id')->get()->map(function ($q) {
                $tags = collect(json_decode($q->content, true));
                $tags->put('id', $q->id);
                return $tags->toArray();
            })->toArray();
            $tags[] = [
                'tableType' => 'tmd',
                'types'     => [
                    'typeId'   => $q->id,
                    'typeName' => [
                        json_decode($q->content, true),
                    ],
                    'tagList'  => [
                        $tag,
                    ]
                ]
            ];
            return $tags;
        });

        $result->put('tags', $tagType);
        dd($result->toArray());


    }

    public function test_logic()
    {
        function check($value){
            if(isset($value)){
                echo "isset()?????????????????? \n";
            }else{
                echo "isset()?????????????????? \n";
            }

            if(empty($value)){
                echo "empty()??????????????? \n";
            }else{
                echo "empty()???????????? \n";
            }

            if(is_null($value)){
                echo "is_null()??????????????? \n";
            }else{
                echo "is_null()???????????? \n";
            }
        }

        echo "??????value1????????????\n";
        $value1=null;
        check($value1);

//        echo "<br />";
//
//        echo "??????value1??????null<hr />";
//        $value1=null;
//        check($value1);
//
//        echo "<br />";
//
//        echo "??????value1???????????????<hr />";
//        $value1=array();
//        check($value1);
//
//        echo "<br />";
//
//        echo "??????value1???<hr />";
//        unset($value1);
//        @check($value1);
//
//        echo "<br />";
//
//        echo "??????value1??????0<hr />";
//        $value1="0";
//        check($value1);

    }
}

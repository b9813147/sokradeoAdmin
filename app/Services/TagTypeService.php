<?php

namespace App\Services;

use App\Models\TagType;
use App\Repositories\TagTypeRepository;

class TagTypeService extends BaseService
{
    protected $repository;

    /**
     * @param TagTypeRepository $repository
     */
    public function __construct(TagTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Convert comment tag type structure
     * @param TagType $commentTagType
     * @return array
     */
    private function convertCommentTagType(TagType $commentTagType): array
    {
        return [
            'id'        => $commentTagType->id,
            'group_id'  => $commentTagType->group_id,
            'habook_id' => $commentTagType->habook_id,
            'name'      => $this->getNameFromTagTypeContent($commentTagType->content),
            'tags'      => $commentTagType->tag->map(function ($tag) {
                return [
                    'id'          => $tag->id,
                    'is_positive' => $tag->is_positive,
                    'content'     => $this->getTagDataFromTagContent($tag->content),
                ];
            })->toArray(),
        ];
    }


    /**
     * Get name from Tag Type content
     * @param string $content - ex.: {"cn": "Bloom认知分类 (2001 Revised)", "en": "Bloom‘s taxonomy (Revised)", "tw": "Bloom認知分類 (2001 Revised)", "customize": null}
     * @return string
     */
    public function getNameFromTagTypeContent(string $content): string
    {
        $content = json_decode($content, true);
        $locale  = !empty($content['customize'])
            ? 'customize'
            : \App::getLocale();

        return $content[$locale];
    }

    /**
     * Get Tag data from Tag content
     * @param string $content - ex.: {"name": {"cn": "记忆", "en": "Remember", "tw": "記憶", "customize": null}, "description": {"cn": "从长期记忆中提取相关知识", "en": "Retrieve related knowledge from long term memory", "tw": "從長期記憶中提取相關知識", "customize": null}}
     * @return array - ['name' => '', 'desc' => '']
     */
    public function getTagDataFromTagContent(string $content): array
    {
        $content = json_decode($content, true);
        $locale  = !empty($content['name']['customize']) || !empty($content['description']['customize'])
            ? 'customize'
            : \App::getLocale();

        return [
            'name' => $content['name'][$locale],
            'desc' => $content['description'][$locale],
        ];
    }


}

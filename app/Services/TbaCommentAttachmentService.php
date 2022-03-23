<?php

namespace App\Services;

use App\Repositories\TbaCommentAttachmentRepository;

class TbaCommentAttachmentService extends BaseService
{
    protected $repository;

    /**
     * @param TbaCommentAttachmentRepository $repository
     */
    public function __construct(TbaCommentAttachmentRepository $repository)
    {
        $this->repository = $repository;
    }

}

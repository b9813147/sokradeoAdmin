<?php

namespace App\Services;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\TbaComment as CommentModel;
use App\Models\TbaCommentAttach as CommentAttachModel;
use App\Models\TbaCommentAttache;
use App\Repositories\TbaCommentRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TbaCommentService extends BaseService
{
    /**
     * @var TbaCommentRepository
     */
    protected $repository;
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var tbaCommentBlobMediaService
     */
    protected $tbaCommentBlobMediaService;
    /**
     * @var TbaCommentAttachmentService
     */
    protected $tbaCommentAttachmentService;
    /**
     * @var array|string[]
     */
    protected $blobAllowedVideoExtentions = [];
    /**
     * @var array|string[]
     */
    protected $blobAllowedMiscExtentions = [];
    /**
     * @var array|string[]
     */
    protected $blobAllowedExtensions = [];
    /**
     * @var array
     */
    protected $localAllowedExtensions = [];
    /**
     * @var TagTypeService
     */
    protected $tagTypeService;

    /**
     * @param TbaCommentRepository $tbaCommentRepository
     * @param UserService $userService
     * @param tbaCommentBlobMediaService $tbaCommentBlobMediaService
     * @param TbaCommentAttachmentService $tbaCommentAttachmentService
     * @param TagTypeService $tagTypeService
     */
    public function __construct(
        TbaCommentRepository        $tbaCommentRepository,
        UserService                 $userService,
        TbaCommentBlobMediaService  $tbaCommentBlobMediaService,
        TbaCommentAttachmentService $tbaCommentAttachmentService,
        TagTypeService              $tagTypeService
    )
    {
        $this->repository                  = $tbaCommentRepository;
        $this->userService                 = $userService;
        $this->tagTypeService              = $tagTypeService;
        $this->tbaCommentBlobMediaService  = $tbaCommentBlobMediaService;
        $this->tbaCommentAttachmentService = $tbaCommentAttachmentService;
        $this->blobAllowedVideoExtentions  = $this->tbaCommentBlobMediaService->blobAllowedVideoExtentions();
        $this->blobAllowedMiscExtentions   = $this->tbaCommentBlobMediaService->blobAllowedMiscExtentions();
        $this->blobAllowedExtensions       = $this->tbaCommentBlobMediaService->getBlobAllowedExtensions();
        $this->localAllowedExtensions      = []; // currently empty

    }

    /**
     * @param int $tba_id
     * @param $users
     */
    public function createUserCommentGroup(int $tba_id, &$users)
    {
        foreach ($users as $user) {
            $user_id         = $this->userService->firstWhere(['habook' => $user['accUser']])->id;
            $user['user_id'] = $user_id;
            $this->repository->createUserCommentGroups($tba_id, $user);
        }
    }

    /**
     * @param  $comment
     * @param array $fileData - [tbaId, commentId, name, ext, size, path]
     */
    public function createCommentAttachment($comment, array $fileData)
    {
        // Create comment in db
//        $comment = $this->repository->create($comment);
        // Upload file and create data in db
        $file = (bool)strpos(get_headers($fileData['path'])[0], '200');
        if (!empty($fileData) && $file && $comment) {
            $attachmentData = $this->getAttachmentData($comment->tba_id, $comment->id, $fileData);
            // Media type
            if (in_array($attachmentData['ext'], $this->blobAllowedExtensions))
                $this->commentMediaFileHandler($attachmentData);

            // Types for local storage
            else if (in_array($attachmentData['ext'], $this->localAllowedExtensions))
                $this->commentLocalFileHandler($attachmentData);
        }
    }

    /**
     * Handle comment file upload
     * @param array $attachmentData - [tbaId, commentId, name, ext, size, path]
     */
    private function commentMediaFileHandler(array $attachmentData)
    {
        try {
            // Extract essential data
            $blobDestDir     = $attachmentData['commentId'];
            $fileNameWithExt = $attachmentData['name'] . '.' . $attachmentData['ext']; // 6666666.mp4
            $blobDestPath    = $blobDestDir . '/' . $fileNameWithExt; // 123523/6666666.mp4
            $fileSrcDir      = $attachmentData['path'];

            // Execute methods
            $this->clearCommentFileDirs($attachmentData['tbaId'], $attachmentData['commentId']); // clear files
            $this->tbaCommentBlobMediaService->uploadMediaBlob($blobDestPath, $fileSrcDir); // upload file

            // Update comment attachment
            $this->tbaCommentAttachmentService->updateOrCreate(['tba_comment_id' => $attachmentData['commentId']], $attachmentData);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Remove all uploaded files in local storage and blob based on comment dir
     * @param int $tbaId
     * @param int $commentId
     */
    private function clearCommentFileDirs(int $tbaId, int $commentId)
    {
        $this->clearCommentMediaDir($commentId);
        $this->clearCommentLocalFileDir($tbaId, $commentId);
    }

    /**
     * Remove all uploaded files in blob storag
     * @param int $commentId
     */
    private function clearCommentMediaDir(int $commentId)
    {
        $this->tbaCommentBlobMediaService->deleteMediaBlobDir($commentId);
    }

    /**
     * Remove all uploaded files in local storag
     * @param int $tbaId
     * @param int $commentId
     */
    private function clearCommentLocalFileDir(int $tbaId, int $commentId)
    {
        $subImgDir  = $tbaId . '/evaluate_event_file/' . $commentId;
        $fullImgDir = storage_path('app/public/tba/' . $subImgDir);
        if (File::exists($fullImgDir))
            File::cleanDirectory($fullImgDir);
    }

    /**
     * Create a comment attachment data structure for file handler
     * @param int $tbaId
     * @param int $commentId
     * @param array $fileData - [name, size, tmp_name, type]
     * @return array - [tbaId, commentId, name, ext, size, path]
     */
    private function getAttachmentData(int $tbaId, int $commentId, array $fileData): array
    {
        return [
            'tbaId'     => $tbaId,
            'commentId' => $commentId,
            'name'      => time(), // file name is timestamp
            'ext'       => $fileData['ext'],
            'size'      => $fileData['size'],
            'path'      => $fileData['path'],
        ];
    }

    /**
     * Handle comment local file upload
     * @param array $attachmentData - [tbaId, commentId, name, ext, size, path]
     */
    private function commentLocalFileHandler(array $attachmentData)
    {
        try {
            // Extract essential data
            $subImgDir  = $attachmentData['tbaId'] . '/evaluate_event_file/' . $attachmentData['commentId'];
            $fullImgDir = storage_path('app/public/tba/' . $subImgDir);

            $fileNameWithExt = $attachmentData['name'] . '.' . $attachmentData['ext']; // 6666666.mp4
            $fileSrcDir      = $attachmentData['path'];

            // Execute methods
            $this->clearCommentFileDirs($attachmentData['tbaId'], $attachmentData['commentId']); // clear files
            Storage::makeDirectory('public/tba/' . $subImgDir); // create dir
            copy($fileSrcDir, $fullImgDir . '/' . $fileNameWithExt);

            // Update comment attachment
            $imageUrl = url('storage/tba/' . $subImgDir . '/' . $fileNameWithExt);
            $this->repository->upsertCommentImgAttachment($attachmentData['commentId'], $imageUrl);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Display all comments based on the given tbaId.
     * @param int $tbaId
     * @param null|int $public - [optional] 1 for public, 0 for private
     * @return array
     */
    public function getComments(int $tbaId, int $public = null): array
    {
        $comments = $this->repository->getComments($tbaId, $public);
        $comments = $comments->map(function ($comment) {
            return $this->convertComment($comment);
        });
        return $comments->toArray();
    }

    /**
     * Convert new structure to old structure
     * @param CommentModel $comment
     * @return array
     */
    private function convertComment(CommentModel $comment): array
    {
        $file = $comment->tbaCommentAttaches->first();

        return [
            'id'          => $comment->id,
            'type'        => (!empty($comment->tag->tagType->content))
                ? $this->tagTypeService->getNameFromTagTypeContent($comment->tag->tagType->content)
                : null,
            'tag'         => (!empty($comment->tag->content))
                ? $this->tagTypeService->getTagDataFromTagContent($comment->tag->content)
                : null,
            'is_positive' => $comment->tag->is_positive,
            'time'        => $comment->time_point,
            'text'        => $comment->text,
            'nick_name'   => $comment->nick_name,
            'user'        => $comment->user,
            'attachment'  => $this->convertCommentAttachToAttachmentData($file),
            'tba'         => $comment->tba,
            'group_id'    => (!empty($comment->group_id))
                ? $comment->group_id
                : null,
            'channel_id'  => (!empty($comment->group_id))
                ? GlobalPlatform::convertGroupIdToChannelId($comment->group_id)
                : null,
            'created_at'  => $comment->created_at,
            'updated_at'  => $comment->updated_at,
        ];
    }

    /**
     * Convert file to attachment data
     * @param TbaCommentAttache $commentAttach
     * @return array - ['src' => url ir null, 'ext' => ext or null, 'type' => 'image' or 'media' or null]
     */
    private function convertCommentAttachToAttachmentData($commentAttach): array
    {
        $attachmentData = ['src' => null, 'ext' => null, 'type' => null];

        // If no attachment, return attachment null
        if (empty($commentAttach))
            return $attachmentData;

        // If local image, return image url
        // Assuming image_url is always local image
        if ($commentAttach->image_url) {
            $attachmentData['src'] = $commentAttach->image_url;
            $attachmentData['ext'] = pathinfo($commentAttach->image_url, PATHINFO_EXTENSION);
        }

        // If blob media, return blob url with SAS
        if ($commentAttach->name && $commentAttach->ext) {
            $attachmentData['src'] = $this->tbaCommentBlobMediaService->getBlobSASLink(
                $commentAttach->tba_comment_id,
                $commentAttach->name . "." . $commentAttach->ext // abc.mp4
            );
            $attachmentData['ext'] = $commentAttach->ext;
        }

        // Set up type
        if (in_array($attachmentData['ext'], $this->blobAllowedVideoExtentions)) {
            $attachmentData['type'] = 'media';
        } else if (in_array($attachmentData['ext'], $this->blobAllowedMiscExtentions)) {
            $attachmentData['type'] = 'image';
        }

        return $attachmentData;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function convertTbaCommentToTbaEvaluateEvent(int $id): bool
    {
        return $this->repository->convertTbaCommentToTbaEvaluateEvent($id);
    }
}

<?php

namespace App\Services\Src;

use App\Helpers\Path\File as FilePath;
use App\Repositories\FileRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileService extends BaseService
{
    use FilePath;

    protected $repository;

    //
    public function __construct(FileRepository $fileRepository)
    {
        $this->repository = $fileRepository;
    }

    /**
     * @param $resrcId
     * @param $src
     */
    public function createSrc($resrcId, $src)
    {
        $file = $this->repository->createFile($resrcId, [
            'name' => $src['name'],
            'ext'  => $src['ext'],
        ]);

        (is_string($src['file']))
            ? $this->moveFile($file->id, $src['file'])
            : $this->uploadFile($file->id, $src['file']);
    }

    public function getDetail($src)
    {
        // 待實作
    }

    public function getExecuting($src)
    {
        $name = $src->name . '.' . $src->ext;
        return Response::download($this->pathFile($src->id, true), $name);
    }

    /**
     * @param string $file local file path
     */
    protected function moveFile($fileId, $file)
    {
        if (!Storage::exists($file)) {
            return;
        }

        $tar = $this->pathFile($fileId);
        if (Storage::exists($old = $this->pathFile($fileId), true)) {
            Storage::delete($old);
        }

        Storage::move($file, $tar);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file uploaded file
     */
    protected function uploadFile($fileId, $file)
    {
        // 待實作
    }

}

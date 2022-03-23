<?php

namespace App\Services;

use App\Repositories\FileRepository;
use App\Repositories\ResourceRepository;
use App\Repositories\TbaAnnexRepository;
use App\Types\Src\SrcType;
use Yish\Generators\Foundation\Service\Service;

class AnnexService extends Service
{
    protected $tbaAnnexRepository;
    protected $resourceRepository;
    protected $fileRepository;

    /**
     * TbaAnnexService constructor.
     * @param TbaAnnexRepository $tbaAnnexRepository
     * @param ResourceRepository $resourceRepository
     * @param FileRepository $fileRepository
     */
    public function __construct(TbaAnnexRepository $tbaAnnexRepository, ResourceRepository $resourceRepository, FileRepository $fileRepository)
    {
        $this->tbaAnnexRepository = $tbaAnnexRepository;
        $this->resourceRepository = $resourceRepository;
        $this->fileRepository     = $fileRepository;
    }

    /**
     * @param $TbaId
     * @param $UserId
     * @param $file
     * @param null $AnnexType
     * @return mixed
     */
    public function saveFile($TbaId, $UserId, $file, $AnnexType = null)
    {
        $FileOriginalName = explode('.', $file->getClientOriginalName());
        $fileName         = $FileOriginalName[0];
        $ext              = $FileOriginalName[1];
        $tbaAnnex         = $this->tbaAnnexRepository->firstWhere(['type' => $AnnexType, 'tba_id' => $TbaId]);
        if (collect($tbaAnnex)->isEmpty()) {
            // create resource
            $resourceId = $this->resourceRepository->create([
                'user_id'  => $UserId,
                'src_type' => SrcType::File,
                'name'     => $fileName,
                'status'   => 1
            ])->id;
            // create tbaAnnex
            $this->tbaAnnexRepository->create([
                'tba_id'      => $TbaId,
                'resource_id' => $resourceId,
                'type'        => $AnnexType,
                'size'        => $file->getSize()
            ]);
            // create files
            $fileId = $this->fileRepository->create([
                'resource_id' => $resourceId,
                'name'        => $fileName,
                'ext'         => $ext,
            ])->id;

            // file storage $tbaAnnexIds
            return $file->move(storage_path("app/file/"), $fileId);
        }
        // update resourceRepository
        $this->resourceRepository->update($tbaAnnex->resource_id, [
            'name'    => $fileName,
            'user_id' => $UserId
        ]);
        // update fileRepository
        $this->fileRepository->updateBy('resource_id', $tbaAnnex->resource_id, [
            'name' => $fileName,
            'ext'  => $ext,
        ]);

        $fileId = $this->fileRepository->firstWhere(['resource_id' => $tbaAnnex->resource_id])->id;

        return $file->move(storage_path('app/file'), $fileId);
    }
}


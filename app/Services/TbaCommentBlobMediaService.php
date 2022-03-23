<?php

namespace App\Services;

use App\Libraries\Azure\Blob;

class TbaCommentBlobMediaService extends BaseService
{
    public $blobAllowedVideoExtentions = [];
    public $blobAllowedMiscExtentions = [];
    public $blobAllowedExtensions = [];

    protected $blobAccount = null;
    protected $blobKey = null;
    protected $blobEndpoint = null;
    protected $blobContainer = null;
    protected $blobMediaContainer = null;
    protected $blobService;

    // Construction
    public function __construct()
    {
        $this->blobAllowedVideoExtentions = ['mp3', 'mp4', 'wav', 'webm', 'mov', 'm4a'];
        $this->blobAllowedMiscExtentions  = ['jpg', 'jpeg', 'png'];
        $this->blobAllowedExtensions      = array_merge($this->blobAllowedVideoExtentions, $this->blobAllowedMiscExtentions);

        $this->blobAccount        = getenv('BLOB_ACCOUNT');
        $this->blobKey            = getenv('BLOB_KEY');
        $this->blobEndpoint       = getenv('ENDPOINT');
        $this->blobMediaContainer = getenv('BLOB_MEDIA_CONTAINER');
        $this->blobService        = new Blob($this->blobAccount, $this->blobKey, $this->blobEndpoint);
    }

    // Get allowed video extensions
    public function blobAllowedVideoExtentions(): array
    {
        return $this->blobAllowedVideoExtentions;
    }

    // Get allowed misc. extensions
    public function blobAllowedMiscExtentions(): array
    {
        return $this->blobAllowedMiscExtentions;
    }

    // Get allowed extensions
    public function getBlobAllowedExtensions(): array
    {
        return $this->blobAllowedExtensions;
    }

    /**
     * Upload media file to blob
     * @param string $blobDestDir
     * @param string $fileSrcDir
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function uploadMediaBlob(string $blobDestDir, string $fileSrcDir): ?\Illuminate\Http\JsonResponse
    {
        return !is_null($blobDestDir) && !is_null($fileSrcDir) ? $this->blobService->update($blobDestDir, $this->blobMediaContainer, $fileSrcDir) : null;
    }

    /**
     * Delete media directory
     * @param string $blobDestDir
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function deleteMediaBlobDir(string $blobDestDir): ?\Illuminate\Http\JsonResponse
    {
        return !is_null($blobDestDir) ? $this->blobService->deleteDir($blobDestDir, $this->blobMediaContainer) : null;
    }

    // Create Blob URL with SAS from file data
    public function getBlobSASLink(int $dirId, string $blobName)
    {
        if (is_null($dirId) || is_null($blobName)) return null;

        // Get full link with SAS
        $blobPath    = $dirId . "/" . $blobName; // dirId/123123123.mp4
        $requiredSAS = true;
        $blobLinkSAS = $this->getBlobLink($blobPath, $requiredSAS);

        return $blobLinkSAS;
    }

    // Create Blob URL without SAS from file data
    public function getPlainBlobLink(int $dirId, string $blobName)
    {
        if (is_null($dirId) || is_null($blobName)) return null;

        // Get full link without SAS
        $blobPath    = $dirId . "/" . $blobName; // dirId/123123123.mp4
        $blobLinkSAS = $this->getBlobLink($blobPath);

        return $blobLinkSAS;
    }

    // Get Blob Link
    public function getBlobLink(string $blobPath, bool $requiredSAS = false)
    {
        $blobLink = null;
        if (is_null($blobPath)) return $blobLink;
        try {
            $blobLinkResult = ($requiredSAS === true)
                ? $this->blobService->GetUrlByToken($blobPath, $this->blobMediaContainer, 24)
                : $this->blobService->GetUrl($blobPath, $this->blobMediaContainer);
            $blobLink       = $blobLinkResult["url"];
        } catch (\Exception $e) {
            return null;
        }
        return $blobLink;
    }
}

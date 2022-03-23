<?php

namespace App\Repositories;

use App\Models\BbLicense;

class BbLicenseRepository extends BaseRepository
{
    protected $model;

    /**
     * @param BbLicense $model
     */
    public function __construct(BbLicense $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $code
     * @param array $where
     * @param array $data
     * @return bool
     */
    public function createLicenseForGroup(array $code, array $where, array $data): bool
    {
        $isSuccessful = true;
        try {
            $bbLicenseGroups = $this->model->with('bbLicenseGroups')->firstWhere($code)->bbLicenseGroups() ?? null;
            if ($bbLicenseGroups->where($where)->exists()) {
                $bbLicenseGroups->update($data);
                return $isSuccessful;
            }
            $bbLicenseGroups->create(array_merge($where, $data));
        } catch (\Exception $exception) {
            \Log::info('Create license', [
                'status'  => 0,
                'message' => $exception->getMessage(),
            ]);
            $isSuccessful = false;
        }

        return $isSuccessful;
    }

    /**
     * @param string $code
     * @param array $where
     * @return bool
     */
    public function deleteLicenseForGroup(string $code, array $where): bool
    {
        $isSuccessful = true;

        $this->model->with('bbLicenseGroups')->firstWhere('code', $code)->bbLicenseGroups()->where($where)->delete();
        try {
            $this->model->with('bbLicenseGroups')->firstWhere('code', $code)->bbLicenseGroups()->where($where)->delete();
        } catch (\Exception $exception) {
            \Log::info('Delete license', [
                'status'  => 0,
                'message' => $exception->getMessage(),
            ]);
            $isSuccessful = false;
        }

        return $isSuccessful;

    }
}

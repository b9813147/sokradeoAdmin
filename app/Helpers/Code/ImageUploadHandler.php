<?php


namespace App\Helpers\Code;


trait ImageUploadHandler
{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    /**
     * @param $file
     * @param string $file_prefix
     * @param $folder_path
     * @param $id
     * @return bool
     */
    public function imageSave($file, $folder_path, $id, $file_prefix = 'thum')
    {
        // 取Image的後綴
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // 拼接文件名 不指定預設 thum
        $filename = $file_prefix . '.' . $extension;
        // 如果上传的不是图片将终止操作
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }
        $file->move(storage_path("app/public/" . $folder_path . '/' . $id), $filename);

        return $filename;
    }
}

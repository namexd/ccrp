<?php

namespace App\Traits;

use App\Models\Upload;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use OSS\Core\OssException;
use Storage;

trait ControllerUploader
{
    /**
     * @param $file \Illuminate\Http\UploadedFile
     * @param string $action
     * @param int $unit_id
     * @param string $app
     * @return array
     */
    public function upload($file, $action = 'images', $unit_id = 0, $app = 'ccrp')
    {
        $data = ['status' => false, 'message' => ''];
        if ($file->isValid()) {
            //获取文件的原文件名 包括扩展名
            $original_name = $file->getClientOriginalName();
            //获取文件的扩展名
            $extension = $file->getClientOriginalExtension();
            //获取文件的类型
            $type = $file->getClientMimeType();
            //获取文件的绝对路径，但是获取到的在本地不能打开
            $folder = config('filesystems.disks.oss.folder') . $app . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $unit_id;
            $path = $file->getRealPath();
            //要保存的文件名 时间+扩展名
            $uniqid = uniqid($app, true);
            $filename = $folder . DIRECTORY_SEPARATOR . date('Y-m-d') . DIRECTORY_SEPARATOR . $uniqid . '.' . $extension;
            //保存文件          配置文件存放文件的名字  ，文件名，路径
            try {

                $bool = Storage::disk('oss')->put($filename, file_get_contents($path));
                if ($bool) {
                    $url = Storage::disk('oss')->url($filename);
                    $size = Storage::disk('oss')->size($filename);
                    $upload = new Upload();
                    $upload->uniqid = $uniqid;
                    $upload->user_id = $this->user()->id;
                    $upload->app = $app;
                    $upload->action = $action;
                    $upload->unit_id = $unit_id;
                    $upload->filename = substr($original_name, 0, 20);
                    $upload->url = $url;
                    $upload->ext = $extension;
                    $upload->type = $type;
                    $upload->save();
                    $data = $upload->toArray();
                    $data['status'] = true;
                }
            } catch (OssException $exception) {
                $data['err'] = $exception->getMessage();
            }
        }
        return $data;
    }
}

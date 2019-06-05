<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Certification;
use App\Models\Ccrp\File;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{

    public function transform(File $file)
    {
        $arr = [
            'id' => $file->id,
            'file_name'=>$file->file_name,
            'file_server'=>$file->file_server,
            'file_url'=>$file->file_url,
            'file_type'=>$file->file_type,
            'file_category'=>$file->file_category,
            'file_desc'=>$file->file_desc,
            'company_id'=>$file->company_id,
            'company_name'=>$file->company_name,
            'create_time'=>$file->create_time,
            'out_date'=>$file->out_date,
            'file_url2'=>$file->file_url2,
            'status'=>$file->status,
            'note'=>$file->note,
        ];
        return $arr;
    }

 

}
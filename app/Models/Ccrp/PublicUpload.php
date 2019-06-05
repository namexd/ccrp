<?php

namespace App\Models\Ccrp;


use function App\Utils\format_value;

/**
 * Class Collector
 * @package App\Models
 */
class PublicUpload extends Coldchain2Model
{
    protected $table = 'public_upload';
    protected $fillable = ['name', 'path'];

    public function getPathAttribute($value)
    {
        return config('api.defaults.ccms') . $value;
    }
}

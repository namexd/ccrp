<?php


namespace App\Http\Requests\Api\Ccrp;


class LedspeakerRequest extends Request
{
    public function rules()
    {
        return [
            'ledspeaker_name' => 'required',
            'supplier_ledspeaker_id' => 'required',
            'supplier_model' => 'required',
            'module' => 'required',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
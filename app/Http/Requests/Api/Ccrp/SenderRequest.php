<?php


namespace App\Http\Requests\Api\Ccrp;


class SenderRequest extends Request
{
    public function rules()
    {
        return [
            'sender_id' => 'required',
            'note' => 'required',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
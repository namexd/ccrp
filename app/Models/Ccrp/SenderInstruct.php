<?php

namespace App\Models\Ccrp;


use GuzzleHttp\Client;

class SenderInstruct extends Coldchain2Model
{
    protected $table = 'sender_instructs';
    const API_SERVER = 'http://cmd.coldwang.com/cmdlw.php';
    const API_PARAMS = [
        'action' => 'bind',
        'bindcode' => '',
        'bindss' => '',
        'senderid' => '',
    ];
    //http://cmd.coldwang.com/cmdlw.php?action=bind&bindcode=2&bindss=17111214775@17111214280&senderid=88888806

    const BINDCODES = [
        0 => '0:关闭绑定',
//        1 =>'仅平台绑定的显示',
        2 => '2:开启接收+显示+上传绑定',
        3 => '3:开启显示绑定，但接收、上传不绑定',
    ];


    public function instruct()
    {
        $client = new Client();
        $params = self::API_PARAMS;
        $params['bindcode'] = $this->bindcode;
        $params['bindss'] = $this->bindss;
        $params['senderid'] = $this->senderid;

        $options = [
            'query' => $params,
        ];

        try {
            $res = $client->request('GET', self::API_SERVER, $options);
            return $res->getStatusCode();
        } catch (\Exception $exception) {
            return 500;
        }
    }

    public function sender()
    {
        return $this->belongsTo(Sender::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
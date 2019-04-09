<?php
return [
    'defaults' => [
        'image' => [
            'host' => env('ALIYUN_OSS_URL'),
            'logo' => [
                'default' => env('DEFAULT_IMAGE'),
                'cdc' => env('DEFAULT_IMAGE'),
            ],
            'signature' => [
                'signature_id_url' => env('SIGNATURE_ID_URL'),
                'signature_image_name_url' => env('SIGNATURE_IMAGE_NAME_URL'),
            ],
        ],
        'sms' => [
            'aliyun' => [
                'template' => [
                    'vcode' => env('ALIYUN_SMS_CODE_VCODE'),
                ],
            ],
        ],
        'bpms_api_server' => env('BPMS_API_SERVER'),
        'ccms' => env('CCMS_URL'),
    ],
];

<?php

return [
    'city' => [
        'Махачкала',
        'Каспийск',
        'Избербаш',
        'Хасавюрт',
        'Бабаюрт'
    ],
    'stock' => '6b7f2bb6-7d5e-11ea-80cb-ac1f6bd1d36d',
    'orderStartNumber' => 0, // стартовый номер заказа для 1c
    'productIds' => [
        '15bb7407-9dad-11e9-968f-005056011715',
        '1e969483-9db1-11e9-968f-005056011715',
        '37e4b613-9dac-11e9-968f-005056011715',
        '37e4b61d-9dac-11e9-968f-005056011715',
        '547d8dbd-9db1-11e9-968f-005056011715',
        '56ab0d1b-9dae-11e9-968f-005056011715',
        '60763af1-9db1-11e9-968f-005056011715',
        '66712576-9db1-11e9-968f-005056011715',
        'b5aa4197-9dae-11e9-968f-005056011715',
        'd3958176-9dae-11e9-968f-005056011715',
        'e2ba9fef-9db0-11e9-968f-005056011715',
        'ffb5b845-9daf-11e9-968f-005056011715'
    ],
    '1c' => [
        'user' => 'webservice',
        'password' => 'H5v-8Yt2S57',
        'urls' => [
            '1c.pharm36.ru/pharm36/hs/reserv/goods',
            '1c.pharm36.ru/pharm36/hs/reserv/goods/getChanges',
            '1c.pharm36.ru/pharm36/hs/reserv/pharmacies',
            '1c.pharm36.ru/pharm36/hs/reserv/pharmacyOffers',
            '1c.pharm36.ru/pharm36/hs/reserv/pharmacyOffers/getChanges',
            '1c.pharm36.ru/pharm36/hs/reserv/orders',
            '1c.pharm36.ru/pharm36/hs/reserv/offers'
        ]
    ],
    'pay' => [
        'sber' => [
            'prod' => [
                'prefix_number' => 'order_',
                'url' => 'https://securepayments.sberbank.ru/payment/rest/register.do',
                'statusUrl' => 'https://securepayments.sberbank.ru/payment/rest/getOrderStatus.do',
                'refundUrl' => 'https://securepayments.sberbank.ru/payment/rest/refund.do',
                'username' => '366express-api',
                'password' => '9RML$ZMjN@',
            ],
            'test' => [
                'prefix_number' => 'test-order_',
                'url' => 'https://3dsec.sberbank.ru/payment/rest/register.do',
                'statusUrl' => 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do',
                'refundUrl' => 'https://3dsec.sberbank.ru/payment/rest/refund.do',
                'username' => '366express-api',
                'password' => '366express',
            ],
        ],
    ],
    'yandex' => [
        'GeoCoder' => [
            'apikey' => 'de8de84b-e8b4-46c9-ba10-4cf2911deebf',
        ],
        'delivery' => [
            'contact_name' => 'Аптека',
            'contact_email' => 'info@120на80.рф',
            'contact_phone' => '78722606366',
            'auth_token' => 'AgAAAABGZK83AAVM1bN7yo78ok-7q6tFrMOVk-0',//test -> AgAAAABEqOAQAAVM1daezZh6F05Lu2mamlHRF6I
            'idempotency_prefix' => 'test',
            'links' => [
                'create' => 'https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/claims/create',
            ],
        ],
    ]
];

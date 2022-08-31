<?php

return [
    'stock' => '6b7f2bb6-7d5e-11ea-80cb-ac1f6bd1d36d',
    'orderStartNumber' => 0, // стартовый номер заказа для 1c
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
                'url' => 'https://securepayments.sberbank.ru/payment/rest/register.do',
                'statusUrl' => 'https://securepayments.sberbank.ru/payment/rest/getOrderStatus.do',
                'refundUrl' => 'https://securepayments.sberbank.ru/payment/rest/refund.do',
                'username' => 'p571008484-api',
                'password' => 'dp77zR1F%cBl',
            ],
            'test' => [
                'url' => 'https://3dsec.sberbank.ru/payment/rest/register.do',
                'statusUrl' => 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do',
                'refundUrl' => 'https://3dsec.sberbank.ru/payment/rest/refund.do',
                'username' => 't571008484-api',
                'password' => 'VqXHTSQ3',
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
    ],
    'dadata_api' => [
        'token' => 'cf0a769944c29a5b279769a97303e8508558679b',
        'secret' => '875598b9e417663986bd5b8b5aab7155ed646df5'
    ],
    'loyalty' => [
        'test' => [
            'url' => [
                'pos' => 'http://seredinacrm4.ru:8083/posprocessing.asmx',
                'lk' => 'http://seredinacrm4.ru:1011/CustomerOfficeService',
                'manager' => 'http://seredinacrm4.ru:1012/ManagerOfficeService',
                'admin' => 'http://seredinacrm4.ru:1013/AdministratorOfficeService'
            ],
            'organization' => '00001', // идентификатор организации
            'business_unit' => 1, // идентификатор магазина
            'pos' => 1, // идентификатор кассы
            'org_name' => 'ZS2',
            'login' => 'crm\Integr',
            'password' => 'E9JxGqe2Z',
            'id_task_card' => 'integr2',
            'partner_id' => 'BE4205A0-1EC0-E611-80B5-001DD8B75065',
            'session_id' => 'A35B9DCA-1947-407B-8079-86AF61E2A2C5', // сессия администратора
            'manager' => [
                'login' => 'integer',
                'password' => '9LuwK8NvVjx6jw',
            ]
        ]
    ]
];

<?php

return [
    'stock' => '6b7f2bb6-7d5e-11ea-80cb-ac1f6bd1d36d',
    'orderStartNumber' => 0, // стартовый номер заказа для 1c
    'infoEmail' => 'info@120на80.рф',
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
        'url' => [
            'pos' => 'http://37.18.77.42:8083/posprocessing.asmx',
            'lk' => 'http://37.18.77.42:1011/CustomerOfficeService',
            'manager' => 'http://37.18.77.42:1012/ManagerOfficeService',
            'admin' => 'http://37.18.77.42:1013/AdministratorOfficeService'
        ],
        'test' => [
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
        ],
        'prod' => [
            'organization' => '10074', // идентификатор организации
            'business_unit' => 'test', // идентификатор магазина
            'pos' => 'test', // идентификатор кассы
            'org_name' => 'ZS2',
            'login' => 'crm\dagfarm',
            'password' => 'EgW8mBQG9',
            'id_task_card' => 'dag1',
            'partner_id' => 'E078F788-5707-ED11-80CB-001DD8B75065',
            'session_id' => 'A35B9DCA-1947-407B-8079-86AF61E2A2C5', // сессия администратора
            'manager' => [
                'login' => 'dagfarm',
                'password' => 'YtvH9zDSVJ06PLbnm7',
            ]
        ]
    ],
    'mobileStores' => [
        1 => [ // Махачкала
            'f36356e9-eaa1-11e9-969d-005056011715', // пр. Петра I ,135
            'a9ccff41-f0be-11e9-969d-005056011715', // ул. М.Ярагского, 71
            'dba0cfa1-ee6e-11e9-969d-005056011715', // ул. Каммаева, 89 А
            'af98853a-f0da-11e9-969d-005056011715', // п. Семендер, пр. Казбекова, 32
            '70d598ba-fc8c-11e9-96c6-005056011715', // ул. Абдулы Алиева, 4А
            '6179a810-3e07-11eb-80ec-ac1f6bd1d36d', // пр. А.Акушинского, 1А
            'fcd58bdb-f170-11e9-969d-005056011715', // пр. Гамидова, 48
        ],
        2 => [ // Каспийск
            'f4ecaaea-b427-11ec-80f3-ac1f6bd1d36d', // ул. Ленина,54
        ],
        3 => [ // Избербаш
            '389f7b66-f019-11e9-969d-005056011715', // ул. Маяковского, 114а
        ],
        4 => [ // Хасавюрт
            '1fcdea46-f171-11e9-969d-005056011715', // ул, Даибова, 8
        ],
        5 => [ // Бабаюрт
            '45e32433-f171-11e9-969d-005056011715', // ул. Дж.Алиева, 30
        ]
    ],
];

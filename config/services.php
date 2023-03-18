<?php

return [
    'uniteller' => [
        'is_enable' => env('UNITELLER_IS_ENABLE', false),
        'webhook_domain' => env('UNITELLER_WEBHOOK_DOMAIN', null),
        'webhook_slug' => env('UNITELLER_WEBHOOK_SLUG', null),
        'login' => env('UNITELLER_LOGIN', null),
        'password' => env('UNITELLER_PASSWORD', null),
        'shop_id' => env('UNITELLER_SHOP_ID', null),
        'default_order_life_time' => "012:30",
        'default_vat' => -1,
        'default_pay_attr' => 4,
        'default_line_attr' => 4,
        'default_tax_mode' => 2,
        'inn_merchant' => null,
        'url_register' => "https://fpay.uniteller.ru/v2/api/register",
    ],
];

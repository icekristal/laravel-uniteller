install:
```php
composer require icekristal/laravel-uniteller
```
migration:
```php
php artisan vendor:publish --provider="Icekristal\LaravelUnitellerApi\UnitellerApiServiceProvider" --tag="migrations"
```

config:
```php
php artisan vendor:publish --provider="Icekristal\LaravelUnitellerApi\UnitellerApiServiceProvider" --tag="config"
```

use:
```php

class Order extends Model
{
    use \Icekristal\LaravelUnitellerApi\InteractsUnitellerService;
}
```

copy config settings in services:
```php
    'uniteller' => [
        'is_enable' => env('UNITELLER_IS_ENABLE', false),
        'is_save_database' => env('UNITELLER_IS_SAVE_DATABASE', false),
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
        'is_enable_logs_webhook' => false,
        'db_connection' => env('DB_CONNECTION', 'mysql'),
        'name_channel_logs_webhook' => 'default',
        'url_register' => "https://fpay.uniteller.ru/v2/api/register",
        'model_object' => null //Model Order::class
    ],
```

use service:
```php
Uniteller::setCustomerInfo($customerInfo); // set customer info
Uniteller::setCashierInfo($cashierInfo); // set cashier info
Uniteller::setProductsInfo($productsInfo); // set products info
Uniteller::setTotalSumma($totalSumma); // set total summa
Uniteller::setObjectPayment($object); // self object your system
Uniteller::getPayUrl(); // pay url
Uniteller::getFinishResult(); // get finish result pay
```

<?php

namespace Icekristal\LaravelUnitellerApi\Facades;

use Icekristal\LaravelUnitellerApi\Services\UnitellerServiceApi;
use Illuminate\Support\Facades\Facade;

/**
 * @method static UnitellerServiceApi setCustomerInfo(array $customerInfo)
 * @method static UnitellerServiceApi getCashierInfo()
 * @method static UnitellerServiceApi setCashierInfo(array $cashierInfo)
 * @method static UnitellerServiceApi getProductsInfo()
 * @method static UnitellerServiceApi setProductsInfo(array $productsInfo)
 * @method static UnitellerServiceApi getTotalSumma()
 * @method static UnitellerServiceApi setTotalSumma(float|int $totalSumma)
 * @method static UnitellerServiceApi getObjectPayment()
 * @method static UnitellerServiceApi setObjectPayment($objectPayment)
 * @method static UnitellerServiceApi getOrderId()
 * @method static UnitellerServiceApi setOrderId(mixed $orderId)
 * @method static UnitellerServiceApi getPayUrl()
 * @method static UnitellerServiceApi sendRequest()
 * @method static UnitellerServiceApi updateWebhook($data)
 * @method static UnitellerServiceApi getFinishResult()
 */
class Uniteller extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ice.uniteller.api';
    }
}

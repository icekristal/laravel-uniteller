<?php

namespace Icekristal\LaravelUnitellerApi\Services;

use Illuminate\Support\Facades\Http;

class UnitellerServiceApi
{

    private mixed $login;
    private mixed $password;
    private mixed $shopId = null;
    private mixed $merchantInn = null;
    public mixed $orderLifeTime = null;
    public mixed $taxMode = null;
    public mixed $urlRegister = "https://fpay.uniteller.ru/v2/api/register";

    public mixed $vat = -1;
    public mixed $payAttr = 4;
    public mixed $lineAttr = 4;

    public array $customerInfo = [];
    public array $cashierInfo = [];
    public array $productsInfo = [];
    public array $paymentsInfo = [];
    public $dateTimePayment = null;

    public $objectPayment = null; //Order or Client...
    public $urlReturn = null; //Order or Client...

    public int|float $totalSumma = 0;

    public function __construct()
    {
        $this->login = config('services.uniteller.login');
        $this->password = config('services.uniteller.password');
        $this->shopId = config('services.uniteller.shop_id');
        $this->orderLifeTime = config('services.uniteller.default_order_life_time');
        $this->taxMode = config('services.uniteller.default_tax_mode');
        $this->vat = config('services.uniteller.default_tax_mode');
        $this->payAttr = config('services.uniteller.default_pay_attr');
        $this->lineAttr = config('services.uniteller.default_line_attr');
        $this->urlRegister = config('services.uniteller.url_register');
        $this->merchantInn = config('services.uniteller.inn_merchant');
        $this->dateTimePayment = now()->format("Y-m-d H:i:s");
        $this->urlReturn = config('services.uniteller.webhook_domain') ?? config('app.url') . "/" . config('services.uniteller.webhook_slug');
    }

    /**
     * @return array
     */
    public function getCustomerInfo(): array
    {
        return $this->customerInfo;
    }

    /**
     * @param array $customerInfo
     *
     * example: ['phone' => '+79998887700', 'name' => 'Name', 'email' => 'test@test.com']
     *
     * @return UnitellerServiceApi
     */
    public function setCustomerInfo(array $customerInfo): UnitellerServiceApi
    {
        $this->customerInfo = $customerInfo;
        return $this;
    }

    /**
     * @return array
     */
    public function getCashierInfo(): array
    {
        return $this->cashierInfo;
    }

    /**
     * @param array $cashierInfo
     * @return UnitellerServiceApi
     */
    public function setCashierInfo(array $cashierInfo): UnitellerServiceApi
    {
        $cashierInfo['inn'] = $this->merchantInn ?? $cashierInfo['inn'] ?? null;
        $this->cashierInfo = $cashierInfo;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductsInfo(): array
    {
        return $this->productsInfo;
    }

    /**
     * @param array $productsInfo
     * example:
     * $array = [
     *  [
     *      'name' => 'Name Product',
     *      'price' => 500, //Price one product
     *      'qty' => 1,
     *      'taxmode' => 1,
     *      'sum' => 500, //Total price price * qty
     *      'vat' => 1,
     *      'payattr' => 4,
     *      'lineattr' => 4,
     *  ],
     *  [],
     *  ...
     *  [],
     *  [],
     * ]
     * @return UnitellerServiceApi
     */
    public function setProductsInfo(array $productsInfo): UnitellerServiceApi
    {
        $returnProductInfo = [];
        foreach ($productsInfo as $item) {
            $itemUpdate = $item;

            $itemUpdate['name'] = mb_substr($item['name'] ?? '-', 0, 126);
            $itemUpdate['price'] = $item['price'] ?? 0;
            $itemUpdate['qty'] = $item['qty'] ?? 1;
            $itemUpdate['taxmode'] = $item['taxmode'] ?? $this->taxMode;
            $itemUpdate['sum'] = $item['sum'] ?? $itemUpdate['price'] * $itemUpdate['qty'];
            $itemUpdate['vat'] = $item['vat'] ?? $this->vat;
            $itemUpdate['payattr'] = $item['payattr'] ?? $this->payAttr;
            $itemUpdate['lineattr'] = $item['lineattr'] ?? $this->lineAttr;

            $returnProductInfo[] = $itemUpdate;
        }
        $this->productsInfo = $returnProductInfo;
        return $this;
    }

    /**
     * @return float|int
     */
    public function getTotalSumma(): float|int
    {
        return $this->totalSumma;
    }

    /**
     * @param float|int $totalSumma
     * @return UnitellerServiceApi
     */
    public function setTotalSumma(float|int $totalSumma): UnitellerServiceApi
    {
        $this->totalSumma = $totalSumma;
        return $this;
    }

    /**
     * @return null
     */
    public function getObjectPayment()
    {
        return $this->objectPayment;
    }

    /**
     * @param null $objectPayment
     */
    public function setObjectPayment($objectPayment): UnitellerServiceApi
    {
        $this->objectPayment = $objectPayment;
        return $this;
    }


    /**
     * Get url payment
     * @return string
     */
    public function getPayUrl(): string
    {
        if (is_null($this->objectPayment)) abort(422, 'no set object payment');
        $sendInfo['UPID'] = $this->shopId;
        $sendInfo['ObjectType'] = get_class($this->objectPayment);
        $sendInfo['ObjectId'] = $this->objectPayment->id;
        $sendInfo['OrderLifeTime'] = $this->orderLifeTime;
        $sendInfo['CurrentDate'] = $this->dateTimePayment;
        $sendInfo['Subtotal_P'] = $this->getTotalSumma();
        $sendInfo['Receipt'] = [];
        $sendInfo['Signature'] = [];
        $sendInfo['URL_RETURN'] = $this->urlReturn;
        $sendInfo['URL_RETURN_OK'] = $this->urlReturn . "?success=1";
        $sendInfo['URL_RETURN_NO'] = $this->urlReturn . "?success=0";

        $resultAnswer = Http::post($this->urlRegister, $sendInfo)->json();
        if ($resultAnswer) {
            $infoAnswer = $resultAnswer->getBody();
        }
    }
}

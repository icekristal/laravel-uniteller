<?php

namespace Icekristal\LaravelUnitellerApi\Services;

use Icekristal\LaravelUnitellerApi\Models\ServiceUniteller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnitellerServiceApi
{

    private mixed $login;
    private mixed $password;
    private mixed $shopId = null;
    private mixed $merchantInn = null;
    public mixed $orderLifeTime = null;
    public mixed $taxMode = null;
    public mixed $urlRegister = "https://fpay.uniteller.ru/v2/api/register";

    public ?ServiceUniteller $unitellerModel = null;
    public mixed $vat = -1;
    public mixed $payAttr = 4;
    public mixed $lineAttr = 4;
    public mixed $orderId = null;

    public bool $isSaveDataBase = false;

    public array $customerInfo = [];
    public array $cashierInfo = [];
    public array $productsInfo = [];
    public array $paymentsInfo = [];
    public $dateTimePayment = null;

    public $objectPayment = null; //Order or Client...
    public $urlReturn = null; //Order or Client...

    public $answerUniteller = null;

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
        $this->isSaveDataBase = config('services.uniteller.is_save_database') ?? false;
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
     * example: ['phone' => '+79998887700', 'name' => 'Name', 'email' => 'test@test.com', 'id' => 11]
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
        $totalSumma = 0;
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
            $totalSumma += $itemUpdate['sum'];
            $returnProductInfo[] = $itemUpdate;
        }
        $this->productsInfo = $returnProductInfo;

        if ($this->totalSumma <= 0) {
            $this->setTotalSumma($totalSumma);
        }

        return $this;
    }

    /**
     * @return float|int
     */
    public function getTotalSumma(): float|int
    {
        return number_format($this->totalSumma, 2, '.', '');
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
        $this->setOrderId($objectPayment?->id ?? null);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId(): mixed
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     * @return UnitellerServiceApi
     */
    public function setOrderId(mixed $orderId): UnitellerServiceApi
    {
        $this->orderId = $orderId;
        $this->unitellerModel = ServiceUniteller::query()->where('id', $orderId)->first();
        return $this;
    }


    private function generateReceipt(): string
    {
        $result['customer'] = $this->getCustomerInfo();
        $result['cashier'] = $this->getCashierInfo();
        $result['taxmode'] = $this->taxMode;
        $result['lines'] = $this->getProductsInfo();
        $result['payments'] = [
            'kind' => 1, // 1 - bank card
            'type' => 0, // type payment
            'amount' => $this->getTotalSumma(),
        ];
        $result['total'] = $this->getTotalSumma();

        return base64_encode(json_encode($result));
    }


    private function generateSignature(): string
    {
        return strtoupper(
            hash("sha256",
                hash("sha256", $this->getOrderId()) .
                "&" . hash("sha256", $this->shopId) .
                "&" . hash("sha256", $this->orderLifeTime) .
                "&" . hash("sha256", $this->dateTimePayment) .
                "&" . hash("sha256", $this->generateReceipt()) .
                "&" . hash("sha256", $this->password))

        );
    }


    /**
     * Get url payment
     * @return string|null
     */
    public function getPayUrl(): ?string
    {
        if (is_null($this->objectPayment)) abort(422, 'no set object payment');
        return !is_null($this->answerUniteller) ? $this->answerUniteller['Link'] ?? null : null;
    }

    public function sendRequest()
    {
        $serviceUniteller = null;

        $sendInfo['UPID'] = $this->shopId;
        $sendInfo['OrderID'] = $this->getOrderId() ?? $this->objectPayment->id ?? null;
        $sendInfo['OrderLifeTime'] = $this->orderLifeTime;
        $sendInfo['CurrentDate'] = $this->dateTimePayment;
        $sendInfo['Subtotal_P'] = $this->getTotalSumma();
        $sendInfo['Receipt'] = [];
        $sendInfo['Signature'] = [];
        $sendInfo['URL_RETURN'] = $this->urlReturn;
        $sendInfo['URL_RETURN_OK'] = $this->urlReturn . "?success=1";
        $sendInfo['URL_RETURN_NO'] = $this->urlReturn . "?success=0";

        if ($this->isSaveDataBase) {
            $serviceUniteller = ServiceUniteller::query()->updateOrCreate([
                'order_id' => $sendInfo['OrderID']
            ], [
                'object_type' => get_class($this->objectPayment) ?? null,
                'object_id' => $this->objectPayment->id ?? null,
                'send_info' => $sendInfo,
                'answer_info' => null,
            ]);
        }

        $resultAnswer = Http::post($this->urlRegister, $sendInfo)->json();
        if ($resultAnswer) {
            if ($this->isSaveDataBase && !is_null($serviceUniteller)) {
                $serviceUniteller->update([
                    'answer_info' => $resultAnswer
                ]);
            }
            $this->answerUniteller = $resultAnswer->getBody();
            return $this->answerUniteller;
        }
        return false;
    }

    private function generateWebHookSignature($statusText): string
    {
        return mb_strtoupper(md5($this->unitellerModel->order_id . $statusText . $this->password));
    }

    public string|null $requestStatus = null;
    public bool $isAccessSignature = false;

    public function updateWebhook($data): UnitellerServiceApi
    {
        $this->requestStatus = $data['Status'] ?? null;
        $requestSignature = $data['Signature'] ?? null;
        $mySignature = $this->generateWebHookSignature($this->requestStatus);
        $this->isAccessSignature = $requestSignature == $mySignature;
        $orderId = $data['Order_Id'] ?? 0;
        $this->writeLog("Check signature order_id: {$orderId} || request: {$requestSignature} == my: {$mySignature}", $this->isAccessSignature);

        if (!is_null($this->unitellerModel) && $this->isSaveDataBase) {
            $this->unitellerModel->update([
                'status' => $this->requestStatus,
                'webhook_info' => $data,
                'is_success_completed' => $this->isAccessSignature,
                'is_finish' => true,
            ]);
        }
        return $this;
    }

    public function getFinishResult(): ?array
    {
        if (!is_null($this->unitellerModel) && $this->isSaveDataBase && $this->unitellerModel->is_finish) {
            return [
                'status' => $this->unitellerModel->status,
                'is_success_completed' => $this->unitellerModel->is_success_completed,
            ];
        }

        return [
            'status' => $this->requestStatus,
            'is_success_completed' => $this->isAccessSignature,
        ];
    }


    private function writeLog($text, bool $isInfo = true): void
    {
        if (config('services.uniteller.is_enable_logs_webhook')) {
            $log = Log::channel(config('services.uniteller.name_channel_logs_webhook', 'default'));
            if ($isInfo) {
                $log->info($text);
            } else {
                $log->warning($text);
            }
        }
    }
}

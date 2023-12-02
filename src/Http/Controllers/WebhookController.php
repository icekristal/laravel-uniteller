<?php

namespace Icekristal\LaravelUnitellerApi\Http\Controllers;

use Icekristal\LaravelTelegram\Jobs\IceTelegramJob;
use Icekristal\LaravelUnitellerApi\Facades\Uniteller;
use Icekristal\LaravelUnitellerApi\Models\ServiceUniteller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends BaseController
{
    public function index(Request $request): Response
    {
        if (config('services.uniteller.is_enable_logs_webhook')) {
            Log::channel(config('services.uniteller.name_channel_logs_webhook', 'default'))->info($request->all());
        }

        $uniteller = Uniteller::setOrderId($request->get('Order_ID'))->updateWebhook($request->all())->getFinishResult();
        return new Response($uniteller['is_success_completed'] ?? false, 200);
    }
}

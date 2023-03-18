<?php

namespace Icekristal\LaravelUnitellerApi\Http\Controllers;

use Icekristal\LaravelTelegram\Jobs\IceTelegramJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class WebhookController extends BaseController
{
    public function index(Request $request): Response
    {
        return new Response([true], 200);
    }
}

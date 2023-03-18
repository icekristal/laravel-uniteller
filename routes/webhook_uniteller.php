<?php

use Icekristal\LaravelUnitellerApi\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;


if (config('services.uniteller.is_enable')) {
    Route::post(config('services.uniteller.webhook_slug'), [WebhookController::class, 'index'])->name("wh_uniteller.post")->domain(config('services.uniteller.webhook_domain'));
}


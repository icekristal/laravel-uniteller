<?php

namespace Icekristal\LaravelUnitellerApi;

use Icekristal\LaravelUnitellerApi\Services\UnitellerServiceApi;
use Illuminate\Support\ServiceProvider;

class UnitellerApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ice.uniteller.api', UnitellerServiceApi::class);
        $this->registerRoutes();
    }


    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhook_uniteller.php');
    }
}

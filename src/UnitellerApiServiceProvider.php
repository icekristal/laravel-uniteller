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

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishMigrations();
        }
    }


    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhook_uniteller.php');
    }

    protected function publishMigrations(): void
    {
        if (!class_exists('CreateServiceUnitellerTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_service_uniteller_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_service_uniteller_table.php'),
            ], 'migrations');
        }
    }
}

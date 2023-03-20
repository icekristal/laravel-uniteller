<?php

namespace Icekristal\LaravelUnitellerApi;

use Icekristal\LaravelUnitellerApi\Models\ServiceUniteller;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait InteractsUnitellerService
{

    /**
     *  last row uniteller order
     * @return MorphOne
     */
    public function unitellerOrder(): MorphOne
    {
        return $this->setConnection(config('services.uniteller.db_connection') ?? env('DB_CONNECTION'))->morphOne(ServiceUniteller::class, 'object')->latest();
    }

    /**
     *  all order uniteller orders
     * @return MorphMany
     */
    public function unitellerOrders(): MorphMany
    {

        return $this->setConnection(config('services.uniteller.db_connection') ?? env('DB_CONNECTION'))->morphMany(ServiceUniteller::class, 'object');
    }
}

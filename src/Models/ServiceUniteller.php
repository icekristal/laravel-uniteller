<?php

namespace Icekristal\LaravelUnitellerApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property integer $id
 * @property string $order_id
 * @property string $object_type
 * @property integer $object_id
 * @property string $created_at
 * @property string $updated_at
 * @property object $send_info
 * @property object $answer_info
 */
class ServiceUniteller extends Model
{
    /**
     *
     * Name Table
     * @var string
     */
    protected $table = 'service_uniteller';


    protected $fillable = [
        'object_type', 'object_id', 'send_info', 'answer_info', 'order_id'
    ];

    /**
     *
     * Mutation
     *
     * @var array
     */
    protected $casts = [
        'send_info' => 'object',
        'answer_info' => 'object',
    ];

    /**
     * Owner transaction
     *
     * @return MorphTo
     */
    public function object(): MorphTo
    {
        return $this->morphTo();
    }
}

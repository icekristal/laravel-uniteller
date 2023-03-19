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
 * @property object $webhook_info
 * @property boolean $is_finish
 * @property boolean $is_success_completed
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
        'object_type', 'object_id', 'send_info', 'answer_info', 'webhook_info', 'order_id', 'is_finish', 'is_success_completed'
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
        'webhook_info' => 'object',
        'is_finish' => 'boolean',
        'is_success_completed' => 'boolean',
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

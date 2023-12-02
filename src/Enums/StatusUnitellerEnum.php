<?php

namespace Icekristal\LaravelUnitellerApi\Enums;

enum StatusUnitellerEnum: string
{
    case WAITING = 'waiting';
    case PAID = 'paid';
    case AUTHORIZED = 'authorized';
    case NOT_AUTORIZED = 'not autorized';
    case CANCELED = 'canceled';
}

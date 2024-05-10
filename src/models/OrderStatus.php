<?php

declare(strict_types=1);

namespace Steamy\Model;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
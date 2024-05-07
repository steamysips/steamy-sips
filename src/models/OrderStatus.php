<?php

declare(strict_types=1);

namespace Steamy\Model;

abstract class OrderStatus {
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const COMPLETED = 'completed';
}
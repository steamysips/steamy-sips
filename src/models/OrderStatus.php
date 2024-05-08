<?php

declare(strict_types=1);

namespace Steamy\Model;

enum OrderStatus {
    case PENDING;
    case CANCELLED;
    case COMPLETED;
}
<?php

declare(strict_types=1);

namespace Steamy\Model;

enum OrderCupSize: string
{
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';
}
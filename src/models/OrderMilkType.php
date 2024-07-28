<?php

declare(strict_types=1);

namespace Steamy\Model;

enum OrderMilkType: string
{
    case ALMOND = 'almond';
    case COCONUT = 'coconut';
    case OAT = 'oat';
    case SOY = 'soy';

}
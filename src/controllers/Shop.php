<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Shop
{
    use Controller;

    public function index(): void
    {
        // must handle route shop/products/32432
        $this->view(
            'Shop',
            [],
            'Shop'
        );
    }
}

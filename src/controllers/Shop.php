<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Shop
{
    use Controller;

    public function index(): void
    {
        $css_file_path = ROOT . "/styles/views/Shop.css";

        // must handle route shop/product/32432
        $this->view(
            'Shop',
            [],
            'Shop',
            "<link rel=\"stylesheet\" href=\"$css_file_path\"/>"
        );
    }
}

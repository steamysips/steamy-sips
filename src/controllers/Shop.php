<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;

/**
 * Displays all products when URL is /shop or /shop/products
 */
class Shop
{
    use Controller;

    public function index(): void
    {
        $URL = Utility::splitURL();

        // check if URL follows format /shop/products/<number>
        if ($URL[1] == 'products' && sizeof($URL) == 3) {
            // call Product controller
            (new Product())->index();
            return;
        }

        // check if URL does not follow required format /shop or /shop/products
        if (!(sizeof($URL) == 2 && $URL[1] == 'products' || sizeof($URL) == 1)) {
            // display error page
            $this->view(
                '404',
                template_title: 'Error'
            );
            return;
        }

        // fetch all products from database
        $data['products'] = array_fill(
            0,
            10,
            (object)[
                'name' => 'Espresso',
                'description' => 'Personalize your coffee blend, selecting from diverse beans, roasts, and flavors
                 for a truly unique brew tailored to your preferences.',
                'rating' => 3.1
            ]
        );

        $this->view(
            'Shop',
            $data,
            'Shop'
        );
    }
}

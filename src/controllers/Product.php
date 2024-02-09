<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;

/**
 * Displays product page when URL follows format `/shop/products/<number>`.
 */
class Product
{
    use Controller;

    public function index(): void
    {
        // get product id from URL
        $product_id = Utility::splitURL()[2];

        // fetch product data from database
        $data["product"] = (object)[
            'name' => 'Espresso',
            'description' => 'Personalize your coffee blend, selecting from diverse beans, roasts, and flavors
                 for a truly unique brew tailored to your preferences.',
            'rating' => 3.1
        ];

        if ($data["product"] == null) {
            // if product was not found, display error page
            $this->view(
                '404',
                template_title: 'Not found'
            );
            return;
        }

        // get average rating of product (calculated in sql)
        $data["product"]->avg_rating = 3.1;

        // get all reviews for product
        $data["product"]->reviews = array_fill(
            0,
            10,
            (object)[
                'date' => 'April 9, 2023',
                'author' => 'User123',
                'text' => 'This is a comment.',
                'children' => [
                    (object)[
                        'date' => 'April 10, 2023',
                        'author' => 'Administrator',
                        'text' => 'This is a comment.',
                        'children' => [
                            (object)[
                                'date' => 'April 9, 2023',
                                'author' => 'User123',
                                'text' => 'Yes.',
                                'children' => [
                                    (object)[
                                        'date' => 'April 9, 2023',
                                        'author' => 'User123',
                                        'text' => 'This is a comment.',
                                        'children' => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->view(
            'Product',
            $data,
            'Product name'
        );
    }
}
<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;

/**
 * Displays product page when URL follows format `/shop/products/<number>`.
 */
class Product
{
    use Controller;

    private \Steamy\Model\Product $product;
    private bool $invalidProductID = false;

    public function __construct()
    {
        // get product id from URL
        $product_id = filter_var(Utility::splitURL()[2], FILTER_VALIDATE_INT);

        if (!$product_id) {
            $this->invalidProductID = true;
            return;
        }

        // fetch product from database
        $fetched_product = \Steamy\Model\Product::getByID($product_id);

        if (!$fetched_product) {
            $this->invalidProductID = true;
        } else {
            $this->product = $fetched_product;
        }
    }

    public function index(): void
    {
        if ($this->invalidProductID) {
            // if product was not found, display error page
            $this->view(
                '404',
                template_title: 'Product not found'
            );
            return;
        }
        $data["product"] = $this->product;

        // get all reviews for product
        $data["reviews"] = array_fill(
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
            $data["product"]->getName() . ' | Steamy Sips'
        );
    }
}
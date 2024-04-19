<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Model\Product;

/**
 * Handles /products route of api
 */
class Products
{
    private function getProducts(): void
    {
        $all_products = Product::getAll();
        $result = [];
        foreach ($all_products as $product) {
            $result[] = $product->toArray();
        }
        echo json_encode($result);
    }

    public function index(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->getProducts();
                break;
            default:
                echo json_encode("Error");
        }
    }
}
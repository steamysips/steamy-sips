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

    private function addProduct(): void
    {
    }

    private function deleteProduct(): void
    {
    }

    private function updateProduct(): void
    {
    }


    public function index(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->getProducts();
                break;
            case 'POST':
                $this->addProduct();
                break;
            case 'DELETE':
                $this->deleteProduct();
                break;
            case 'PUT':
                $this->updateProduct();
                break;
            default:
                http_response_code(400);
                die();
        }
    }
}
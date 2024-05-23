<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;

class Orders
{
    use Controller;

    private array $view_data = [];

    private function validateURL(): bool
    {
    $url = Utility::getURL();
    $parts = explode('/', $url);
    // Check if the URL matches the expected pattern
    $isValidURL = preg_match('/^orders\/\d+$/', $url) === 1;
    return $isValidURL;
    }

    private function getOrderIDFromURL(): ?int
    {
    if ($this->validateURL()) {
        $url = Utility::getURL();
        $parts = explode('/', $url);
        // Check if the last part of the URL is a valid integer
        $lastPart = end($parts);
        if (is_numeric($lastPart)) {
            return (int)$lastPart;
        } else {
            return null;
        }
    }
    return null;
    }


    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new Error())->handlePageNotFoundError();
            die();
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();

        $order_id = $this->getOrderIDFromURL();
        if ($order_id === null) {
            (new Error())->handlePageNotFoundError();
            die();
        }

        $order = Order::getByID($order_id);
        if (!$order) {
            (new Error())->handlePageNotFoundError();
            die();
        }

        $order_products = OrderProduct::getByID($order_id);

        $this->view_data['orders'] = $order;
        $this->view_data['orderproduct'] = $order_products;

        $this->view(
            'orders',
            $this->view_data,
            'Order',
            enableIndexing: false
        );
    }
}

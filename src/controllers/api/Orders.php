<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Opis\JsonSchema\{Errors\ErrorFormatter};
use Steamy\Core\Utility;
use Steamy\Model\Order;
use Steamy\Core\Model;
use Steamy\Model\OrderProduct;
use Steamy\Model\OrderStatus;

class Orders
{
    use Model;

    public static array $routes = [
        'GET' => [
            '/orders' => 'getAllOrders',
            '/orders/{id}' => 'getOrderById',
        ],
        'POST' => [
            '/orders' => 'createOrder',
        ],
        'PUT' => [
            '/orders/{id}' => 'updateOrder',
        ],
        'DELETE' => [
            '/orders/{id}' => 'deleteOrder',
        ]
    ];

    /**
     * Get the list of all orders.
     */
    public function getAllOrders(): void
    {
        $allOrders = Order::getAll();

        $result = [];
        foreach ($allOrders as $order) {
            $result[] = $order->toArray();
        }

        echo json_encode($result);
    }

    /**
     * Get the details of a specific order by its ID.
     */
    public function getOrderById(): void
    {
        $orderId = (int)Utility::splitURL()[3];

        $order = Order::getByID($orderId);

        if ($order === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }

        echo json_encode($order->toArray());
    }

    /**
     * Create a new order for products.
     */
    public function createOrder(): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                throw new \Exception('Invalid JSON input');
            }

            // Validate the order data against the schemas
            $orderValidationResult = Utility::validateAgainstSchema((object)$data, "orders/create.json");
            $orderProductValidationResult = Utility::validateAgainstSchema((object)$data, "orders/create_orderproduct.json");

            if (!$orderValidationResult->isValid() || !$orderProductValidationResult->isValid()) {
                $errors = array_merge(
                    (new ErrorFormatter())->format($orderValidationResult->error()),
                    (new ErrorFormatter())->format($orderProductValidationResult->error())
                );
                http_response_code(400);
                echo json_encode(['error' => $errors]);
                return;
            }

            // Ensure that the only allowed properties are included
            $lineItems = array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'] ?? null,
                    'cup_size' => $item['cup_size'] ?? null,
                    'milk_type' => $item['milk_type'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                    'unit_price' => $item['unit_price'] ?? null
                ];
            }, $data['line_items'] ?? []);

            // Create a new Order object
            $newOrder = new Order(
                store_id: (int)$data['store_id'],
                client_id: (int)$data['client_id'],
                line_items: $lineItems,
                pickup_date: isset($data['pickup_date']) ? Utility::stringToDate($data['pickup_date']) : null
            );

            // Save the order
            if ($newOrder->save()) {
                http_response_code(201);
                echo json_encode(['message' => 'Order created successfully', 'order_id' => $newOrder->getOrderID()]);
            } else {
                throw new \Exception('Failed to create order');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    /**
     * Update the details of an order with the specified ID.
     */
    public function updateOrder(): void
    {
        $orderId = (int)Utility::splitURL()[3];

        $order = Order::getByID($orderId);

        if ($order === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }

        $data = (object)json_decode(file_get_contents("php://input"), true);
        $result = Utility::validateAgainstSchema($data, "orders/update.json");

        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = ['error' => $errors];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        $lineItems = array_map(function ($item) {
            return new OrderProduct(
                product_id: $item['product_id'],
                cup_size: $item['cup_size'],
                milk_type: $item['milk_type'],
                quantity: $item['quantity'],
                unit_price: $item['unit_price']
            );
        }, $data->line_items);

        $order->setPickupDate($data->pickup_date ? Utility::stringToDate($data->pickup_date) : null);
        $order->setStatus(OrderStatus::from($data->status));

        try {
            if ($order->save()) {
                http_response_code(200);
                echo json_encode(['message' => 'Order updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update order']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete an order with the specified ID.
     */
    public function deleteOrder(): void
    {
        $orderId = (int)Utility::splitURL()[3];

        $order = Order::getByID($orderId);

        if ($order === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }

        try {
            $order->deleteOrder();
            http_response_code(204);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete order']);
        }
    }
}

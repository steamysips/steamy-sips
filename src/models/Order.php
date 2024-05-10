<?php

declare(strict_types=1);

namespace Steamy\Model;

use DateTime;
use Exception;
use Steamy\Core\Model;
use Steamy\Core\Utility;

class Order
{
    use Model;

    protected string $table = 'order';

    private int $store_id;
    private int $order_id;
    private OrderStatus $status;
    private DateTime $created_date;
    private ?DateTime $pickup_date; // ?DateTime type allows $pickup_date to be null
    private int $client_id;

    public function __construct(
        int $store_id,
        int $client_id,
        ?int $order_id = null,
        ?DateTime $pickup_date = null,
        OrderStatus $status = OrderStatus::PENDING, // Default to 'pending',
        DateTime $created_date = new DateTime(),
    ) {
        $this->store_id = $store_id;
        $this->order_id = $order_id ?? -1;
        $this->status = $status;
        $this->created_date = $created_date;
        $this->pickup_date = $pickup_date;
        $this->client_id = $client_id;
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status->value,
            'created_date' => $this->created_date->format('Y-m-d H:i:s'),
            'pickup_date' => $this->pickup_date?->format('Y-m-d H:i:s'),
            'client_id' => $this->client_id,
            'store_id' => $this->store_id
        ];
    }


    public function save(): bool
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // Get data to be inserted into the order table
        $order_data = $this->toArray();
        unset($order_data['order_id']); // Remove order_id as it's auto-incremented
        unset($order_data['status']); // Remove status as it's set to 'pending' by default
        unset($order_data['pickup_date']); // Remove pickup_date as it's set to NULL by default
        unset($order_data['created_date']); // Remove created_date as it's set by database

        Utility::show($order_data);
        // Perform insertion into the order table
        try {
            $new_id = $this->insert($order_data);
            if ($new_id === null) {
                return false;
            }
            $this->order_id = $new_id;
            return true;
        } catch (Exception $e) {
            echo $e;
            return false;
        }
    }

    /**
     * @param int $order_id
     * @return Order|null Order matching order ID.
     */
    public static function getByID(int $order_id): ?Order
    {
        if ($order_id < 0) {
            return null;
        }

        // Perform query to fetch order from the database
        $query = "SELECT * FROM `order` WHERE order_id = :order_id";
        $orderData = self::query($query, ['order_id' => $order_id]);

        // Check if order with the specified ID exists
        if (empty($orderData)) {
            return null;
        }

        // Extract order details from the query result
        $orderData = $orderData[0];

        // Create Order object with retrieved data
        return new Order(
            store_id: $orderData->store_id,
            client_id: $orderData->client_id,
            order_id: $orderData->order_id,
            pickup_date: $orderData->pickup_date ? Utility::stringToDate($orderData->pickup_date) : null,
            status: OrderStatus::from($orderData->status),
            created_date: Utility::stringToDate($orderData->created_date),
        );
    }

    private static function getOrderProducts(int $order_id): array
    {
        $query = "SELECT *
                  FROM order_product
                  WHERE order_id = :order_id";

        $data = self::query($query, ['order_id' => $order_id]);

        if (empty($data)) {
            return [];
        }

        $order_products_arr = [];

        // Iterate through each product data and create Product objects
        foreach ($data as $result) {
            $order_products_arr[] = new OrderProduct(
                product_id: $result->product_id,
                cup_size: $result->cup_size,
                milk_type: $result->milk_type,
                quantity: $result->quantity,
                unit_price: (float)$result->unit_price,
                order_id: $result->order_id,
            );
        }

        return $order_products_arr;
    }


    public function getOrderID(): int
    {
        return $this->order_id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->created_date;
    }

    public function getPickupDate(): ?DateTime
    {
        return $this->pickup_date;
    }

    public function setPickupDate(?DateTime $pickup_date): void
    {
        $this->pickup_date = $pickup_date;
    }

    public function getClientID(): int
    {
        return $this->client_id;
    }

    public function validate(): array
    {
        $errors = [];

        $validStatus = [OrderStatus::PENDING, OrderStatus::CANCELLED, OrderStatus::COMPLETED];
        if (!in_array($this->status, $validStatus)) {
            $errors['status'] = "Status must be one of: " . implode(', ', $validStatus);
        }

        return $errors;
    }

    /**
     * Adds a product to the order.
     *
     * @param OrderProduct $newOrderProduct
     * @return bool
     */
    public function addOrderProduct(OrderProduct $newOrderProduct): bool
    {
        $newOrderProduct->setOrderID($this->order_id);
        try {
            return $newOrderProduct->save();
        } catch (Exception) {
            return false;
        }
    }

    public function calculateTotalPrice(): float
    {
        $query = "SELECT SUM(unit_price * quantity) AS total_price 
        FROM order_product WHERE order_id = :order_id";
        
        $result = self::get_row($query, ['order_id' => $this->order_id]);
        
        if ($result) {
            return (float) $result->total_price;
            }
            
            return 0.0;
    }

    public function toHTML(): string
    {
    $html = <<<HTML
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price per Unit</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
    HTML;

    $query = "SELECT op.product_id, op.quantity, op.unit_price, p.name 
              FROM order_product op
              JOIN product p ON op.product_id = p.product_id
              WHERE op.order_id = :order_id";

    $orderProducts = self::query($query, ['order_id' => $this->order_id]);

    foreach ($orderProducts as $orderProduct) {
        $productName = $orderProduct->name;
        $quantity = $orderProduct->quantity;
        $pricePerUnit = $orderProduct->unit_price;
        $totalPrice = $pricePerUnit * $quantity;

        $html .= <<<HTML
        <tr>
            <td>$productName</td>
            <td>Qty $quantity</td>
            <td>\$$pricePerUnit</td>
            <td>\$$totalPrice</td>
        </tr>
        HTML;
    }

    $html .= <<<HTML
        </tbody>
    </table>
    HTML;

    return $html;
    }
}

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
    private string $status;
    private DateTime $created_date;
    private ?DateTime $pickup_date; // ?DateTime type allows $pickup_date to be null
    private int $client_id;

    /** @var OrderProduct[] Array of line items */
    private array $line_items; // array of order products

    public function __construct(
        int $store_id,
        int $client_id,
        array $line_items = [],
        ?int $order_id = null,
        ?DateTime $pickup_date = null,

        string $status = "pending",
        DateTime $created_date = new DateTime(),
    ) {
        $this->store_id = $store_id;
        $this->order_id = $order_id ?? -1;
        $this->status = $status;
        $this->created_date = $created_date;
        $this->pickup_date = $pickup_date;
        $this->client_id = $client_id;
        $this->line_items = $line_items;
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
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

        // check if order has at least 1 line item
        if (empty($this->line_items)) {
            return false;
        }

        $conn = self::connect();
        $conn->beginTransaction();

        // Get data to be inserted into the order table.
        // The remaining attributes are set to their default values by mysql
        $query = "insert into `order` (client_id, store_id) values(?, ?)";
        $stm = $conn->prepare($query);
        $success = $stm->execute([$this->client_id, $this->store_id]);

        if (!$success) {
            $conn->rollBack();
            $conn = null;
            return false;
        }
        $new_order_id = (int)$conn->lastInsertId();

        $query = <<< EOL
        insert into `order_product` (order_id, product_id, cup_size, milk_type, quantity, unit_price)
        values(:order_id, :product_id, :cup_size, :milk_type, :quantity, :unit_price)
        EOL;
        $stm = $conn->prepare($query);

        foreach ($this->line_items as $line_item) {
            $success = $stm->execute(['order_id' => $new_order_id, ... $line_item->toArray()]);

            if (!$success) {
                $conn->rollBack();
                $conn = null;
                return false;
            }
        }
        $this->order_id = $new_order_id;

        $conn->commit();
        $conn = null;
        return true;
    }

    /**
     * Adds a line item to the order.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function addLineItem(OrderProduct $orderProduct): void
    {
        $this->line_items[] = $orderProduct;
    }

    public function getLineItems(): array
    {
        return $this->line_items;
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
            status: $orderData->status,
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
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

        $validStatus = ['pending', 'cancelled', 'completed'];
        if (!in_array($this->status, $validStatus)) {
            $errors['status'] = "Status must be one of: " . implode(', ', $validStatus);
        }

        return $errors;
    }


    public function calculateTotalPrice(): float
    {
        // TODO: Use a single query to calculate total price
        return 0;
    }

    public function toHTML(): string
    {
        // TODO: get order products and names of each product using a single query

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

        // Iterate through each product in the order
        foreach ($this->products as $product) {
            // Get the product details
            $productName = $product['product']->getName();
            $quantity = $product['quantity'];
            $pricePerUnit = $product['product']->getPrice();
            $totalPrice = $quantity * $pricePerUnit;

            // Add a row for the product in the HTML table
            $html .= <<<HTML
                <tr>
                    <td>$productName</td>
                    <td>Qty $quantity</td>
                    <td>\$$pricePerUnit</td>
                    <td>\$$totalPrice</td>
                </tr>
            HTML;
        }

        // Close the HTML table
        $html .= <<<HTML
            </tbody>
        </table>
        HTML;

        return $html;
    }
}

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

    /** @var OrderProduct[] Array of line items */
    private array $line_items; // array of order products

    public function __construct(
        int $store_id,
        int $client_id,
        array $line_items = [],
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
        $this->line_items = $line_items;
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
        // check if order has at least 1 line item
        if (empty($this->line_items)) {
            return false;
        }

        $conn = self::connect();
        $conn->beginTransaction();

        // get data to be inserted into the order table.
        // the remaining attributes are set to their default values by mysql
        $query = "insert into `order` (client_id, store_id) values(?, ?)";
        $stm = $conn->prepare($query);
        $success = $stm->execute([$this->client_id, $this->store_id]);

        if (!$success) {
            $conn->rollBack();
            $conn = null;
            return false;
        }

        // get id of last inserted order
        $new_order_id = $conn->lastInsertId();

        if ($new_order_id === false) {
            $conn->rollBack();
            $conn = null;
            return false;
        }

        $new_order_id = (int)$new_order_id;

        // prepare a query for inserting a line item
        $query = <<< EOL
        insert into `order_product` (order_id, product_id, cup_size,
                                     milk_type, quantity, unit_price)
        values(:order_id, :product_id, :cup_size, :milk_type, :quantity, :unit_price)
        EOL;
        $stm = $conn->prepare($query);

        foreach ($this->line_items as $line_item) {
            // fetch product corresponding to line item
            $product = Product::getByID($line_item->getProductID());

            if (empty($product)) {
                // product does not exist
                $conn->rollBack();
                $conn = null;
                return false;
            }

            if (!$line_item->validate()) {
                // line item contains invalid attributes
                $conn->rollBack();
                $conn = null;
                return false;
            }

            $line_item->setOrderID($new_order_id);
            $line_item->setUnitPrice($product->getPrice());

            $success = $stm->execute($line_item->toArray());
            if (!$success) {
                $conn->rollBack();
                $conn = null;
                return false;
            }
            // TODO: Update stock level in store table
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


    public function calculateTotalPrice(): float
    {
        $query = "SELECT SUM(unit_price * quantity) AS total_price 
        FROM order_product WHERE order_id = :order_id";

        $result = self::get_row($query, ['order_id' => $this->order_id]);

        if ($result) {
            return (float)$result->total_price;
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

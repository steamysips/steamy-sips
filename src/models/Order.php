<?php

declare(strict_types=1);

namespace Steamy\Model;

use PDO;
use DateTime;
use Exception;
use PDOException;
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


    /**
     * Saves order to database
     * @throws Exception
     * @throws PDOException
     */
    public function save(): bool
    {
        // check if order has at least 1 line item
        if (empty($this->line_items)) {
            throw new Exception('Cart cannot be empty');
        }

        $conn = self::connect();
        $conn->beginTransaction();

        // validate store
        $store = Store::getByID($this->store_id);

        if (!$store) {
            $conn->rollBack();
            $conn = null;
            throw new Exception('Store does not exist');
        }

        // create a new order
        // Attributes missing in query are set to their default values by mysql
        $query = "insert into `order` (client_id, store_id) values(?, ?)";
        $insert_line_item_stm = $conn->prepare($query);
        $success = $insert_line_item_stm->execute([$this->client_id, $this->store_id]);

        if (!$success) {
            $conn->rollBack();
            $conn = null;
            throw new Exception('Order could not be created');
        }

        // get id of last inserted order
        $new_order_id = $conn->lastInsertId();

        if ($new_order_id === false) {
            $conn->rollBack();
            $conn = null;
            throw new Exception("Failed to retrieve last inserted order ID");
        }

        // cast string ID to integer
        $new_order_id = (int)$new_order_id;

        // prepare a query for inserting a line item in order_product table
        $query = <<< EOL
        insert into `order_product` (order_id, product_id, cup_size,
                                     milk_type, quantity, unit_price)
        values(:order_id, :product_id, :cup_size, :milk_type, :quantity, :unit_price)
        EOL;
        $insert_line_item_stm = $conn->prepare($query);

        // prepare a query for updating stock level
        $query = "update store_product
        set stock_level = :new_stock_level
        where store_id = :store_id
        and product_id = :product_id";
        $update_stock_stm = $conn->prepare($query);

        foreach ($this->line_items as $line_item) {
            if (!empty($line_item->validate())) {
                // line item contains invalid attributes
                $conn->rollBack();
                $conn = null;
                throw new Exception("Invalid line item:" . json_encode($line_item));
            }

            // fetch product corresponding to line item
            $product = Product::getByID($line_item->getProductID());

            if (empty($product)) {
                // product does not exist
                $conn->rollBack();
                $conn = null;
                throw new Exception("Product with ID " . $line_item->getProductID() . " does not exist");
            }

            // get stock level for current product
            $stock_level = $store->getProductStock($product->getProductID());

            if ($line_item->getQuantity() > $stock_level) {
                // store does not have enough stock
                $conn->rollBack();
                $conn = null;
                throw new Exception(
                    "Store with ID " . $this->store_id
                    . " has insufficient stock for product " . $line_item->getProductID()
                );
            }

            // insert into order_product table
            $line_item->setOrderID($new_order_id);
            $line_item->setUnitPrice($product->getPrice());

            $success = $insert_line_item_stm->execute($line_item->toArray());
            if (!$success) {
                $conn->rollBack();
                $conn = null;
                return false;
            }

            // update stock level in store table
            $new_stock_level = $stock_level - $line_item->getQuantity();
            $success = $update_stock_stm->execute(
                [
                    'product_id' => $product->getProductID(),
                    'store_id' => $this->store_id,
                    'new_stock_level' => $new_stock_level
                ]
            );
            if (!$success) {
                $conn->rollBack();
                $conn = null;
                throw new Exception(
                    "Unable to update stock level for store with ID " . $this->store_id
                );
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
     * @throws Exception
     */
    public function addLineItem(OrderProduct $orderProduct): void
    {
        $errors = $orderProduct->validate();
        if (!empty($errors)) {
            throw new Exception("Invalid line item: " . json_encode($errors));
        }
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

    /**
     * Deletes the order and associated line items from the database.
     */
    public function deleteOrder(): void
    {
        $conn = self::connect();
        $conn->beginTransaction();

        try {
            // Delete line items first
            $query = "DELETE FROM order_product WHERE order_id = :order_id";
            $stm = $conn->prepare($query);
            $stm->execute(['order_id' => $this->order_id]);

            // Delete the order itself
            $query = "DELETE FROM `order` WHERE order_id = :order_id";
            $stm = $conn->prepare($query);
            $stm->execute(['order_id' => $this->order_id]);

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
        } finally {
            $conn = null;
        }
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

    /**
     * Retrieves a list of orders for a specific client.
     *
     * @param int $client_id The ID of the client whose orders are to be retrieved.
     * @param int $limit The maximum number of orders to retrieve. Defaults to 5.
     * @return Order[] An array of Order objects ordered in descending order of created_date
     * @throws PDOException If there is an error executing the database query.
     */
    public static function getOrdersByClientId(int $client_id, int $limit = 5): array
    {
        $db = self::connect();
        $stmt = $db->prepare(
            '
        SELECT o.order_id, o.created_date, o.status, o.store_id, o.pickup_date, o.client_id
        FROM `order` o
        WHERE o.client_id = :client_id
        ORDER BY o.created_date DESC
        LIMIT :limit;
        '
        );
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $orderDataArray = $stmt->fetchAll(PDO::FETCH_OBJ);
        $orders = [];

        foreach ($orderDataArray as $orderData) {
            // Get the line items for this order
            $lineItems = self::getOrderProducts((int)$orderData->order_id);

            // Create an Order object with the retrieved data
            $orders[] = new Order(
                store_id: (int)$orderData->store_id,
                client_id: (int)$orderData->client_id,
                line_items: $lineItems,
                order_id: (int)$orderData->order_id,
                pickup_date: $orderData->pickup_date ? Utility::stringToDate($orderData->pickup_date) : null,
                status: OrderStatus::from($orderData->status),
                created_date: Utility::stringToDate($orderData->created_date),
            );
        }
        $db = null;
        return $orders;
    }


    public function getOrderID(): int
    {
        return $this->order_id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getStoreID(): int
    {
        return $this->store_id;
    }

    public function getStore(): ?Store
    {
        return Store::getByID($this->store_id);
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
}

<?php

declare(strict_types=1);

namespace Steamy\Model;

use DateTime;
use Exception;
use Steamy\Core\Model;

class Order
{
    use Model;

    private int $order_id;
    private string $status;
    private DateTime $created_date;
    private ?DateTime $pickup_date; // ?DateTime type allows $pickup_date to be null
    private string $street;
    private string $city;
    private District $district;
    private float $total_price;
    private Client $client;
    private array $products = []; // Each element of this array contains the following columns: product, milk_type, quantity, cup_size.


    public function __construct(Client $client, array $products)
    {
        // Set default values
        $this->order_id = -1;
        $this->status = "pending";
        $this->created_date = new DateTime();
        $this->pickup_date = null;
        $this->total_price = 0;
        $this->street = "";
        $this->city = "";

        // Set client attribute
        $this->client = $client;

        // Set products attribute
        $this->setProducts($products);
    }

    public function setProducts(array $products): void
    {
        $this->products = $products; // Updated attribute name
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
            'created_date' => $this->created_date->format('Y-m-d H:i:s'),
            'pickup_date' => $this->pickup_date?->format('Y-m-d H:i:s'),
            'street' => $this->street,
            'city' => $this->city,
            'district' => $this->district->getID(), // Return the district ID
            'total_price' => $this->total_price,
            'client_id' => $this->client->getUserID() // Return the client ID 
        ];
    }


    public function save(): void
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            return;
        }

        // Get data to be inserted into the order table
        $order_data = $this->toArray();
        unset($order_data['order_id']); // Remove order_id as it's auto-incremented
        unset($order_data['status']); // Remove status as it's set to 'pending' by default
        unset($order_data['pickup_date']); // Remove pickup_date as it's set to NULL by default

        // Perform insertion into the order table
        $this->insert($order_data, 'order');
    }

    public function getProducts(): array
    {
        return $this->products;
    }

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

        // Fetch client associated with the order
        $client = Client::getByID($orderData->client_id);

        if (!$client) {
            return null;
        }

        // Fetch products associated with the order
        $products = self::getOrderProducts($order_id);

        // Create Order object with retrieved data
        $order = new Order($client, $products);
        $order->order_id = $orderData->order_id;
        $order->status = $orderData->status;

        try {
            $order->created_date = new DateTime($orderData->created_date);
        } catch (Exception $e) {
            error_log('Error converting date: ' . $e->getMessage());
        }

        try {
            $order->pickup_date = $orderData->pickup_date ? new DateTime($orderData->pickup_date) : null;
        } catch (Exception $e) {
            error_log('Error converting date: ' . $e->getMessage());
        }

        $order->street = $orderData->street;
        $order->city = $orderData->city;
        $order->total_price = $orderData->total_price;

        return $order;
    }

    private static function getOrderProducts(int $order_id): array
    {
        $query = "SELECT product_id, milk_type, quantity, cup_size FROM order_product WHERE order_id = :order_id";
        $productsData = self::query($query, ['order_id' => $order_id]);

        // Initialize an empty array to store products
        $products = [];

        // Iterate through each product data and create Product objects
        foreach ($productsData as $productData) {
            // Create a product array with necessary information
            $product = [
                'product' => $productData->product,
                'milk_type' => $productData->milk_type,
                'quantity' => $productData->quantity,
                'cup_size' => $productData->cup_size
            ];

            // Add the product array to the products array
            $products[] = $product;
        }

        return $products;
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

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getDistrict(): District
    {
        return $this->district;
    }

    public function setDistrict(District $district): void
    {
        $this->district = $district;
    }

    public function getTotalPrice(): float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): void
    {
        $this->total_price = $total_price;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function validate(): array
    {
        $errors = [];

        $validStatus = ['pending', 'cancelled', 'completed'];
        if (!in_array($this->status, $validStatus)) {
            $errors['status'] = "Status must be one of: " . implode(', ', $validStatus);
        }

        if (empty($this->street)) {
            $errors['street'] = "Street address is required";
        }

        if (empty($this->city)) {
            $errors['city'] = "City is required";
        }

        if (empty($this->district->getName())) {
            $errors['district'] = 'District name is required';
        }

        if ($this->total_price < 0) {
            $errors['total_price'] = "Total price must be non-negative";
        }

        return $errors;
    }

    /**
     * Loads and returns an array of products associated with this order from the database.
     *
     * @return array An array of Product objects associated with this order.
     */
    public function loadProducts(): array
    {
        // Initialize an empty array to store Product objects
        $products = [];

        // Query the database for products related to this order
        $query = <<<SQL
        SELECT product_id, name, calories, stock_level, img_url, img_alt_text, category, price, description
        FROM product 
        WHERE product_id IN (SELECT product_id FROM order_product WHERE order_id = :order_id)
    SQL;

        // Execute the query and fetch the product records
        $productRecords = $this->query($query, ['order_id' => $this->order_id]);

        // Iterate through the retrieved product records and create Product objects
        foreach ($productRecords as $record) {
            // Create a new Product object and add it to the products array
            $product = new Product(
                $record->name,
                $record->calories,
                $record->stock_level,
                $record->img_url,
                $record->img_alt_text,
                $record->category,
                (float)$record->price,
                $record->description
            );
            $product->setProductID($record->product_id); // Set the product ID
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Adds a product to the order.
     *
     * @param Product $product The product to add.
     * @param string $milk_type The type of milk.
     * @param int $quantity The quantity of the product.
     * @param string $cup_size The cup size.
     * @return void
     */
    public function addProduct(Product $product, string $milk_type, int $quantity, string $cup_size): void
    {
        $this->products[] = [
            'product' => $product,
            'milk_type' => $milk_type,
            'quantity' => $quantity,
            'cup_size' => $cup_size
        ];
    }


    public function removeProduct(int $index): void
    {
        if (isset($this->products[$index])) {
            unset($this->products[$index]);
            // Reindex the array after removal
            $this->products = array_values($this->products);
        }
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = 0.0;
        foreach ($this->products as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            $totalPrice += $product->getPrice() * $quantity;
        }
        return $totalPrice;
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


    public function getQuantityForProduct(Product $product): int
    {
        // Query the order_product table to get the quantity for the specified product
        $query = "SELECT quantity FROM order_product WHERE order_id = :order_id AND product_id = :product_id";
        $params = ['order_id' => $this->getOrderID(), 'product_id' => $product->getProductID()];
        $result = $this->query($query, $params);

        // If there are no results, return 0
        if (empty($result)) {
            return 0;
        }

        // Otherwise, return the quantity
        return $result[0]->quantity;
    }


}

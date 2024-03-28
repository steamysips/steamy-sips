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


    /**
     * @throws Exception
     */
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
        return
            [
                'order_id' => $this->order_id,
                'status' => $this->status,
                'created_date' => $this->created_date,
                'pickup_date' => $this->pickup_date,
                'street' => $this->street,
                'city' => $this->city,
                'district' => $this->district,
                'total_price' => $this->total_price,
                'client' => $this->client->toArray()
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

    /**
    * Fetches a Client from the database by their ID.
    *
    * @param int $client_id The ID of the client to fetch.
    * @return Client|false The Client object if found, or false if not found.
    */
    public static function getByID(int $client_id): Client|false
    {
    // Perform query to fetch client from the database
    $query = "SELECT * FROM client WHERE user_id = :user_id";
    $result = self::get_row($query, ['user_id' => $client_id]);

    // Check if client with the specified ID exists
    if (!$result) {
        return false;
    }

    // Extract client details from the query result
    $client = new Client(
        $result->email,
        $result->first_name,
        $result->last_name,
        "dummy-password", // a dummy is used since original password is unknown
        $result->phone_no,
        new District($result->district_id),
        $result->street,
        $result->city
    );

    $client->setUserID($client_id);

    // store hash of true password
    $client->setPassword($result->password);

    return $client;
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

    public function getProducts(): array
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
        <table border='1'>
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
    
    
    private function getQuantityForProduct(Product $product): int
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
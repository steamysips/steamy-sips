<?php

namespace Steamy\Model;

use DateTime;
use Steamy\Core\Model;
use Steamy\Core\Utility;
class Order
{
    use Model;

    private int $order_id;
    private string $status;
    private DateTime $created_date;
    private ?DateTime $pickup_date; //?DateTime type allows $pickup_date to be null, providing flexibility when working with orders.
    private string $street;
    private string $city;
    private District $district;
    private float $total_price;
    private int $user_id;

    public function __construct(int $order_id)
    {
        $record = $this->first(['order_id' => $order_id]);

        $this->order_id = $record->order_id;
        $this->status = $record->status;
        $this->created_date = new DateTime($record->created_date);
        $this->pickup_date = isset($record->pickup_date) ? new DateTime($record->pickup_date) : null;
        $this->street = $record->street;
        $this->city = $record->city;
        $this->district = new District ($record->district);
        $this->total_price = $record->total_price;
        $this->user_id = $record->user_id;
    }

    public function toArray(): array
    {
        return
            (array)[
                'order_id' => $this->order_id,
                'status' => $this->status,
                'created_date' => $this->created_date,
                'pickup_date' => $this->pickup_date,
                'street' => $this->street,
                'city' => $this->city,
                'district' => $this->district,
                'total_price' => $this->total_price,
                'user_id' => $this->user_id
            ];
    }

    public function save(): void
    {
    // If attributes of the object are invalid, exit
    if (count($this->validate()) > 0) {
        Utility::show($this->validate());
        return;
    }

    // Get data to be inserted into the order table
    $order_data = $this->toArray();
    unset($order_data['order_id']); // Remove order_id as it's auto-incremented
    unset($order_data['status']); // Remove status as it's set to 'pending' by default
    unset($order_data['pickup_date']); // Remove pickup_date as it's set to NULL by default

    // Perform insertion into the order table
    $this->insert($order_data, 'order');

    $inserted_record = self::first($order_data, 'order');

    if (!$inserted_record) {
        return;
    }
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

    public function getUserID(): int
    {
        return $this->user_id;
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
        SELECT * FROM product 
        WHERE product_id IN (SELECT product_id FROM order_product WHERE order_id = :order_id)
        SQL;

        // Execute the query and fetch the product records
        $productRecords = $this->query($query, ['order_id' => $this->order_id]);

        // Iterate through the retrieved product records and create Product objects
        foreach ($productRecords as $record) {
            // Create a new Product object and add it to the products array
            $product = new Product($record->product_id);
            $products[] = $product;
        }

        return $products;
    }



}
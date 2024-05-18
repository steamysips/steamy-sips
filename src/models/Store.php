<?php

declare(strict_types=1);

namespace Steamy\Model;

use Steamy\Core\Model;

class Store
{
    use Model;

    private int $store_id;
    private string $phone_no;
    private Location $address;

    public function __construct(
        int $store_id = null,
        string $phone_no = null,
        Location $address = null
    ) {
        $this->store_id = $store_id ?? -1;
        $this->phone_no = $phone_no ?? "";
        $this->address = $address ?? new Location();
    }

    public static function getByID(int $store_id): ?Store
    {
        if (empty($store_id) || $store_id < 0) {
            return null;
        }

        $query = <<< EOL
             SELECT store_id, phone_no, street, ST_X(coordinate) as latitude,
             ST_Y(coordinate) as longitude, district_id, city
             FROM store WHERE store_id = :id
        EOL;
        $params = ['id' => $store_id];

        $result = Store::query($query, $params);

        if (empty($result)) {
            return null;
        }

        $result = $result[0];

        $address = new Location(
            street: $result->street,
            city: $result->city,
            district_id: $result->district_id,
            latitude: $result->latitude,
            longitude: $result->longitude
        );

        return new Store(store_id: $result->store_id, phone_no: $result->phone_no, address: $address);
    }

    /**
     * @return array An associative array indexed by attribute name where each value is a primitive
     */
    public function toArray(): array
    {
        return [
            'store_id' => $this->store_id,
            'phone_no' => $this->phone_no,
            'street' => $this->address->getStreet(),
            'city' => $this->address->getCity(),
            'district_id' => $this->address->getDistrictID(),
            'latitude' => $this->address->getLatitude(),
            'longitude' => $this->address->getLongitude()
        ];
    }

    /**
     * @return Store[] An array of all Store objects
     */
    public static function getAll(): array
    {
        $query = "SELECT store_id FROM store;";
        $results = self::query($query);
        if (empty($results)) {
            return [];
        }

        $stores = [];
        foreach ($results as $result) {
            $stores[] = Store::getByID($result->store_id);
        }
        return $stores;
    }

    /**
     * Inserts current object to database. store_id is set automatically by database
     * @return bool Whether operation was successful
     */
    public function save(): bool
    {
        if (!empty($this->validate())) {
            return false;
        }

        // Get data to be inserted into the table
        $data = $this->toArray();
        unset($data['store_id']); // Remove store_id as it's auto-incremented

        $query = <<< EOL
        INSERT INTO store (phone_no, street, coordinate, district_id, city)
        VALUES(:phone_no, :street, POINT(:latitude, :longitude), :district_id, :city)
        EOL;

        $conn = self::connect();
        $stm = $conn->prepare($query);
        $stm->execute($data);

        if ($stm->rowCount() === 0) {
            $conn = null;
            return false;
        }

        $this->store_id = (int)$conn->lastInsertId();

        $conn = null;
        return true;
    }

    public function validate(): array
    {
        $errors = $this->address->validate();

        // Perform phone number length check
        if (strlen($this->phone_no) < 7) {
            $errors ['phone_no'] = "Phone number must be at least 7 characters long";
        }

        // Validate latitude and longitude
        $latitude = $this->address->getLatitude();
        $longitude = $this->address->getLongitude();

        if ($latitude == null || $longitude == null ||
            ($latitude < -90 || $latitude > 90 ||
                $longitude < -180 || $longitude > 180)) {
            $errors['coordinates'] = "Invalid latitude or longitude.";
        }

        return $errors;
    }

    public function getProductStock(int $product_id): int
    {
        $query = "SELECT stock_level FROM store_product WHERE store_id = :store_id AND product_id = :product_id;";
        $params = ['store_id' => $this->store_id, 'product_id' => $product_id];
        $result = self::query($query, $params);

        if (!empty($result)) {
            return (int)$result[0]->stock_level;
        } else {
            return 0; // Product not found in the store or stock level is 0
        }
    }

    public function getProducts(): array
    {
        $query = "SELECT p.* FROM product p JOIN store_product sp ON p.product_id = sp.product_id WHERE sp.store_id = :store_id;";
        $params = ['store_id' => $this->store_id];
        $results = self::query($query, $params);

        $products = [];
        foreach ($results as $result) {
            $products[] = new Product(
                $result->product_id,
                $result->name,
                $result->calories,
                $result->img_url,
                $result->img_alt_text,
                $result->category,
                $result->price,
                $result->description
            );
        }
        return $products;
    }

    public function getStoreID(): int
    {
        return $this->store_id;
    }

    public function setStoreID(
        int $store_id
    ): void {
        $this->store_id = $store_id;
    }

    public function getPhoneNo(): string
    {
        return $this->phone_no;
    }

    public function setPhoneNo(
        string $phone_no
    ): void {
        $this->phone_no = $phone_no;
    }

    public function getAddress(): Location
    {
        return $this->address;
    }

    public function setAddress(
        Location $address
    ): void {
        $this->address = $address;
    }
}

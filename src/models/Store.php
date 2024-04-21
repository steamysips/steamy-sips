<?php

declare(strict_types=1);

namespace Steamy\Model;

use Exception;
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
     * Inserts current object to database. store_id is set automatically by database
     * @return bool Whether operation was successful
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        // Get data to be inserted into the table
        $data = $this->toArray();
        unset($data['store_id']); // Remove store_id as it's auto-incremented

        $query = <<< EOL
        INSERT INTO store (phone_no, street, coordinate, district_id, city)
        VALUES(:phone_no, :street, POINT(:latitude, :longitude), :district_id, :city)
        EOL;

        try {
            Store::query($query, $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function validate(): bool
    {
        // TODO
        return true;
    }

    public function getProductStock(int $product_id): int
    {
        // TODO
        return 0;
    }

    public function getProducts(): array
    {
        // TODO: Return an array of Product objects
        return [];
    }

    public function getStoreId(): int
    {
        return $this->store_id;
    }

    public function setStoreId(
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

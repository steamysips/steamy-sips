<?php

declare(strict_types=1);

namespace Steamy\Model;

use PHPUnit\Exception;
use Steamy\Core\Model;

class Store
{
    use Model;

    private int $store_id;
    private string $phone_no;
    private Location $address;

    public function __construct(int $store_id, string $phone_no, Location $address)
    {
        $this->store_id = $store_id;
        $this->phone_no = $phone_no;
        $this->address = $address;
    }

    public static function getByID(): ?Store
    {
        // TODO
        return null;
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
            'district' => $this->address->getDistrict()->getID(),
            'latitude' => $this->address->getLatitude(),
            'longitude' => $this->address->getLongitude()
        ];
    }

    /**
     * Inserts current object to database
     * @return bool Whether operation was successful
     */
    public function save(): bool
    {
        try {
            // Get data to be inserted into the table
            $data = $this->toArray();
            unset($data['store_id']); // Remove store_id as it's auto-incremented
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProductStock(int $product_id): int
    {
        // TODO
        return 0;
    }

    public function getProducts():array
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

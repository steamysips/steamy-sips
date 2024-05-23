<?php

declare(strict_types=1);

namespace Steamy\Model;

use Exception;
use Steamy\Core\Model;

class OrderProduct
{
    use Model;

    protected string $table = 'order_product';

    private int $order_id;
    private int $product_id;
    private string $cup_size;
    private string $milk_type;
    private int $quantity;
    private float $unit_price;

    /**
     * Create a new OrderProduct object
     * @param int $product_id
     * @param string $cup_size
     * @param string $milk_type
     * @param int $quantity
     * @param float|null $unit_price If not set, the default $unit_price is -1.
     * @param int|null $order_id If not set, the default $order_id is -1.
     */
    public function __construct(
        int $product_id,
        string $cup_size,
        string $milk_type,
        int $quantity,
        ?float $unit_price = null,
        ?int $order_id = null,
    ) {
        $this->order_id = $order_id ?? -1;
        $this->product_id = $product_id;
        $this->cup_size = $cup_size;
        $this->milk_type = $milk_type;
        $this->quantity = $quantity;
        $this->unit_price = $unit_price ?? -1;
    }

    public function save(): bool
    {
        // If attributes of the object are invalid, exit
        if (count($this->validate()) > 0) {
            return false;
        }

        // set unit price
        $this->unit_price = Product::getByID($this->product_id)->getPrice();

        // Perform insertion into the order table
        try {
            $this->insert($this->toArray(), $this->table);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function validate(): array
    {
        $errors = [];

        if (filter_var($this->quantity, FILTER_SANITIZE_NUMBER_INT) <= 0) {
            $errors['quantity'] = 'Quantity must be a positive integer';
        }

        if (!in_array($this->milk_type, ['almond', 'coconut', 'oat', 'soy'])) {
            $errors['milk_type'] = 'Milk type invalid';
        }

        if (!in_array($this->cup_size, ['small', 'medium', 'large'])) {
            $errors['cup_size'] = 'Cup size type invalid';
        }

        if ($this->unit_price <= 0) {
            $errors['unit_price'] = 'Unit price cannot be negative';
        }

        return $errors;
    }

    public static function getByID(int $order_id, int $product_id = null): ?OrderProduct
    {
    $query = 'SELECT * FROM order_product WHERE order_id = :order_id';
    $params = ['order_id' => $order_id];

    if ($product_id !== null) {
        $query .= ' AND product_id = :product_id';
        $params['product_id'] = $product_id;
    }

    $result = self::query($query, $params);
    if (empty($result)) {
        return null;
    }

    // Assuming there's only one product for a given order if product_id is not provided
    if ($product_id === null) {
        $result = $result[0];
    }

    return new OrderProduct(
        product_id: $result->product_id,
        cup_size: $result->cup_size,
        milk_type: $result->milk_type,
        quantity: $result->quantity,
        unit_price: (float)$result->unit_price,
        order_id: $result->order_id,
    );
   }

    public function getOrderID(): int
    {
        return $this->order_id;
    }

    public function getProductID(): int
    {
        return $this->product_id;
    }

    public function getProductName(): string
    {
        return Product::getByID($this->product_id)->getName();
    }

    public function getCupSize(): string
    {
        return $this->cup_size;
    }

    public function getMilkType(): string
    {
        return $this->milk_type;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    public function setOrderID(int $order_id): void
    {
        $this->order_id = $order_id;
    }

    public function setProductID(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function setCupSize(string $cup_size): void
    {
        $this->cup_size = $cup_size;
    }

    public function setMilkType(string $milk_type): void
    {
        $this->milk_type = $milk_type;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setUnitPrice(float $unit_price): void
    {
        $this->unit_price = $unit_price;
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'cup_size' => $this->cup_size,
            'milk_type' => $this->milk_type,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
        ];
    }
}

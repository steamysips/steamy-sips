<?php

declare(strict_types=1);

namespace Steamy\Model;

use Exception;
use Steamy\Core\Model;
use Steamy\Model\OrderMilkType;
use Steamy\Model\OrderCupSize;

class OrderProduct
{
    use Model;

    protected string $table = 'order_product';

    private int $order_id;
    private int $product_id;
    private OrderCupSize $cup_size;
    private OrderMilkType $milk_type;
    private int $quantity;
    private float $unit_price;

    /**
     * Create a new OrderProduct object
     * @param int $product_id
     * @param OrderCupSize $cup_size
     * @param OrderMilkType $milk_type
     * @param int $quantity
     * @param float|null $unit_price If not set, the default $unit_price is -1.
     * @param int|null $order_id If not set, the default $order_id is -1.
     */
    public function __construct(
        int $product_id,
        OrderCupSize $cup_size,
        OrderMilkType $milk_type,
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

        // Validate milk type using enum values
        if (!in_array($this->milk_type, [OrderMilkType::ALMOND, OrderMilkType::COCONUT, OrderMilkType::OAT, OrderMilkType::SOY])) {
            $errors['milk_type'] = 'Milk type invalid';
        }

        // Validate cup size using enum values
        if (!in_array($this->cup_size, [OrderCupSize::SMALL, OrderCupSize::MEDIUM, OrderCupSize::LARGE])) {
            $errors['cup_size'] = 'Cup size type invalid';
        }

        if ($this->unit_price <= 0) {
            $errors['unit_price'] = 'Unit price cannot be negative';
        }

        return $errors;
    }

    /**
     * order_id and product_id are the primary of the record to be searched.
     * @param int $order_id
     * @param int $product_id
     * @return OrderProduct|null
     */
    public static function getByID(int $order_id, int $product_id): ?OrderProduct
    {
        $query = 'SELECT * FROM order_product WHERE order_id = ? and product_id= ?';
        $params = [$order_id, $product_id];

        $result = self::query($query, $params);
        if (empty($result)) {
            return null;
        }

        $result = $result[0];

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

    public function getCupSize(): OrderCupSize
    {
        return $this->cup_size;
    }

    public function getMilkType(): OrderMilkType
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

    public function setCupSize(OrderCupSize $cup_size): void
    {
        $this->cup_size = $cup_size;
    }

    public function setMilkType(OrderMilkType $milk_type): void
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
            'cup_size' => $this->cup_size->value, 
            'milk_type' => $this->milk_type->value, 
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
        ];
    }
}

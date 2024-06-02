<?php

declare(strict_types=1);

namespace Steamy\Tests\helpers;

use DateTime;
use Exception;
use Steamy\Core\Database;
use Steamy\Model\Client;
use Steamy\Model\Location;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Product;
use Faker\Generator;
use Steamy\Model\Review;
use Steamy\Model\Store;

trait TestHelper
{
    use Database;

    private static ?Generator $faker;
    private static int $seed;


    /**
     * Clears data from all tables except district table.
     * @return void
     */
    public static function resetDatabase(): void
    {
        $conn = self::connect();
        $conn->beginTransaction();

        // Order of deletion is important to prevent foreign key violation
        $query = <<< SQL
            DELETE FROM password_change_request;

            DELETE FROM `order_product`;
            DELETE FROM `order`;

            DELETE FROM `comment`;
            DELETE FROM `review`;
            
            DELETE FROM `administrator`;
            DELETE FROM `client`;
            DELETE FROM `user`;

            DELETE FROM `store_product`;
            DELETE FROM `store`;
            DELETE FROM `product`;
        SQL;

        $conn->exec($query);
        $conn->commit();
        $conn = null;
    }

    /**
     * Creates a client and saves it to database
     * @return Client
     * @throws Exception
     */
    public static function createClient(): Client
    {
        $client = new Client(
            self::$faker->email(),
            self::$faker->firstName(),
            self::$faker->lastName(),
            self::$faker->password(),
            self::$faker->phoneNumber(),
            new Location(self::$faker->streetAddress(), self::$faker->city(), self::$faker->numberBetween(1, 9))
        );

        $success = $client->save();
        if (!$success) {
            throw new Exception('Unable to save a unique client to database');
        }
        return $client;
    }

    /**
     * Creates a product and saves it to database.
     * @return Product
     * @throws Exception
     */
    public static function createProduct(): Product
    {
        $product = new Product(
            self::$faker->company(),
            70,
            "Velvet.jpeg",
            self::$faker->sentence(),
            self::$faker->word(),
            6.50,
            self::$faker->sentence(),
            new DateTime()
        );

        $success = $product->save();

        if (!$success) {
            throw new Exception('Unable to save product to database');
        }

        return $product;
    }

    /**
     * Create a review and saves it to database.
     * @param Product $product A valid product already present in database
     * @param Client $client A valid client already present in database
     * @param bool $verified Whether to create an order for client for given product.
     * @return Review
     * @throws Exception
     */
    public static function createReview(Product $product, Client $client, bool $verified = false): Review
    {
        if ($verified) {
            // place an order for  client and product

            // create store
            $store = new Store(
                phone_no: "13213431",
                address: new Location(
                    street: "Royal",
                    city: "Curepipe",
                    district_id: 1,
                    latitude: 50,
                    longitude: 50
                )
            );
            $success = $store->save();
            if (!$success) {
                throw new Exception('Unable to create store');
            }

            // Add stock to the store for the product to be bought
            $store->addProductStock($product->getProductID(), 10);

            $order = new Order($store->getStoreID(), $client->getUserID(), [
                new OrderProduct($product->getProductID(), 'small', 'oat', 1)
            ]);

            $success = $order->save();
            if (!$success) {
                throw new Exception('Unable to save order');
            }
        }

        $review = new Review(
            product_id: $product->getProductID(),
            client_id: $client->getUserID(),
            text: "This is a test review.",
            rating: 5
        );

        $success = $review->save();

        if (!$success) {
            throw new Exception('Unable to save review');
        }

        return $review;
    }

    public static function logAction($action)
    {
        // Implementation of logging an action
    }
}
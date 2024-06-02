<?php

declare(strict_types=1);

namespace Steamy\Tests\helpers;

use Exception;
use Faker\Factory;
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
     * Initializes faker generator with a random seed
     * @return void
     */
    public static function initFaker(): void
    {
        self::$faker = Factory::create();
        self::$seed = mt_rand();
        self::$faker->seed(self::$seed);
    }

    /**
     * Prints current faker seed to terminal
     * @return void
     */
    public static function printFakerSeed(): void
    {
        $seed = self::$seed;

        $error_message = <<< EOL
        
        ------------ Faker seed ------------
        Faker seed for failed test: $seed
        ------------------------------------
        
        EOL;

        error_log($error_message);
    }

    /**
     * Clears data from all tables except district table.
     * @return void
     */
    public static function resetDatabase(): void
    {
        $conn = self::connect();

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
        $conn = null;
    }

    /**
     * Creates a random client and saves it to database.
     * Client email is guaranteed to be unique.
     * @return Client
     * @throws Exception
     */
    public static function createClient(): Client
    {
        $client = new Client(
            self::$faker->unique()->email(),
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
     * Creates a random product and saves it to database.
     * @return Product
     * @throws Exception
     */
    public static function createProduct(): Product
    {
        $product = new Product(
            name: self::$faker->company(),
            calories: self::$faker->numberBetween(1, 500),
            img_url: "Velvet.jpeg",
            img_alt_text: self::$faker->sentence(),
            category: self::$faker->lexify(),
            price: 6.50,
            description: self::$faker->sentence()
        );

        $success = $product->save();

        if (!$success) {
            $json = json_encode($product->toArray());
            $errors = json_encode($product->validate());

            $msg = <<< EOL
            Unable to save product to database:
            $json
            
            Attribute errors:
            $errors
            EOL;

            throw new Exception($msg);
        }

        return $product;
    }

    /**
     * @throws Exception
     */
    public static function createStore(): Store
    {
        $store = new Store(
            phone_no: self::$faker->phoneNumber(),
            address: new Location(
                street: self::$faker->streetAddress(),
                city: self::$faker->city(),
                district_id: self::$faker->numberBetween(1, 9),
                latitude: self::$faker->numberBetween(-90, 90),
                longitude: self::$faker->numberBetween(-180, 180)
            )
        );

        $success = $store->save();

        if (!$success) {
            throw new Exception('Unable to save store to database');
        }
        return $store;
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
            $store = self::createStore();

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
            text: self::$faker->sentence(10),
            rating: self::$faker->numberBetween(1, 5)
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
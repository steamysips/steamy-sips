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
     * Creates a random valid client and may save it to database.
     * Client email is guaranteed to be unique.
     * @param bool $saveToDatabase Defaults to true.
     * @return Client
     * @throws Exception
     */
    public static function createClient(bool $saveToDatabase = true): Client
    {
        $first_name = self::$faker->firstName();
        $last_name = self::$faker->lastName();

        // ensure that length is correct
        if (strlen($first_name) < 3) {
            $first_name .= "aaa";
        }
        if (strlen($last_name) < 3) {
            $last_name .= "aaa";
        }

        $client = new Client(
            self::$faker->unique()->email(),
            $first_name,
            $last_name,
            self::$faker->password(),
            self::$faker->phoneNumber(),
            new Location(self::$faker->streetAddress(), self::$faker->city(), self::$faker->numberBetween(1, 9))
        );

        if (!$saveToDatabase) {
            return $client;
        }

        $success = $client->save();
        if (!$success) {
            $json = json_encode($client->toArray());
            $errors = json_encode($client->validate());

            $msg = <<< EOL
            Unable to save client to database:
            $json
            
            Attribute errors:
            $errors
            EOL;

            throw new Exception($msg);
        }
        return $client;
    }

    /**
     * Creates a random valid product and may save it to database.
     * @param bool $saveToDatabase Defaults to True.
     * @return Product
     * @throws Exception
     */
    public static function createProduct(bool $saveToDatabase = true): Product
    {
        $img_ext = self::$faker->randomElement(['png', 'jpeg', 'avif', 'jpg', 'webp']);
        $product_name = self::$faker->words(2, true);

        $product = new Product(
            name: $product_name,
            calories: self::$faker->numberBetween(1, 500),
            img_url: $product_name . "." . $img_ext,
            img_alt_text: self::$faker->sentence(),
            category: self::$faker->lexify(),
            price: 6.50,
            description: self::$faker->sentence()
        );

        if (!$saveToDatabase) {
            return $product;
        }

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
    public static function createStore(bool $saveToDatabase = true): Store
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

        if (!$saveToDatabase) {
            return $store;
        }

        $success = $store->save();

        if (!$success) {
            $json = json_encode($store->toArray());
            $errors = json_encode($store->validate());

            $msg = <<< EOL
            Unable to save store to database:
            $json
            
            Attribute errors:
            $errors
            EOL;

            throw new Exception($msg);
        }
        return $store;
    }

    /**
     * Create a review and saves it to database.
     * @param Product $product A valid product already present in database
     * @param Client $client A valid client already present in database
     * @param int|null $rating Rating for review
     * @param bool $verified Whether to create an order for client for given product.
     * @return Review
     * @throws Exception
     */
    public static function createReview(
        Product $product,
        Client $client,
        int $rating = null,
        bool $verified = false
    ): Review {
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
                $json = json_encode($order->toArray());
                $errors = json_encode($order->validate());

                $msg = <<< EOL
                Unable to save order to database:
                $json
                
                Attribute errors:
                $errors
                EOL;
                throw new Exception($msg);
            }
        }

        $review = new Review(
            product_id: $product->getProductID(),
            client_id: $client->getUserID(),
            text: self::$faker->sentence(10),
            rating: $rating ?? self::$faker->numberBetween(1, 5)
        );

        $success = $review->save();

        if (!$success) {
            $json = json_encode($review->toArray());
            $errors = json_encode($review->validate());

            $msg = <<< EOL
                Unable to save review to database:
                $json
                
                Attribute errors:
                $errors
                EOL;
            throw new Exception($msg);
        }

        return $review;
    }
}
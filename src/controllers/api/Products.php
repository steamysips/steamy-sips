<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Opis\JsonSchema\{Helper, Validator, Errors\ErrorFormatter};
use Steamy\Core\Utility;
use Steamy\Model\Product;
use Steamy\Core\Model;

class Products
{
    use Model;

    public static array $routes = [
        'GET' => [
            '/products' => 'getAllProducts',
            '/products/categories' => 'getProductCategories',
            '/products/{id}' => 'getProductById',
            '/products/{id}/reviews' => 'getAllReviewsForProduct',
        ],
        'POST' => [
            '/products' => 'createProduct',
        ],
        'PUT' => [
            '/products/{id}' => 'updateProduct',
        ],
        'DELETE' => [
            '/products/{id}' => 'deleteProduct',
        ]
    ];

    /**
     * Get the list of all products available in the store.
     */
    public function getAllProducts(): void
    {
        // Retrieve all products from the database
        $allProducts = Product::getAll();

        // Convert products to array format
        $result = [];
        foreach ($allProducts as $product) {
            $result[] = $product->toArray();
        }

        // Return JSON response
        echo json_encode($result);
    }

    /**
     * Get the details of a specific product by its ID.
     */
    public function getProductById(): void
    {
        $productId = (int)Utility::splitURL()[3];

        // Retrieve product details from the database
        $product = Product::getByID($productId);

        // Check if product exists
        if ($product === null) {
            // Product not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        // Return JSON response
        echo json_encode($product->toArray());
    }

    /**
     * Get the list of product categories.
     */
    public function getProductCategories(): void
    {
        // Retrieve all product categories from the database
        $categories = Product::getCategories();

        // Return JSON response
        echo json_encode($categories);
    }

    /**
     * Create a new product entry in the database.
     */
    public function createProduct(): void
    {
        $data = (object) json_decode(file_get_contents("php://input"), true);

        var_dump($data);

        $schemaPath = __DIR__ . '/../../../resources/schemas';
        $validator = new Validator();

        $validator->resolver()->registerPrefix(
            "https://example.com/",
            $schemaPath,
        );

        $result = $validator->validate(
            $data,
            "https://example.com/products/create.json"
        );



        if (!($result->isValid())) {
            $errors = (new ErrorFormatter())->format($result->error());
            $response = [
                'errors' => $errors
            ];
            http_response_code(400);
            echo json_encode($response);
            return;
        }

        return;

        // Create a new Product object
        $newProduct = new Product(
            $data['name'],
            (int)$data['calories'],
            $data['img_url'],
            $data['img_alt_text'],
            $data['category'],
            (float)$data['price'],
            $data['description']
        );

        // Save the new product to the database
        if ($newProduct->save()) {
            // Product created successfully, return 201 Created
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully', 'product_id' => $newProduct->getProductID()]
            );
        } else {
            // Failed to create product, return 500 Internal Server Error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }
    }

    /**
     * Delete a product with the specified ID.
     */
    public function deleteProduct(): void
    {
        $productId = (int)Utility::splitURL()[3];

        // Retrieve the product by ID
        $product = Product::getByID($productId);

        // Check if product exists
        if ($product === null) {
            // Product not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        // Attempt to delete the product
        if ($product->deleteProduct()) {
            // Product successfully deleted
            http_response_code(204); // No Content
        } else {
            // Failed to delete the product
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to delete product']);
        }
    }

    /**
     * Update the details of a product with the specified ID.
     */
    public function updateProduct(): void
    {
        $productId = (int)Utility::splitURL()[3];

        // Retrieve PUT request data
        $putData = json_decode(file_get_contents("php://input"), true);

        // Check if PUT data is valid
        if (empty($putData)) {
            // Invalid JSON data
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        // Retrieve existing product
        $product = Product::getByID($productId);

        // Check if product exists
        if ($product === null) {
            // Product not found
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        // Update product in the database
        $success = $product->updateProduct($putData);

        if ($success) {
            // Product updated successfully
            http_response_code(200); // OK
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            // Failed to update product
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to update product']);
        }
    }

    /**
     * Get all reviews for a particular product by its ID.
     */
    public function getAllReviewsForProduct(): void
    {
        // Get product ID from URL
        $productId = (int)Utility::splitURL()[3];

        // Instantiate the Reviews controller
        $reviewsController = new Reviews();

        // Call the method to get all reviews for the specified product
        // Since the Reviews controller method expects the ID to be in the URL, we'll set it directly
        $_SERVER['REQUEST_URI'] = "/api/v1/products/$productId/reviews";

        // Call the method from the Reviews controller
        $reviewsController->getAllReviewsForProduct();
    }
}

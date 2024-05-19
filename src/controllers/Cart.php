<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Exception;
use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Mailer;
use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
use Steamy\Model\Product;
use Steamy\Model\Store;

class Cart
{
    use Controller;

    private array $view_data = ['cart_items' => [], 'stores' => []];

    private function displayCart(): void
    {
        $cart = json_decode(file_get_contents('php://input'), true);
        $this->view_data['cart_total'] = 0;
        // loop through each cart item
        foreach ($cart as $item) {
            // fetch corresponding product based on product ID
            $product_id = filter_var($item['productID'], FILTER_VALIDATE_INT);
            $cart_item['product'] = Product::getByID($product_id);

            // ignore invalid cart items with invalid product IDs
            if (empty($cart_item['product'])) {
                continue;
            }

            $cart_item['quantity'] = filter_var($item['quantity'], FILTER_VALIDATE_INT);
            $cart_item['milkType'] = strtolower($item['milkType']);
            $cart_item['cupSize'] = strtolower($item['cupSize']);
            $cart_item['subtotal'] = $cart_item['quantity'] * $cart_item['product']->getPrice();
            $this->view_data['cart_total'] += $cart_item['subtotal'];

            $this->view_data['cart_items'][] = $cart_item;
        }

        $this->view_data['stores'] = Store::getAll();

        $this->view(
            'cart',
            $this->view_data,
            template_title: "Review order",
            template_meta_description: "Your ultimate shopping cart at Steamy Sips.
            Review your chosen items, adjust quantities, and proceed to checkout seamlessly.
             Savor the convenience of online shopping with us.",
            enableIndexing: false
        );
    }

    private function validateURL(): bool
    {
        return in_array(Utility::getURL(), ['cart', 'cart/upload', 'cart/checkout']);
    }

    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new Error())->handlePageNotFoundError();
            die();
        }
    }

    private function handleCheckout(): void
    {
        // TODO: write appropriate errors to Cart view instead of sending response code

        // check if user is logged in
        $signed_client = $this->getSignedInClient();
        if (!$signed_client) {
            http_response_code(401);
            echo json_encode(['error' => 'You must login first']);
            return;
        }

        $form_data = json_decode(file_get_contents('php://input'), true);

        if (empty($form_data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cart cannot be empty']);
            return;
        }

        // Validate store id
        $store_id = filter_var($form_data['store_id'] ?? "", FILTER_VALIDATE_INT);

        if (!$store_id || empty(Store::getByID($store_id))) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid store']);
            return;
        }

        // create and populate new Order object
        $new_order = new Order(store_id: $store_id, client_id: $signed_client->getUserID());
        foreach ($form_data['items'] as $item) {
            $line_item = new OrderProduct(
                product_id: filter_var($item['productID'], FILTER_VALIDATE_INT),
                cup_size: strtolower($item['cupSize']),
                milk_type: strtolower($item['milkType']),
                quantity: filter_var($item['quantity'], FILTER_VALIDATE_INT)
            );
            $new_order->addLineItem($line_item);
        }

        // save order
        $success_order = false;
        try {
            $success_order = $new_order->save();
            http_response_code($success_order ? 201 : 400);
        } catch (Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }

        // send confirmation email if order was successfully saved
        if ($success_order) {
            try {
                $signed_client->sendOrderConfirmationEmail($new_order);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // client is requesting /cart for the first time

            // display loading page first and inject a script that does 3 things:
            // 1. send a POST request with cart data to server
            // 2. re-render page when server sends cart page
            // 3. initialize new cart page

            $this->view(
                'loading',
                template_title: "Review order",
                template_tags: "<script src='/js/cart_view.bundle.js'></script>",
                template_meta_description: "Experience anticipation as your journey begins at Steamy Sips.
             Our loading page sets the stage for your flavorful adventure. Sit back, relax,
              and prepare for a tantalizing experience ahead.",
                enableIndexing: false
            );

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Utility::getURL() === 'cart/upload') {
            // client has sent his cart data and is requesting cart page
            $this->displayCart();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Utility::getURL() === 'cart/checkout') {
            // client has sent his cart data and wants to check out
            $this->handleCheckout();
            return;
        }

        http_response_code(400);
    }
}

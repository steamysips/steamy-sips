<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
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
            (new _404())->index();
            die();
        }
    }

    private function handleCheckout(): void
    {
        // if user is not logged in, redirect to login page
        $signed_client = $this->getSignedInClient();
        if (!$signed_client) {
            Utility::redirect('login');
        }

        $form_data = json_decode(file_get_contents('php://input'), true);

        if (empty($form_data)) {
            echo 'cart cannot be empty';
            http_response_code(400);
            return;
        }

        $store_id = filter_var($form_data['store_id'], FILTER_VALIDATE_INT);

        if (!$store_id) {
            http_response_code(400);
            return;
        }

        // create and populate new Order object
        $new_order = new Order(store_id: 1, client_id: $signed_client->getUserID());
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
        $success = $new_order->save();

        if ($success) {
            http_response_code(201);
            return;
        }

        http_response_code(400);
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

        (new _404())->index();
    }
}

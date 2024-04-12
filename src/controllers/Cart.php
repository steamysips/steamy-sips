<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Model\Product;

class Cart
{
    use Controller;

    private array $view_data = ['cart_items' => []];

    private function displayCart(): void
    {
        // loop through each cart item
        foreach ($_SESSION['cart'] as $item) {
            // fetch corresponding product based on product ID
            $product_id = filter_var($item['productID'], FILTER_VALIDATE_INT);
            $cart_item['product'] = Product::getByID($product_id);

            // ignore invalid cart items with invalid product IDs
            if (empty($cart_item['product'])) {
                continue;
            }

            $cart_item['quantity'] = filter_var($item['quantity'], FILTER_VALIDATE_INT);
            $cart_item['milkType'] = htmlspecialchars(strtolower($item['milkType']));
            $cart_item['cupSize'] = htmlspecialchars(strtolower($item['cupSize']));
            $cart_item['subtotal'] = $cart_item['quantity'] * $cart_item['product']->getPrice();

            $this->view_data['cart_items'][] = $cart_item;
        }

        $this->view('cart', $this->view_data, template_title: "Review order");
    }

    public function index(): void
    {
        // check if the latest cart data is available
        if (isset($_SESSION['cart'])) {
            $this->displayCart();

            // unset variable for next request to ensure that the latest cart is always fetched from client
            unset($_SESSION['cart']);

            return;
        }

        // check if client has sent his latest cart data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // client sent his cart data from localstorage
            // load cart to session

            // Parse json data and save it to session
            // Reference: https://stackoverflow.com/a/39508364/17627866
            $_SESSION['cart'] = json_decode(file_get_contents('php://input'), true);

            return;
        }

        // send script to browser to fetch cart from localstorage
        $script_src = ROOT . "/js/cart-uploader.js";

        $cart_script_tag = <<< EOL
            <script type="module" src="$script_src"></script>
            EOL;

        $this->view('loading', template_title: "Review order", template_tags: $cart_script_tag);
    }
}

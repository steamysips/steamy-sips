<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Model\Product;
use Steamy\Model\Store;

class Cart
{
    use Controller;

    private array $view_data = ['cart_items' => [], 'stores' => []];

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
            $cart_item['milkType'] = strtolower($item['milkType']);
            $cart_item['cupSize'] = strtolower($item['cupSize']);
            $cart_item['subtotal'] = $cart_item['quantity'] * $cart_item['product']->getPrice();

            $this->view_data['cart_items'][] = $cart_item;
        }

        $this->view_data['stores'] = Store::getAll();

        $this->view(
            'cart',
            $this->view_data,
            template_title: "Review order",
            template_meta_description: "Your ultimate shopping cart at Steamy Sips.
            Review your chosen items, adjust quantities, and proceed to checkout seamlessly.
             Savor the convenience of online shopping with us."
        );
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
        $cart_script_tag = <<< EOL
            <script src="/js/cart_uploader.bundle.js"></script>
        EOL;

        $this->view(
            'loading',
            template_title: "Review order",
            template_tags: $cart_script_tag,
            template_meta_description: "Experience anticipation as your journey begins at Steamy Sips.
             Our loading page sets the stage for your flavorful adventure. Sit back, relax,
              and prepare for a tantalizing experience ahead."
        );
    }
}

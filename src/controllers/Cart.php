<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Cart
{
    use Controller;

    private function displayOrder(): void
    {
        $this->view('cart', template_title: "Review order");
    }

    public function index(): void
    {
        // check if the latest cart data is available
        if (isset($_SESSION['cart'])) {
            // display cart
//            Utility::show($_SESSION['cart']);

            $this->displayOrder();

            // unset variable for next request to ensure that the latest cart is always fetched
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
            <script type="module" defer src="$script_src"></script>
            EOL;

        $this->view('loading', template_title: "Review order", template_tags: $cart_script_tag);
    }
}

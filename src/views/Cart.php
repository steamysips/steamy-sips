<?php

declare(strict_types=1);
/**
 * Variables below are defined in Cart controller.
 *
 * @var array $cart_items Represents an array of cart items, where each item is an object containing information
 * about a product, including its quantity, cupSize and milkType attributes.
 */
?>

<main class="container">
    <h1>Shopping Cart</h1>

    <label for="store_location">Choose store:</label>
    <select id="store_location" name="store_location">
        <option value="location1">Location 1</option>
        <option value="location2">Location 2</option>
        <option value="location3">Location 3</option>
        <!-- TODO: Add more options as needed -->
    </select>

    <?php
    if (count($cart_items) == 0) {
        echo "<p>Your cart is empty 😥</p>";
    }
    foreach ($cart_items as $item) {
        $product = $item['product'];
        $product_id = filter_var($product->getProductID(), FILTER_SANITIZE_NUMBER_INT);
        $product_name = htmlspecialchars($product->getName());
        $product_link = htmlspecialchars(ROOT . "/shop/products/" . $product_id);
        $image_url = htmlspecialchars($product->getImgAbsolutePath());
        $image_alt = htmlspecialchars($product->getImgAltText());
        $unit_price = filter_var(
            $product->getPrice(),
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );

        $cupSize = htmlspecialchars($item['cupSize']);
        $milkType = htmlspecialchars($item['milkType']);
        $quantity = filter_var($item['quantity'], FILTER_SANITIZE_NUMBER_INT);
        $subtotal = filter_var(
            $item['subtotal'],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        // Note: cupSize and milkType must be in lowercase because this information
        // is stored in lowercase in localstorage.


        // capitalize first letter of cup size and milk type when displayed on page
        $uc_cupsize = ucfirst($cupSize);
        $uc_milktype = ucfirst($milkType);

        echo <<< EOL
            <section class="cart-item" data-productid = "$product_id"
              data-quantity="$quantity" data-cupsize="$cupSize"
             data-milktype="$milkType" data-unitprice="$unit_price" >
                <img width="40" src="$image_url" alt="$image_alt">
                <div class="container">
                    <div class="" style="margin-bottom:10px">
                    <a href="$product_link"><h3 style="margin-bottom:0">$product_name</h3></a>
                        <small> $uc_cupsize | $uc_milktype | In stock</small>
                    </div>
                    <strong>Rs $subtotal</strong>
                </div>
                <label for="" class="container">
                    Quantity
                    <input min="0" max="20" value="$quantity" type="number">
                    <small>Set to 0 to remove item</small>
                </label>                
            </section>
            EOL;
    }
    ?>


    <button type="submit">Checkout</button>
</main>

<script type="module" src="<?= ROOT ?>/js/cart-view.js"></script>
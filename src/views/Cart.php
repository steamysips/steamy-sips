<?php

declare(strict_types=1);
/**
 * Variables below are defined in Login controller
 * @var array $cart_items
 */
?>

<style>
  section {
    margin-bottom: 0;
  }

  .cart-item {
    display: flex;
    gap: 1em;
    border-bottom: 1px solid gray;
  }

  section:nth-child(2) {
    border-top: 1px solid gray;
  }

  .cart-item > img {
    flex: 1;
  }

  .cart-item > .container {
    flex: 2;
  }

  .cart-item > label {
    flex: 1;
  }

</style>

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
        $product_name = $item['product']->getName();
        $image_url = $item['product']->getImgAbsolutePath();
        $image_alt = $item['product']->getImgAltText();
        $cupSize = $item['cupSize'];
        $milkType = $item['milkType'];
        $quantity = $item['quantity'];
        $subtotal = $item['subtotal'];

        echo <<< EOL
            <section class="cart-item">
                <img width="40" src="$image_url" alt="$image_alt">
                <div class="container">
                    <div class="" style="margin-bottom:10px">
                        <h3 style="margin-bottom:0px">$product_name</h3>
                        <small> $cupSize | $milkType | In stock</small>
                    </div>
                    <strong>Rs $subtotal</strong>
                </div>
                <label for="">
                    Quantity
                    <input min="1" max="20" value="$quantity" type="number" id="">
                </label>                
                
                <button style="width: auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                        <path stroke="none"
                              d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M14 4l0 4l-6 0l0 -4"/>
                    </svg>
                </button>
                
                <button style="width: auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7l16 0"/>
                        <path d="M10 11l0 6"/>
                        <path d="M14 11l0 6"/>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                    </svg>
                </button>
            </section>
            EOL;
    }
    ?>


    <button type="submit">Checkout</button>
</main>

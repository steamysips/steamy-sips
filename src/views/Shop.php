<?php
/**
 * TODO: Update me after database creation
 * @var array{
 *     name:string,
 *     password:string
 * } $products Array of all products as fetched from database
 */

?>
<main class="container">
    <form role="search">
        <label>
            <input type="search" placeholder="Search"/>
        </label>

        <div id="item-grid">
            <?php
            foreach ($products as $product) {
                $product_href = ROOT . '/shop/products/1';
                $product_img_src = ROOT . "/assets/coffee.jpg";
                echo <<<EOL
                <article data-aos="zoom-in">
                    <img src="$product_img_src" alt="">
                    <h5>$product->name</h5>
                    <p>$product->description</p>
                    <a role="button" href="$product_href" class="contrast">View</a>
                </article>
                EOL;
            }
            ?>
        </div>
    </form>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>
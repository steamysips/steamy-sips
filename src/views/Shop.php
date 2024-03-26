<?php

declare(strict_types=1);
/**
 * TODO: Update me after database creation
 * @var array{
 *     name:string,
 *     password:string
 * } $products Array of all products as fetched from database
 * @var $search_keyword string keyword used to filter products
 */

?>

<main class="container">
    <form name="search_submit" method="get" role="search">
        <label>
            <input value="<?php
            echo $search_keyword; ?>" name="keyword" type="search" placeholder="Search"/>
        </label>
    </form>
    <div id="item-grid">
        <?php
        foreach ($products as $product) {
            $product_href = ROOT . '/shop/products/' . $product->product_id;
            $product_img_src = ROOT . "/assets/img/" . $product->img_url;
            echo <<<EOL
                <article data-aos="zoom-in">
                    <img src="$product_img_src" alt="$product->img_alt_text">
                    <a href="$product_href">
                        <h5>$product->name</h5>
                    </a>
                    <p>$product->description</p>
                </article>
                EOL;
        }
        ?>
    </div>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>
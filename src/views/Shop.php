<?php

declare(strict_types=1);

/**
 * @var Product[] $products Array of all products as fetched from database
 * @var $search_keyword string keyword used to filter products
 */

use Steamy\Model\Product;

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
            $product_href = ROOT . '/shop/products/' . $product->getProductID(); // link to product page
            $product_img_src = $product->getImgAbsolutePath(); // url of image
            $img_alt_text = $product->getImgAltText();
            $description = $product->getDescription();
            $name = $product->getName();

            echo <<<EOL
                <article data-aos="zoom-in">
                    <img src="$product_img_src" alt="$img_alt_text">
                    <a href="$product_href">
                        <h5>$name</h5>
                    </a>
                    <p>$description</p>
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
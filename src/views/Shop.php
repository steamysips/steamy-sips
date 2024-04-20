<?php

declare(strict_types=1);

/**
 * View for shop page
 *
 * @var Product[] $products Array of all products as fetched from database
 * @var String[] $categories Array of all product category names
 * @var $search_keyword string keyword used to filter products
 */

use Steamy\Model\Product;

/**
 * Outputs sanitized HTML to display a product
 * @param $product
 * @return void
 */
function displayProduct($product): void
{
    $product_href = htmlspecialchars(
        ROOT . '/shop/products/' . $product->getProductID()
    ); // link to product page
    $product_img_src = htmlspecialchars($product->getImgAbsolutePath()); // url of image
    $img_alt_text = htmlspecialchars($product->getImgAltText());
    $description = htmlspecialchars($product->getDescription());
    $name = htmlspecialchars($product->getName());
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

<form method="get" class="container">
    <label>
        <input value="<?= htmlspecialchars($search_keyword) ?>" name="keyword" type="search" placeholder="Search"/>
    </label>

    <div>
        <label>
            <select name="sort">
                <option value="" selected>Sort By</option>
                <option value="newest">Newest</option>
                <option value="priceDesc">Price: High-Low</option>
                <option value="priceAsc">Price: Low-High</option>
            </select>
        </label>

        <details role="list">
            <summary aria-haspopup="listbox">Categories</summary>
            <ul role="listbox">
                <?php
                foreach ($categories as $category) {
                    // determine whether $category should be checked
                    $checked = in_array($category, $_GET['categories'] ?? []) ? "checked" : "";
                    $sanitized_category = htmlspecialchars($category);

                    echo <<< EOL
                    <li>
                        <label>
                            <input $checked value="$category" name="categories[]" type="checkbox">
                            $sanitized_category
                        </label>
                    </li>
                    EOL;
                }
                ?>
            </ul>
        </details>
    </div>

    <button type="submit">Submit</button>
</form>

<main class="container">
    <div id="item-grid">
        <?php
        foreach ($products as $product) {
            displayProduct($product);
        }
        ?>
    </div>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>
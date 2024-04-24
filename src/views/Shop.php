<?php

declare(strict_types=1);

/**
 * View for shop page
 *
 * @var Product[] $products Array of all products as fetched from database
 * @var String[] $categories Array of all product category names
 * @var string $search_keyword keyword used to filter products
 * @var string $sort_option Sort By option selected by user
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

    <div class="grid">
        <label>
            <select name="sort">
                <option value="" <?= empty($sort_option) ? "selected" : "" ?>>Sort By</option>
                <option value="newest" <?= $sort_option === "newest" ? "selected" : "" ?>>Newest</option>
                <option value="priceDesc" <?= $sort_option === "priceDesc" ? "selected" : "" ?>>Price: High-Low</option>
                <option value="priceAsc" <?= $sort_option === "priceAsc" ? "selected" : "" ?>>Price: Low-High</option>
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

        <button class="secondary" type="submit">Filter</button>
    </div>

</form>

<main class="container" style="padding-top:0">
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
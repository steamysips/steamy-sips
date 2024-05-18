<?php

declare(strict_types=1);

/**
 * View for shop page
 *
 * @var Product[] $products Array of all products as fetched from database
 * @var string[] $categories Array of all product category names
 * @var string[] $selected_categories Array of selected categories
 * @var string $search_keyword keyword used to filter products
 * @var string $sort_option Sort By option selected by user
 * @var int $current_page_number Current page number.
 * @var int $total_pages Total number of pages
 */

use Steamy\Model\Product;

/**
 * Outputs sanitized HTML to display a product
 * @param Product $product
 * @return void
 */
function displayProduct(Product $product): void
{
    $product_href = htmlspecialchars(
        '/shop/products/' . $product->getProductID()
    ); // link to product page
    $product_img_src = htmlspecialchars($product->getImgAbsolutePath()); // url of image
    $img_alt_text = htmlspecialchars($product->getImgAltText());
    $name = htmlspecialchars($product->getName());
    $price = filter_var($product->getPrice(), FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    echo <<<EOL
        <a data-aos="zoom-in" href="$product_href">
            <img src="$product_img_src" alt="$img_alt_text">
            <hgroup> 
                <h5>$name</h5>
                <h5>Rs $price</h5>
            </hgroup>
        </a>
    EOL;
}

/**
 * Returns a query string that maintains all current query string parameters and page number.
 * @param int $page_number
 * @return string Query string link for page item
 */
function getPageItemLink(int $page_number): string
{
    // create a string with all past query parameters except page and url
    unset($_GET['page']);
    unset($_GET['url']);

    $link = '?' . http_build_query($_GET);

    // add page number as query parameter
    $link .= '&page=' . $page_number;

    return $link;
}

/**
 * Prints page item in HTML format.
 *
 * @param int $current_page_number
 * @param int $page_number Page number of page item
 * @return void
 */
function displayPageItem(int $current_page_number, int $page_number): void
{
    $page_link = getPageItemLink($page_number);
    $className = "page-item" . ($page_number === $current_page_number ? " active" : "");

    echo <<< EOL
    <li class="$className">
        <a class="page-link" href="$page_link">$page_number</a>
    </li>
    EOL;
}

/**
 * Prints navigation button in HTML format
 * @param int $current_page_number
 * @param int $total_pages Total number of pages
 * @param bool $is_left True indicates left navigation button.
 * @return void
 */
function displayNavigationButton(int $current_page_number, int $total_pages, bool $is_left): void
{
    $page_link = getPageItemLink($current_page_number + ($is_left ? -1 : 1));
    $link_content = htmlspecialchars($is_left ? "<" : ">");
    $className = "page-item";

    if (($current_page_number === 1 && $is_left) || ($current_page_number === $total_pages && !$is_left)) {
        $className .= " disabled";
    }

    echo <<< EOL
    <li class="$className">
        <a class="page-link" href="$page_link">$link_content</a>
    </li>
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
                <option value="ratingDesc" <?= $sort_option === "ratingDesc" ? "selected" : "" ?>>Rating: High-Low
                </option>
                <option value="ratingAsc" <?= $sort_option === "ratingAsc" ? "selected" : "" ?>>Rating: Low-High
                </option>
            </select>
        </label>

        <details role="list">
            <summary aria-haspopup="listbox">Categories</summary>
            <ul role="listbox">
                <?php
                foreach ($categories as $category) {
                    // determine whether $category should be checked
                    $checked = in_array($category, $selected_categories) ? "checked" : "";
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

<main class="container" style="padding-top: 0">
    <div id="item-grid">
        <?php
        foreach ($products as $product) {
            displayProduct($product);
        }
        ?>
    </div>
</main>

<nav class="container" style="display: flex; justify-content: center">
    <ul class="pagination">
        <?php
        // Display previous page button
        displayNavigationButton(
            $current_page_number,
            $total_pages,
            true
        );

        // Display each page item
        for ($page_num = 1; $page_num <= $total_pages; $page_num++) {
            displayPageItem($current_page_number, $page_num);
        }

        // Display next page button
        displayNavigationButton(
            $current_page_number,
            $total_pages,
            false
        );
        ?>
    </ul>
</nav>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>

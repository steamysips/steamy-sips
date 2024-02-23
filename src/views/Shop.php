<?php
/**
 * TODO: Update me after database creation
 * @var array{
 *     name:string,
 *     password:string
 * } $products Array of all products as fetched from database
 * @var string $search_keyword keyword used to filter products (if provided)
 */

?>

<main class="container">
    <form name="search_submit" method="get" role="search">
        <label>
            <input value="<?php
            echo isset($_GET['keyword']) ? $_GET['keyword'] : ""; ?>" name="keyword" type="search" placeholder="Search"/>
        </label>
    </form>
    <div id="item-grid">
        <?php
        // Check for $search_keyword and assign a default value if not set
        $search_keyword = isset($search_keyword) ? $search_keyword : "";

        foreach ($products as $product) {
            // Use $search_keyword for filtering or display, ensuring it's not null
            if ($search_keyword !== "" && strpos($product->name, $search_keyword) === false) {
                continue; // Skip product if it doesn't match the search keyword
            }

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
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>  

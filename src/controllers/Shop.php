<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Product;
use Steamy\Controller\Product as ProductController;

/**
 * Displays all products when URL is /shop
 */
class Shop
{
    use Controller;

    private array $view_data;
    private static int $MAX_PRODUCTS_PER_PAGE = 4;

    /**
     * Check if a product matches the category filter (if any)
     * @param Product $product
     * @return bool
     */
    private function match_category(Product $product): bool
    {
        if (empty($_GET['categories'])) {
            return true;
        }

        foreach ($_GET['categories'] as $category) {
            if ($category === $product->getCategory()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if a product name matches the search keyword (if any). Matching algorithm is based on fuzzy searching.
     * @param Product $product
     * @return bool True if match found, false otherwise.
     */
    private function match_keyword(Product $product): bool
    {
        // if there are no search keyword specified by user, accept product
        if (empty($_GET['keyword'])) {
            return true;
        }

        $search_keyword = strtolower(trim($_GET['keyword']));
        $similarity_threshold = 3;

        // calculate edit distance from search keyword to product name
        $edit_distance = Utility::levenshteinDistance(
            $search_keyword,
            strtolower($product->getName())
        );

        if ($edit_distance <= $similarity_threshold) {
            return true;
        }

        // get all words in product name
        $words = explode(" ", strtolower($product->getName()));

        // for each word calculate edit distance from search keyword
        foreach ($words as $word) {
            // ignore words shorter than 4
            if (strlen($word) < 4) {
                continue;
            }

            $edit_distance = Utility::levenshteinDistance(
                $search_keyword,
                $word
            );

            if ($edit_distance <= $similarity_threshold) {
                return true;
            }
        }

        return false;
    }

    /**
     * A callback function for sorting products.
     * @param Product $a
     * @param Product $b
     * @return int An integer less than, equal to, or greater than zero if the first argument is considered to be
     * respectively less than, equal to, or greater than the second.
     */
    private function sort_product(Product $a, Product $b): int
    {
        // ignore sorting if no sort options specified
        if (empty($_GET['sort'] ?? "")) {
            return 0;
        }

        // sort by descending date
        if ($_GET['sort'] === 'newest') {
            return ($a->getCreatedDate() > $b->getCreatedDate()) ? -1 : 1;
        }

        // sort by price
        if (in_array($_GET['sort'], ['priceAsc', 'priceDesc'], true)) {
            if ($_GET['sort'] === 'priceAsc') {
                return ($a->getPrice() < $b->getPrice()) ? -1 : 1;
            }

            // sort descending
            return ($a->getPrice() < $b->getPrice()) ? 1 : -1;
        }

        // sort by rating
        if (in_array($_GET['sort'], ['ratingAsc', 'ratingDesc'], true)) {
            if ($_GET['sort'] === 'ratingAsc') {
                return ($a->getAverageRating() < $b->getAverageRating()) ? -1 : 1;
            }

            // sort descending
            return ($a->getAverageRating() < $b->getAverageRating()) ? 1 : -1;
        }

        return 0; // no sorting if invalid sorting option
    }

    /**
     * @return Product[] Array of products which match filters (excluding pagination) and sorting applied by user
     */
    public function getMatchingProducts(): array
    {
        // Fetch all products from the database
        $all_products = Product::getAll();

        // Apply filtering based on search keyword and category (existing functionality)
        $filtered_products = array_filter($all_products, array($this, "match_keyword"));
        $filtered_products = array_filter($filtered_products, array($this, "match_category"));

        // Sort the filtered products (existing functionality)
        usort($filtered_products, array($this, "sort_product"));

        return $filtered_products;
    }

    /**
     * @return int Page number on shop page. Defaults to 1.
     */
    public function getPageNumber(): int
    {
        return (int)($_GET['page'] ?? 1);
    }

    public function index(): void
    {
        // check if URL follows format /shop/products/<number>
        if (preg_match("/^shop\/products\/[0-9]+$/", Utility::getURL())) {
            // let Product controller handle this
            (new ProductController())->index();
            return;
        }

        // check if URL is not /shop
        if (Utility::getURL() !== "shop") {
            // let 404 controller handle this
            (new Error())->handlePageNotFoundError();
            return;
        }

        // get all products that match user criteria
        $filtered_products = $this->getMatchingProducts();

        // get html for pagination
        $pagination_controller = new Pagination(
            Shop::$MAX_PRODUCTS_PER_PAGE,
            count($filtered_products),
            $this->getPageNumber()
        );

        $this->view_data['pagination'] = $pagination_controller->getHTML();
        $this->view_data['products'] = $pagination_controller->getCurrentItems($filtered_products);

        // Initialize view variables
        $this->view_data['search_keyword'] = $_GET['keyword'] ?? "";
        $this->view_data['categories'] = Product::getCategories();
        $this->view_data['sort_option'] = $_GET['sort'] ?? "";
        $this->view_data['selected_categories'] = $_GET['categories'] ?? [];
        $this->view_data['current_page_number'] = $this->getPageNumber();

        // Render the view with pagination information
        $this->view(
            'Shop',
            $this->view_data,
            'Shop',
            template_tags: $this->getLibrariesTags(['aos']),
            template_meta_description: "Explore a delightful selection of aromatic coffees, teas, and delectable
             treats at Steamy Sips. Discover your perfect brew and elevate your coffee experience today."
        );
    }
}

<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Product;

/**
 * Displays all products when URL is /shop or /shop/products
 */
class Shop
{
    use Controller;

    private array $data;

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
     * Checks if a product name matches the search keyword (if any)
     * @param Product $product
     * @return bool
     */
    private function match_keyword(Product $product): bool
    {
        // if there are no search key, accept product
        if (empty($_GET['keyword'])) {
            return true;
        }
        // else accept only products within a levenshtein distance of 3
        $search_keyword = strtolower(trim($_GET['keyword']));
        $similarity_threshold = 3;
        return Utility::levenshteinDistance($search_keyword, strtolower($product->getName())) <= $similarity_threshold;
    }

    private function sort_product(Product $a, Product $b): int
    {
        // ignore sorting if no sort options specified
        if (empty($_GET['sort'])) {
            return 0;
        }

        // sort by price


        if ($a->getPrice() == $b->getPrice()) {
            return 0;
        }

        if ($_GET['sort'] == 'priceAsc') {
            return ($a->getPrice() < $b->getPrice()) ? -1 : 1;
        }

        if ($_GET['sort'] == 'priceDesc') {
            return ($a->getPrice() < $b->getPrice()) ? 1 : -1;
        }

        return 0;
    }

    public function index(): void
    {
        // check if URL follows format /shop/products/<number>
        if (preg_match("/^shop\/products\/[0-9]+/", $_GET['url'])) {
            // let Product controller handle this
            (new \Steamy\Controller\Product())->index();
            return;
        }

        // check if URL is not /shop or /shop/products
        if (!($_GET['url'] == "shop" || $_GET['url'] == "shop/products")) {
            // let 404 controller handle this
            (new _404())->index();
            return;
        }

        // fetch all products from database
        $all_products = Product::getAll();

        // filter out products which do not match keyword
        $this->data['products'] = array_filter($all_products, array($this, "match_keyword"));

        // filter out products which do not match category
        $this->data['products'] = array_filter($this->data['products'], array($this, "match_category"));


        // sort results
        usort($this->data['products'], array($this, "sort_product"));

        // initialize view variables
        $this->data['search_keyword'] = $_GET['keyword'] ?? "";
        $this->data['categories'] = Product::getCategories();

        $this->view(
            'Shop',
            $this->data,
            'Shop',
            template_tags: $this->getLibrariesTags(['aos']),
            template_meta_description: "Explore a delightful selection of aromatic coffees, teas, and delectable treats at Steamy Sips. Discover your perfect brew and elevate your coffee experience today."
        );
    }
}

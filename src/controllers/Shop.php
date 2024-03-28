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

    /**
     * Determines whether Shop controller should handle current URL and deals with invalid URLs.
     * @return bool True if Shop controller is responsible for handling URL
     */
    private function validateURL(): bool
    {
        // TODO: Move routing logic outside of controller
        $URL = Utility::splitURL();

        // check if URL follows format /shop/products/<number>
        if (sizeof($URL) == 3 && $URL[1] == 'products') {
            // let Product controller handle this URL
            (new \Steamy\Controller\Product())->index();
            return false;
        }

        // check if URL does not follow required format /shop or /shop/products
        if (!(sizeof($URL) == 2 && $URL[1] == 'products' || sizeof($URL) == 1)) {
            // display error page
            $this->view(
                '404',
                template_title: 'Error'
            );
            return false;
        }

        return true;
    }

    public function index(): void
    {
        if (!$this->validateURL()) {
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
            'Shop'
        );
    }
}

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

    private function match_keyword(Product $product): bool
    {
        $search_keyword = trim($_GET['keyword'] ?? "");

        if (strlen($search_keyword) == 0) {
            return true;
        }

        // TODO: Improve searching algorithm. Use fuzzy searching + regex perha
        return strtolower($product->getName()) == strtolower($search_keyword);
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

        // get only products which match search keyword
        $this->data['products'] = array_filter($all_products, array($this, "match_keyword"));


        // initialize view variables
        $this->data['search_keyword'] = $_GET['keyword'] ?? "";

        $this->view(
            'Shop',
            $this->data,
            'Shop'
        );
    }
}

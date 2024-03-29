<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\Review;
use Steamy\Model\User;

/**
 * Displays product page when URL follows format `/shop/products/<number>`.
 */
class Product
{
    use Controller;

    private ?\Steamy\Model\Product $product = null; // product to be displayed
    private array $view_data;
    private ?User $signed_user = null; // currently logged-in user

    public function __construct()
    {
        // initialize some view data
        $this->view_data["default_review"] = "";
        $this->view_data["default_rating"] = "";
        $this->view_data["signed_in_user"] = null;
        $this->view_data["product"] = null;

        // get product id from URL
        $product_id = filter_var(Utility::splitURL()[2], FILTER_VALIDATE_INT);

        // check if user is logged in
        $reviewer_email = $_SESSION['user'] ?? "";

        // get user details
        $user_account = Client::getByEmail($reviewer_email);
        if (!empty($user_account)) {
            $this->signed_user = $user_account;
            $this->view_data["signed_in_user"] = $user_account;
        }

        // if product id valid fetch product from db
        if ($product_id) {
            $fetched_product = \Steamy\Model\Product::getByID($product_id);

            if ($fetched_product) {
                $this->product = $fetched_product;
                $this->view_data["product"] = $this->product;
            }
        }
    }

    private function handleReviewSubmission(): void
    {
        $new_comment = trim($_POST['review_text'] ?? "");
        $rating = filter_var($_POST['review_rating'], FILTER_VALIDATE_INT);

        // unset POST values for next submission. this prevents duplicate form submission
//        unset($_POST['review_text']);
//        unset($_POST['review_rating']);

        // ignore requests from users who are not logged in
        if (empty($this->signed_user)) {
            return;
        }

        $review = new Review(
            $this->signed_user->getUserID(),
            $this->product->getProductID(),
            null,
            $new_comment,
            $rating,
            new \DateTime()
        );

        $this->view_data['errors'] = $review->validate();

        // check if review attributes are valid
        if (empty($this->view_data['errors'])) {
            // save to database
            $review->save();
        } else {
            // form values are invalid
            Utility::show($this->view_data['errors']);

            // set default values on form for submitting reviews
            $this->view_data["default_review"] = $new_comment;
            $this->view_data["default_rating"] = $rating;
        }
    }

    public function index(): void
    {
        // if product was not found, display error page
        if (empty($this->product)) {
            $this->view(
                '404',
                template_title: 'Product not found'
            );
            return;
        }

        // handle review submission
        if (isset($_POST['review_text']) || isset($_POST['review_rating'])) {
            $this->handleReviewSubmission();
        }

        $this->view(
            'Product',
            $this->view_data,
            $this->product->getName() . ' | Steamy Sips'
        );
    }
}
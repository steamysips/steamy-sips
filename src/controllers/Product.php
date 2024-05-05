<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;
use Steamy\Model\Client;
use Steamy\Model\Comment;
use Steamy\Model\Review;
use Steamy\Model\User;
use \Steamy\Model\Product as ProductModel;

/**
 * Displays product page when URL follows format `/shop/products/<number>`.
 */
class Product
{
    use Controller;

    private ?ProductModel $product = null; // product to be displayed
    private array $view_data;
    private ?User $signed_user = null; // currently logged-in user

    public function __construct()
    {
        // initialize some view data
        $this->view_data["default_review"] = "";
        $this->view_data["default_rating"] = "";
        $this->view_data["signed_in_user"] = false;
        $this->view_data["product"] = null;
        $this->view_data["rating_distribution"] = "[]";
        $this->view_data['comment_form_info'] = [];

        // get product id from URL
        $product_id = filter_var(Utility::splitURL()[2], FILTER_VALIDATE_INT);

        // check if user is logged in
        $reviewer_email = $_SESSION['user'] ?? "";

        // get user details
        $user_account = Client::getByEmail($reviewer_email);
        if (!empty($user_account)) {
            $this->signed_user = $user_account;
            $this->view_data["signed_in_user"] = true;
        }

        // if product id valid fetch product from db
        if ($product_id) {
            $fetched_product = ProductModel::getByID($product_id);

            if ($fetched_product) {
                $this->product = $fetched_product;
                $this->view_data["product"] = $this->product;
            }
        }
    }

    private function handleReviewSubmission(): void
    {
        // ignore requests from users who are not logged in
        if (empty($this->signed_user)) {
            return;
        }

        $new_comment = trim($_POST['review_text'] ?? "");
        $rating = filter_var($_POST['review_rating'] ?? -1, FILTER_VALIDATE_INT);

        $review = new Review(
            product_id: $this->product->getProductID(),
            client_id: $this->signed_user->getUserID(),
            text: $new_comment,
            rating: $rating,
        );

        $this->view_data['errors'] = $review->validate();

        // check if review attributes are valid
        if (empty($this->view_data['errors'])) {
            // save to database
            $review->save();

            $redirect_link = 'shop/products/' . $this->product->getProductID();
            $redirect_link .= '#review-' . $review->getReviewID();

            // redirect user to current page to prevent multiple submissions of the same form if user reloads
            Utility::redirect($redirect_link);
        } else {
            // form values are invalid

            // set default values on form for submitting reviews
            $this->view_data["default_review"] = $new_comment;
            $this->view_data["default_rating"] = $rating;
        }
    }

    /**
     * Converts the output of getRatingDistribution into a comma separated list
     * of numbers
     * @return string
     */
    private function formatRatingDistribution(): string
    {
        $percents = $this->product->getRatingDistribution();
        $str = "";

        for ($x = 5; $x > 0; $x--) {
            $str .= $percents[$x] ?? 0;
            if ($x != 1) {
                $str .= ',';
            }
        }
        return "[" . $str . "]";
    }

    private function handleCommentSubmission(): void
    {
        // if no user is signed in, redirect to login page
        if (!$this->signed_user) {
            Utility::redirect('login');
        }

        $new_comment = new Comment(
            user_id: $this->signed_user->getUserID(),
            review_id: filter_var($_POST['review_id'] ?? -1, FILTER_VALIDATE_INT),
            parent_comment_id: filter_var($_POST['parent_comment_id'] ?? -1, FILTER_VALIDATE_INT),
            text: trim($_POST['comment'] ?? "")
        );

        // set review_id
        if (!empty($_GET['reply_to_comment'])) {
            // replying to comment
            $parent_comment = Comment::getByID($new_comment->getParentCommentID());
            $new_comment->setReviewID($parent_comment->getReviewID());
        } else {
            // replying to review
            $new_comment->setParentCommentID(null);
        }

        $comment_errors = array_values($new_comment->validate());
        if (count($comment_errors) > 0) {
            $this->view_data['comment_form_info']['error'] = $comment_errors[0];
            return;
        }

        $success = $new_comment->save();

        if ($success) {
            // comment creation was successful

            // create link to product page
            $redirect_link = 'shop/products/' . $this->product->getProductID();

            // scroll to comment on page
            $redirect_link .= '#comment-' . $new_comment->getCommentID();

            // reloading page to remove any query parameters from the url
            Utility::redirect($redirect_link);
        } else {
            $this->view_data['comment_form_info'] ['error'] = 'An unknown error occurred. Please try again later.';
        }
    }

    private function showCommentForm(): void
    {
        if (!empty($_GET['reply_to_review'])) {
            // replying to a review => save review id in form
            $review_id = filter_var(
                $_GET['reply_to_review'],
                FILTER_VALIDATE_INT
            );
            if (!$review_id) {
                unset($this->view_data['comment_form_info']);
                return;
            }

            $this->view_data['comment_form_info'] ['review_id'] = $review_id;

            $review = Review::getByID($review_id);
            if (!$review) {
                unset($this->view_data['comment_form_info']);
                return;
            }

            $this->view_data['comment_form_info'] ['quote_text'] = $review->getText();
            $this->view_data['comment_form_info'] ['quote_author'] = User::getFullName($review->getClientID());
            $this->view_data['comment_form_info'] ['quote_date'] = $review->getCreatedDate()->format('Y');

            return;
        }


        if (!empty($_GET['reply_to_comment'])) {
            // replying to a comment => save only parent comment id in form
            // (review_id can be determined later)
            $comment_id = filter_var(
                $_GET['reply_to_comment'],
                FILTER_VALIDATE_INT
            );

            if (!$comment_id) {
                unset($this->view_data['comment_form_info']);
                return;
            }

            $this->view_data['comment_form_info'] ['parent_comment_id'] = $comment_id;

            $comment = Comment::getByID($comment_id);
            if (!$comment) {
                unset($this->view_data['comment_form_info']);
                return;
            }

            $this->view_data['comment_form_info'] ['quote_text'] = $comment->getText();
            $this->view_data['comment_form_info'] ['quote_author'] = User::getFullName($comment->getUserID());
            $this->view_data['comment_form_info'] ['quote_date'] = $comment->getCreatedDate()->format('Y');
        }
    }

    private function validateURL(): bool
    {
        return preg_match("/^shop\/products\/[0-9]+$/", Utility::getURL()) === 1;
    }

    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new _404())->index();
            die();
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();

        // if product was not found, display error page
        if (empty($this->product)) {
            (new _404())->index();
            return;
        }

        // handle review submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['review_text'])) {
            $this->handleReviewSubmission();
        }

        // determine if comment form must be displayed
        if (!empty($_GET['reply_to_comment']) || !empty($_GET['reply_to_review'])) {
            $this->showCommentForm();
        }

        // handle comment submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
            $this->handleCommentSubmission();
        }

        $this->view_data['rating_distribution'] = $this->formatRatingDistribution();
        $this->view(
            'Product',
            $this->view_data,
            $this->product->getName() . ' | Steamy Sips',
            template_tags: $this->getLibrariesTags(['chartjs']),
            template_meta_description: $this->product->getName() . " - " . $this->product->getDescription()
        );
    }
}
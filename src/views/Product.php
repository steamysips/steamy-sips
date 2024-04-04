<?php

declare(strict_types=1);

/**
 * @var $product Product product information
 * @var $signed_in_user User
 * @var $default_review string default review text in form
 * @var $default_rating int default rating in form
 * @var $rating_distribution string An array containing the percentages of ratings
 */

use Steamy\Model\Client;
use Steamy\Model\Product;
use Steamy\Model\Review;
use Steamy\Model\User;

?>

<main class="container">
    <div id="product-info" class="grid">
        <img src="<?= $product->getImgAbsolutePath() ?>" alt="<?= $product->getImgAltText() ?>">
        <div>
            <hgroup>
                <h1><?= $product->getName() ?></h1>
                <h4>Rs <?= $product->getPrice() ?></h4>
                <p>360 calories</p>
            </hgroup>
            <p>
                <?= $product->getDescription() ?>
            </p>
            <h4>Size options</h4>
            <fieldset>
                <label for="small">
                    <input type="radio" id="small" name="size" value="small" checked>
                    Small
                </label>
                <label for="medium">
                    <input type="radio" id="medium" name="size" value="medium">
                    Medium
                </label>
                <label for="large">
                    <input type="radio" id="large" name="size" value="large">
                    Large
                </label>
            </fieldset>
            <h4>Customizations</h4>
            <label for="milk">
                Milk
            </label>
            <select id="milk" required>
                <option selected>Almond</option>
                <option>Coconut</option>
                <option>Oat milk</option>
                <option>Soy</option>
            </select>
            <label for="toppings">Toppings</label>
            <select id="milk" required>
                <option selected>Topping 1</option>
                <option>Topping 2</option>
                <option>Topping 3</option>
            </select>
            <button>Add to cart</button>
        </div>
    </div>

    <h2>Customer Reviews (<?= count($product->getReviews()) ?>)</h2>
    <form class="grid" method="post">
        <label>
            <input value="<?= $default_review ?>"
                   required placeholder="Write a new review"
                   name="review_text" type="text"
                <?php
                if (isset($_POST['review_text'])) {
                    echo empty($errors['text']) ? 'aria-invalid=false' : 'aria-invalid=true';
                } ?>
            >
        </label>
        <label>
            <input value="<?= $default_rating ?>"
                   name=" review_rating" required
                   type="number" min="1" max="5"
                   placeholder="Rating"
                <?php
                if (isset($_POST['review_rating'])) {
                    echo empty($errors['rating']) ? 'aria-invalid=false' : 'aria-invalid=true';
                } ?>
            >
        </label>
        <button type="submit" <?= $signed_in_user ? "" : "disabled" ?>>Submit
        </button>
    </form>

    <div style="width: 500px;">
        <canvas id="customer_rating_chart"></canvas>
    </div>

    <label for="filter-by">Filter by</label>
    <select id="filter-by" required>
        <option selected>All reviewers</option>
        <option>Verified purchase only</option>
    </select>
    <div id="reviews">
        <ul>
            <?php

            /**
             * Returns the HTML code for the badge of a review depending on whether review is verified or not
             * @param Review $review Review
             * @return string HTML code of badge
             */
            function getBadge(Review $review): string
            {
                if (Review::isVerified($review->getProductID(), $review->getReviewID())) {
                    return <<< BADGE
                    <div data-tooltip="Verified Purchase" data-placement="left" >
                        <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon-tabler-discount-check-filled"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                         stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"
                         /><path d="M12.01 2.011a3.2 3.2 0 0 1 2.113 .797l.154 .145l.698 .698a1.2 1.2 0 0 0 .71 .341l.135 .008h1a3.2 3.2 0 0 1 3.195 3.018l.005 .182v1c0 .27 .092 .533 .258 .743l.09 .1l.697 .698a3.2 3.2 0 0 1 .147 4.382l-.145 .154l-.698 .698a1.2 1.2 0 0 0 -.341 .71l-.008 .135v1a3.2 3.2 0 0 1 -3.018 3.195l-.182 .005h-1a1.2 1.2 0 0 0 -.743 .258l-.1 .09l-.698 .697a3.2 3.2 0 0 1 -4.382 .147l-.154 -.145l-.698 -.698a1.2 1.2 0 0 0 -.71 -.341l-.135 -.008h-1a3.2 3.2 0 0 1 -3.195 -3.018l-.005 -.182v-1a1.2 1.2 0 0 0 -.258 -.743l-.09 -.1l-.697 -.698a3.2 3.2 0 0 1 -.147 -4.382l.145 -.154l.698 -.698a1.2 1.2 0 0 0 .341 -.71l.008 -.135v-1l.005 -.182a3.2 3.2 0 0 1 3.013 -3.013l.182 -.005h1a1.2 1.2 0 0 0 .743 -.258l.1 -.09l.698 -.697a3.2 3.2 0 0 1 2.269 -.944zm3.697 7.282a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.32 1.497l2 2l.094 .083a1 1 0 0 0 1.32 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                         </svg>
                    </div>
                BADGE;
                }
                return <<< BADGE
                    <div data-tooltip="This user did not buy the product" data-placement="left" >
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-alert-octagon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.802 2.165l5.575 2.389c.48 .206 .863 .589 1.07 1.07l2.388 5.574c.22 .512 .22 1.092 0 1.604l-2.389 5.575c-.206 .48 -.589 .863 -1.07 1.07l-5.574 2.388c-.512 .22 -1.092 .22 -1.604 0l-5.575 -2.389a2.036 2.036 0 0 1 -1.07 -1.07l-2.388 -5.574a2.036 2.036 0 0 1 0 -1.604l2.389 -5.575c.206 -.48 .589 -.863 1.07 -1.07l5.574 -2.388a2.036 2.036 0 0 1 1.604 0z" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                    </div>
                BADGE;
            }

            /**
             * Returns the HTML code for the rating in terms of stars
             * @param Review $review
             * @return string
             */
            function getStars(Review $review): string
            {
                $checked_stars = $review->getRating();
                $unchecked_stars = 5 - $checked_stars;
                $html = "";

                while ($checked_stars > 0) {
                    $html .= <<< EOL
                        <svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"
                          fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"
                            stroke-linejoin="round"  class="fill-star icon icon-tabler icons-tabler-outline icon-tabler-star">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                    EOL;
                    $checked_stars--;
                }


                while ($unchecked_stars > 0) {
                    $html .= <<< EOL
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"
                          fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"
                            stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-star">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                    EOL;
                    $unchecked_stars--;
                }
                return $html;
            }

            /**
             * Returns the HTML code to display a review and its children.
             * @param Review $review
             * @return void
             */
            function recurse(Review $review): void
            {
                $reply_link = ROOT . "/reply/" . "id=?";
                $date = $review->getDate()->format('d M Y');
                $text = $review->getText();
                $author = Client::getByID($review->getUserID())->getFullName();
                $verified_badge = getBadge($review);
                $rating_stars = getStars($review);


                echo <<<EOL
                <li>
                <article>
                    $verified_badge
                    $rating_stars
                   <hgroup> 
                        <h5>$author</h5>
                        <h6 class="review-date">$date</h6>
                   </hgroup>
                   
                    <p>$text</p>
                    <a data-tooltip="Reply" data-placement="right" href= "$reply_link">
                         <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-reply"
                         width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                          stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"
                           fill="none"/><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                           <path d="M11 8l-3 3l3 3" /><path d="M16 11h-8" />
                         </svg>
                    </a>

                </article>
                EOL;

                // print child comments if any
                if (isset($review->children)) {
                    foreach ($review->children as $child_comment) {
                        echo "<ul>";
                        recurse($child_comment);
                        echo "</ul>";
                    }
                }

                echo "</li>";
            }

            // print top-level comments
            $reviews = $product->getReviews();
            foreach ($reviews as $review) {
                recurse($review);
            }
            ?>
        </ul>

    </div>
</main>

<script>
  const labels = ["5 star", "4 star", "3 star", "2 star", "1 star"];
  const data = {
    labels: labels,
    datasets: [
      {
        axis: "y",
        label: "Percentage",
        data: <?= $rating_distribution?>,
        fill: true,
        backgroundColor: "rgb(255, 159, 64)",
        borderWidth: 1,
      }],
  };

  const config = {
    type: "bar",
    data,
    options: {
      indexAxis: "y",
    },
  };

  document.addEventListener("DOMContentLoaded", () => {
    new Chart(
        document.getElementById("customer_rating_chart"), config,
    );
  });

</script>
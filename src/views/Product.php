<?php

declare(strict_types=1);
/**
 * TODO: Update me after database creation
 * @var $product mixed product information
 */

?>
<main class="container">
    <div id="product-info" class="grid">
        <img src="<?= ROOT ?>/assets/img/login-milkshake.avif" alt="">
        <div>
            <hgroup>
                <h2><?= $product->name ?></h2>
                <h3>360 calories</h3>
            </hgroup>
            <p>
                <?= $product->description ?>
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
                <label for="extralarge">
                    <input type="radio" id="extralarge" name="size" value="extralarge" disabled>
                    Extra Large
                </label>
            </fieldset>
            <h4>Customizations</h4>
            <label for="milk">
                Milk
            </label>
            <select id="milk" required>
                <option selected>Almond</option>
                <option>Coconut</option>
                <option>Oatmilk</option>
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

    <h2>Customer Reviews (<?= count($product->reviews) ?>)</h2>
    <form action="" class="grid">
        <label>
            <input placeholder="Write a new review" type="text">
        </label>
        <button type="submit">Submit</button>
    </form>

    <label for="filter-by">Filter by</label>
    <select id="filter-by" required>
        <option selected>All reviewers</option>
        <option>Verified purchase only</option>
    </select>
    <div id="reviews">
        <ul>

            <?php
            function recurse($review): void
            {
                $reply_link = ROOT . "/reply/" . "id=?";
                echo <<<EOL
                <li>
                <article>
                    <div data-tooltip="Verified Purchase" data-placement="left" >
                        <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon-tabler-discount-check-filled"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                         stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"
                         /><path d="M12.01 2.011a3.2 3.2 0 0 1 2.113 .797l.154 .145l.698 .698a1.2 1.2 0 0 0 .71 .341l.135 .008h1a3.2 3.2 0 0 1 3.195 3.018l.005 .182v1c0 .27 .092 .533 .258 .743l.09 .1l.697 .698a3.2 3.2 0 0 1 .147 4.382l-.145 .154l-.698 .698a1.2 1.2 0 0 0 -.341 .71l-.008 .135v1a3.2 3.2 0 0 1 -3.018 3.195l-.182 .005h-1a1.2 1.2 0 0 0 -.743 .258l-.1 .09l-.698 .697a3.2 3.2 0 0 1 -4.382 .147l-.154 -.145l-.698 -.698a1.2 1.2 0 0 0 -.71 -.341l-.135 -.008h-1a3.2 3.2 0 0 1 -3.195 -3.018l-.005 -.182v-1a1.2 1.2 0 0 0 -.258 -.743l-.09 -.1l-.697 -.698a3.2 3.2 0 0 1 -.147 -4.382l.145 -.154l.698 -.698a1.2 1.2 0 0 0 .341 -.71l.008 -.135v-1l.005 -.182a3.2 3.2 0 0 1 3.013 -3.013l.182 -.005h1a1.2 1.2 0 0 0 .743 -.258l.1 -.09l.698 -.697a3.2 3.2 0 0 1 2.269 -.944zm3.697 7.282a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.32 1.497l2 2l.094 .083a1 1 0 0 0 1.32 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                         </svg>
                    </div>

                   <hgroup> 
                        <h5>$review->author</h5>
                        <h6 class="review-date">$review->date</h6>
                   </hgroup>
                   
                    <p>$review->text</p>
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
            foreach ($product->reviews as $review) {
                recurse($review);
            }
            ?>
        </ul>

    </div>
</main>
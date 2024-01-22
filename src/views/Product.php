<?php
/**
 * TODO: Update me after database creation
 * @var $product mixed product information
 */

?>
<main class="container">
    <div class="grid">
        <img src="<?= ROOT ?>/assets/img/login-milkshake.avif" alt="">
        <div class="">
            <hgroup>
                <h2><?= $product->name ?></h2>
                <h3>Average rating: <?= $product->avg_rating ?></h3>
            </hgroup>
            <p>
                <?= $product->description ?>
            </p>
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
    <div id="reviews">
        <ul>

            <?php
            function recurse($review)
            {
                echo <<<EOL
                <li>
                <article>
                    <h5>$review->author @ $review->date</h5>
                    <p>$review->text</p>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-reply"
                     width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                      stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"
                       fill="none"/><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                       <path d="M11 8l-3 3l3 3" /><path d="M16 11h-8" /></svg>
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
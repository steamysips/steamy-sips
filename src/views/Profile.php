<?php

declare(strict_types=1);

/**
 * The following attributes are defined in controllers/Profile.php:
 *
 * @var Client $client signed in client
 * @var Order[] $orders array of orders
 * @var bool $show_account_deletion_confirmation Whether to display a confirmation dialog for account deletion
 */

use Steamy\Model\Client;
use Steamy\Model\Order;

?>

<?php
if ($show_account_deletion_confirmation) : ?>
    <dialog open>
        <article>
            <h3>Deleting your account! </h3>
            <p>Are you sure you want to delete your account? This action is irreversible.</p>
            <footer>
                <form method="post" class="grid">
                    <button class="secondary" type="submit" name="cancel_delete">Cancel</button>
                    <button type="submit" name="confirm_delete">Confirm</button>
                </form>
            </footer>
        </article>
    </dialog>
<?php
endif; ?>

<main class="container">
    <h1>My profile</h1>

    <div class="grid">
        <button class="tablink active">Account</button>
        <button class="tablink">Orders</button>
        <button class="tablink">Settings</button>
    </div>

    <div id="Account" class="tabcontent" style="display: block;">
        <label class="grid">
            Name
            <input value="<?= htmlspecialchars($client->getFirstName() . " " . $client->getLastName()) ?>"
                   type="text"
                   disabled>
        </label>


        <label class="grid">
            Email
            <input value="<?= htmlspecialchars($client->getEmail()) ?>" type="email" disabled>
        </label>


        <label class="grid">
            Address
            <input value="<?= htmlspecialchars($client->getAddress()->getFormattedAddress()) ?>" type="text"
                   disabled>
        </label>

        <label class="grid">
            Phone
            <input value="<?= htmlspecialchars($client->getPhoneNo()) ?>" type="text" disabled>
        </label>

        <a href="/profile/edit">
            <button>Edit</button>
        </a>

    </div>


    <div id="Orders" class="tabcontent">

        <h2>Orders summary</h2>

        <figure>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Store ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>

                <?php
                foreach ($orders as $order) {
                    $date = htmlspecialchars($order->getCreatedDate()->format('Y-m-d H:i:s'));
                    $id = filter_var($order->getOrderID(), FILTER_SANITIZE_NUMBER_INT);
                    $storeid = filter_var($order->getStoreID(), FILTER_SANITIZE_NUMBER_INT);
                    $status = htmlspecialchars(ucfirst($order->getStatus()->value));
                    $totalPrice = htmlspecialchars(number_format($order->calculateTotalPrice(), 2));

                    // Determine button states
                    $cancelDisabled = $status === 'completed' ? 'disabled' : '';
                    $reorderDisabled = $status !== 'completed' ? 'disabled' : '';

                    echo <<< EOL
                    <tr>
                        <td>$id</td>
                        <td>$storeid</td>
                        <td>$date</td>
                        <td>$status</td>
                        <td>\$$totalPrice</td>
                        <td class="grid">
                            <form method="post" action="/profile/cancelOrder">
                                <input type="hidden" name="order_id" value="$id">
                                <button type="submit" $cancelDisabled>Cancel</button>
                            </form>
                            <form method="post" action="/profile/reorderOrder">
                                <input type="hidden" name="order_id" value="$id">
                                <button type="submit" $reorderDisabled>Reorder</button>
                            </form>
                    </td>
                    </tr>
                    EOL;
                }

                ?>


            </table>
        </figure>

    </div>

    <div id="Settings" class="tabcontent">
        <h2>Settings</h2>
        <div id="settings-container">
            <article class="grid">
                <hgroup>
                    <h5>Log out</h5>
                    <h6>Log out from the website. You will lose access to your profile and will have to enter your
                        login details again.</h6>
                </hgroup>
                <form>
                    <button type="submit" name="logout_submit">Log out</button>
                </form>
            </article>
            <article class="grid">
                <hgroup>
                    <h5>Theme</h5>
                    <h6>Change the theme of the website.</h6>
                </hgroup>
                <div class="grid">
                    <div>
                        <input data-theme-switcher="auto" type="radio" id="auto" name="theme"/>
                        <label for="auto">Auto</label>
                    </div>

                    <div>
                        <input data-theme-switcher="light" type="radio" id="light" name="theme"/>
                        <label for="light">Light</label>
                    </div>

                    <div>
                        <input data-theme-switcher="dark" type="radio" id="dark" name="theme"/>
                        <label for="dark">Dark</label>
                    </div>
                </div>
            </article>
            <article class="grid">
                <hgroup>
                    <h5>Delete account</h5>
                    <h6>Permanently delete your account with all its associated information. This action is
                        irreversible.</h6>
                </hgroup>
                <form>
                    <button type="submit" name="account_delete_submit">Delete</button>
                </form>

            </article>
        </div>
    </div>
</main>

<script src="/js/profile_view.bundle.js"></script>
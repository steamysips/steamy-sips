<?php

declare(strict_types=1);

/**
 * The following attributes are defined in controllers/Profile.php
 *
 * @var $client Client signed in client
 * @var $orders array array of orders
 */

use Steamy\Model\Client;

?>

<main class="container">
    <h1>My account</h1>
    <h2>Personal details</h2>
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
        <input value="<?= htmlspecialchars($client->getAddress()->getFormattedAddress()) ?>" type="text" disabled>
    </label>

    <label class="grid">
        Phone
        <input value="<?= htmlspecialchars($client->getPhoneNo()) ?>" type="text" disabled>
    </label>

    <a href="/profile/edit">
        <button>Edit</button>
    </a>

    <h2>Orders summary</h2>

    <figure>
        <table>
            <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Total cost</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php
            foreach ($orders as $order) {
                $date = htmlspecialchars($order->date);
                $id = filter_var($order->id, FILTER_SANITIZE_NUMBER_INT);
                $cost = filter_var($order->cost, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $status = htmlspecialchars($order->status);
                echo <<< EOL
                    <tr>
                        <td>$date</td>
                        <td>$id</td>
                        <td>$cost</td>
                        <td>$status</td>
                        <td class="grid">
                            <button>cancel</button>
                        </td>
                    </tr>
                EOL;
            }

            ?>


        </table>
    </figure>
    <h2>Settings</h2>
    <div id="settings-container">
        <article class="grid">
            <hgroup>
                <h5>Log out</h5>
                <h6>Log out from website. You will lose access to your profile and will have to enter your login
                    details again.</h6>
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

</main>
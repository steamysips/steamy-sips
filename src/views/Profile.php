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

<style>
  /* Style tab links */
  .tablink {
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    font-size: 17px;
    background-color: var(--secondary);
  }

  .active {
    background-color: var(--contrast);
  }

  /* Style the tab content (and add height:100% for full page content) */
  .tabcontent {
    display: none;
    padding: 20px 0;
  }
</style>

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
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php
                foreach ($orders as $order) {
                    $date = htmlspecialchars($order->date);
                    $id = filter_var($order->id, FILTER_SANITIZE_NUMBER_INT);
                    $status = htmlspecialchars($order->status);
                    echo <<< EOL
                    <tr>
                        <td>$date</td>
                        <td>$id</td>
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

<script>
  function openTab(evt, tabName) {
    console.log("New tab = " + tabName);

    // hide all tab contents
    const tabcontents = [...document.getElementsByClassName("tabcontent")];
    for (let i = 0; i < tabcontents.length; i++) {
      tabcontents[i].style.display = "none";
    }

    // remove active class from the currently active tab link
    const tablinks = document.getElementsByClassName("tablink");
    for (let i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // display content for clicked tab
    document.getElementById(tabName).style.display = "block";

    // set active class only to the clicked tab link
    evt.currentTarget.className += " active";
  }

  const tabs = ["Account", "Orders", "Settings"];

  window.addEventListener("DOMContentLoaded", () => {
    [...document.getElementsByClassName("tablink")].forEach((tablink, i) => {
          console.log(i, tablink);
          tablink.addEventListener("click", (e) => openTab(e, tabs[i]));
        },
    );
  });


</script>
<main class="container">
    <h1>Your account</h1>
    <h2>Personal details</h2>
    <form action="">
        <label class="grid">
            Name:
            <input value="john" type="text" disabled>
        </label>


        <label class="grid">
            Email:
            <input value="john@gmail.com" type="text" disabled>
        </label>


        <label class="grid">
            Address:
            <input value="Mauritius" type="text" disabled>
        </label>
        <button disabled>Edit</button>
    </form>
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
                echo <<< EOL
                    <tr>
                        <td>$order->date</td>
                        <td>$order->id</td>
                        <td>$order->cost</td>
                        <td>$order->status</td>
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
                <h6>Log out from website. You will lose access to your profile and will have to enter your login details
                    again.</h6>
            </hgroup>
            <form method="post">
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
            <form method="post">
                <button type="submit" name="account_delete_submit" disabled>Delete</button>
            </form>
        </article>
    </div>

</main>
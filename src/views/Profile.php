<main class="container">
    <h1>Your account</h1>
    <h3>Personal details</h3>
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
    </form>
    <h2>Your orders</h2>

    <table>
        <tr>
            <th>Date</th>
            <th>Order ID</th>
            <th>Total cost</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>16/01/2024</td>
            <td>432434343</td>
            <td>100.00</td>
            <td>
                Completed
            </td>
        </tr>
        <tr>
            <td>12/01/2024</td>
            <td>432434343</td>
            <td>100.00</td>
            <td>
                Completed
            </td>
        </tr>
        <tr>
            <td>11/01/2024</td>
            <td>432434343</td>
            <td>100.00</td>
            <td>
                Completed
            </td>

        </tr>
    </table>
    <h2>Preferences</h2>
    <fieldset>

        <div class="grid">
            <legend>Theme</legend>

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
        </div>
    </fieldset>


    <button>Log out</button>

</main>

<!-- theme switcher-->
<script src="<?= ROOT ?>/js/minimal-theme-switcher.js"></script>
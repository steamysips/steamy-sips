<?php
/**
 * Variables below are defined and initialized in Register controller.
 * @var string $defaultFirstName
 * @var string $defaultLastName
 * @var string $defaultPhoneNumber
 * @var string $defaultStreet
 * @var string $defaultCity
 * @var string $defaultDistrictID
 * @var string $defaultEmail
 * @var string $defaultPassword
 * @var string $defaultConfirmPassword
 */

?>
<main class="container">
    <article class="grid">
        <div>
            <hgroup>
                <h1>Sign up</h1>
                <h2>Create a new account for free</h2>
            </hgroup>
            <form method="post">

                <fieldset>
                    <legend><strong>Personal details</strong></legend>
                    <div class="grid">

                        <div class="container">
                            <label for="first_name">First Name</label>
                            <input autofocus id="first_name" type="text" name="first_name"
                                   value="<?= $defaultFirstName ?>" required/>
                            <?php
                            if (!empty($errors['name'])) : ?>
                                <small class="warning"><?php
                                    echo $errors['name'] ?></small>
                            <?php
                            endif;
                            ?>
                        </div>


                        <div class="container">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" name="last_name"
                                   value="<?= $defaultLastName ?>" required/>
                            <?php
                            if (!empty($errors['name'])) : ?>
                                <small class="warning"><?php
                                    echo $errors['name'] ?></small>
                            <?php
                            endif;
                            ?>
                        </div>

                    </div>

                    <label class="container" for="telephone">
                        Phone Number
                        <small>(eg., +230-5-123-4567)</small>
                    </label>
                    <input value="<?= $defaultPhoneNumber ?>" id="telephone" type="tel" name="phone_no"
                           pattern="\+230-5-[0-9]{3}-[0-9]{4}"
                           title="eg., +230-5-123-4567" required/>

                    <div class="grid">
                        <div class="container">
                            <label for="street">Street</label>
                            <input name="street" value="<?= $defaultStreet ?>" id="street" type="text">
                        </div>

                        <div class="container">
                            <label for="city">City</label>
                            <input name="city" value="<?= $defaultCity ?>" id="city" type="text">
                        </div>
                    </div>

                    <label for="districts">District</label>
                    <!--  TODO: generate district list dynamically-->
                    <select name="district" id="districts">
                        <option value="8" <?= $defaultDistrictID == 8 ? "selected" : "" ?>>Pamplemousses</option>
                        <option value="7" <?= $defaultDistrictID == 7 ? "selected" : "" ?>>Riviere du Rempart</option>
                        <option value="2" <?= $defaultDistrictID == 2 ? "selected" : "" ?>>Port Louis</option>
                        <option value="1" <?= $defaultDistrictID == 1 ? "selected" : "" ?>>Moka</option>
                        <option value="3" <?= $defaultDistrictID == 3 ? "selected" : "" ?>>Flacq</option>
                        <option value="9" <?= $defaultDistrictID == 9 ? "selected" : "" ?>>Plaines Wilhems</option>
                        <option value="4" <?= $defaultDistrictID == 4 ? "selected" : "" ?>>Black River</option>
                        <option value="5" <?= $defaultDistrictID == 5 ? "selected" : "" ?>>Savanne</option>
                        <option value="6" <?= $defaultDistrictID == 6 ? "selected" : "" ?>>Grand Port</option>
                    </select>


                </fieldset>

                <fieldset>
                    <legend><strong>Account information</strong></legend>
                    <label for="email">Email</label>
                    <input value="<?= $defaultEmail ?>" id="email" type="email" name="email" required/>

                    <div class="grid">
                        <div class="container">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password"
                                   aria-label="Password"
                                   value="<?= $defaultPassword ?>"
                                   required/>

                            <?php
                            if (!empty($errors['password'])) : ?>
                                <small class="warning"><?= $errors['password'] ?></small>
                            <?php
                            endif;
                            ?>
                        </div>

                        <div class="container">
                            <label for="confirmPassword">Confirm password</label>
                            <input id="confirmPassword" type="password" name="confirmPassword"
                                   aria-label="Confirm password"
                                   value="<?= $defaultConfirmPassword ?>" required/>
                            <?php
                            if (!empty($errors['confirmPassword'])) : ?>
                                <small class="warning"><?= $errors['confirmPassword'] ?></small>
                            <?php
                            endif; ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <label>
                        <input type="checkbox" role="switch" onclick="togglePasswordVisibility()">
                        Show password
                    </label>
                </fieldset>
                <button type="submit" name="register_submit">Register</button>
                <small>Already have an account? <a href="<?= ROOT ?>/login">Login</a></small>
            </form>
        </div>
    </article>
</main>

<script>
  function togglePasswordVisibility() {
    // Reference: https://www.w3schools.com/howto/howto_js_toggle_password.asp
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      confirmPasswordInput.type = "text";
    } else {
      passwordInput.type = "password";
      confirmPasswordInput.type = "password";

    }
  }
</script>
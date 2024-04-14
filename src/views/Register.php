<?php

declare(strict_types=1);

use Steamy\Model\District;

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
 * @var array $errors list of errors in form after submission
 * @var District[] $districts list of all district objects
 */


/**
 * Returns an aria-invalid attribute for an input tag after a form submission
 * @param $input_name string value in name attribute of input tag
 * @return string
 */
$ariaInvalid = function (string $input_name) use ($errors) {
    // if form is loaded for the first time (form has not been submitted)
    // do not add aria-invalid attribute
    if (!isset($_POST['register_submit'])) {
        return '';
    }

    // if there are no errors for the input tag set aria-invalid to false
    if (empty($errors[$input_name])) {
        return ' aria-invalid="false"';
    }

    return ' aria-invalid="true"'; // Submitted value is correct
};

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
                                   value="<?= $defaultFirstName ?>" required<?= $ariaInvalid('first_name') ?> />
                            <?php
                            if (!empty($errors['first_name'])) : ?>
                                <small class="warning"><?= $errors['first_name'] ?></small>
                            <?php
                            endif; ?>
                        </div>

                        <div class="container">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" name="last_name"
                                   value="<?= $defaultLastName ?>" required<?= $ariaInvalid('last_name') ?> />
                            <?php
                            if (!empty($errors['last_name'])) : ?>
                                <small class="warning"><?= $errors['last_name'] ?></small>
                            <?php
                            endif; ?>
                        </div>

                    </div>

                    <label class="container" for="telephone">
                        Phone Number
                        <small>(eg., +230-5-123-4567)</small>
                    </label>
                    <input value="<?= $defaultPhoneNumber ?>" id="telephone" type="tel" name="phone_no"
                           pattern="\+230-5-[0-9]{3}-[0-9]{4}"
                           title="eg., +230-5-123-4567" required<?= $ariaInvalid('phone_no') ?> />

                    <div class="grid">
                        <div class="container">
                            <label for="street">Street</label>
                            <input name="street" value="<?= $defaultStreet ?>" id="street" type="text"<?= $ariaInvalid(
                                'street'
                            ) ?> />
                            <?php
                            if (!empty($errors['street'])) : ?>
                                <small class="warning"><?= $errors['street'] ?></small>
                            <?php
                            endif; ?>
                        </div>

                        <div class="container">
                            <label for="city">City</label>
                            <input name="city" value="<?= $defaultCity ?>" id="city" type="text"<?= $ariaInvalid(
                                'city'
                            ) ?> />
                            <?php
                            if (!empty($errors['city'])) : ?>
                                <small class="warning"><?= $errors['city'] ?></small>
                            <?php
                            endif; ?>
                        </div>
                    </div>

                    <label for="districts">District</label>
                    <select name="district" id="districts"<?= $ariaInvalid('district') ?>>
                        <?php
                        foreach ($districts as $district) : ?>
                            <option value="<?= $district->getID() ?>" <?= $defaultDistrictID == $district->getID(
                            ) ? "selected" : "" ?>><?= $district->getName() ?></option>
                        <?php
                        endforeach; ?>
                    </select>
                    <?php
                    if (!empty($errors['district'])) : ?>
                        <small class="warning"><?= $errors['district'] ?></small>
                    <?php
                    endif; ?>


                </fieldset>

                <fieldset>
                    <legend><strong>Account information</strong></legend>
                    <label for="email">Email</label>
                    <input value="<?= $defaultEmail ?>" id="email" type="email" name="email" required<?= $ariaInvalid(
                        'email'
                    ) ?> />
                    <?php
                    if (!empty($errors['email'])) : ?>
                        <small class="warning"><?= $errors['email'] ?></small>
                    <?php
                    endif; ?>

                    <div class="grid">
                        <div class="container">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password"
                                   aria-label="Password"
                                   value="<?= $defaultPassword ?>"
                                   required<?= $ariaInvalid('password') ?> />

                            <?php
                            if (!empty($errors['password'])) : ?>
                                <small class="warning"><?= $errors['password'] ?></small>
                            <?php
                            endif; ?>
                        </div>

                        <div class="container">
                            <label for="confirmPassword">Confirm password</label>
                            <input id="confirmPassword" type="password" name="confirmPassword"
                                   aria-label="Confirm password"
                                   value="<?= $defaultConfirmPassword ?>" required<?= $ariaInvalid(
                                'confirmPassword'
                            ) ?> />
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

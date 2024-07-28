<?php

declare(strict_types=1);

/**
 * View for contact us page
 *
 * @var string $defaultFirstName Default value for first name input field
 * @var string $defaultLastName Default value for last name input field
 * @var string $defaultEmail Default value for email input field
 * @var string $defaultMessage Default value for message textarea
 * @var bool $contact_us_successful Whether contact was successful
 * @var array $errors Array of validation errors
 */

?>

<main class="container">
    <div>
        <h1>Contact Us</h1>
        <form action="" method="post">
            <label for="firstname">First name</label>
            <input type="text" id="firstname" name="first_name" value="<?= htmlspecialchars($defaultFirstName) ?>"
                   required>
            <?php
            if (isset($errors['first_name'])) : ?>
                <small class="warning"><?= $errors['first_name'] ?></small>
            <?php
            endif; ?>

            <label for="lastname">Last name</label>
            <input type="text" id="lastname" name="last_name" value="<?= htmlspecialchars($defaultLastName) ?>"
                   required>
            <?php
            if (isset($errors['last_name'])) : ?>
                <small class="warning"><?= $errors['last_name'] ?></small>
            <?php
            endif; ?>

            <label for="email">Your email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($defaultEmail) ?>" required>
            <?php
            if (isset($errors['email'])) : ?>
                <small class="warning"><?= $errors['email'] ?></small>
            <?php
            endif; ?>

            <label for="message">Your message</label>
            <textarea id="message" name="message" rows="5" cols="30" required><?= htmlspecialchars(
                    $defaultMessage
                ) ?></textarea>
            <?php
            if (isset($errors['message'])) : ?>
                <small class="warning"><?= $errors['message'] ?></small>
            <?php
            endif; ?>

            <input type="submit" name="form_submit" value="Submit">
        </form>
    </div>
</main>

<dialog <?= $contact_us_successful ? "open" : "" ?>>
    <article>
        <h3>Thank You for Contacting Us! 🔎</h3>
        <p>Your message has been successfully sent. Our team will review your inquiry and get back to you shortly. We
            appreciate your interest in our services.</p>
        <footer>
            <a href="/home" role="button" data-target="my-modal">Return to Home</a>
        </footer>
    </article>
</dialog>
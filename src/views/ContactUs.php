<?php

declare(strict_types=1);

/**
 * View for contact us page
 *
 * @var string $defaultFirstName Default value for first name input field
 * @var string $defaultLastName Default value for last name input field
 * @var string $defaultEmail Default value for email input field
 * @var string $defaultMessage Default value for message textarea
 * @var array $errors Array of validation errors
 */

?>

<main>
    <div>
        <h1>Contact Us</h1>
        <?php if (isset($_GET['success']) && $_GET['success'] === 'true') : ?>
            <p class="success-message">Your message has been sent successfully!</p>
        <?php endif; ?>
        <form action="" method="post">
            <label for="firstname">First name:</label><br>
            <input type="text" id="firstname" name="first_name" value="<?= htmlspecialchars($defaultFirstName) ?>" required>
            <?php if (isset($errors['first_name'])) : ?>
                <span class="warning"><?= $errors['first_name'] ?></span>
            <?php endif; ?><br><br>

            <label for="lastname">Last name:</label><br>
            <input type="text" id="lastname" name="last_name" value="<?= htmlspecialchars($defaultLastName) ?>" required>
            <?php if (isset($errors['last_name'])) : ?>
                <span class="warning"><?= $errors['last_name'] ?></span>
            <?php endif; ?><br><br>

            <label for="email">Email address:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($defaultEmail) ?>" required>
            <?php if (isset($errors['email'])) : ?>
                <span class="warning"><?= $errors['email'] ?></span>
            <?php endif; ?><br><br>

            <label for="message">Details:</label><br>
            <textarea id="message" name="message" rows="5" cols="30" required><?= htmlspecialchars($defaultMessage) ?></textarea>
            <?php if (isset($errors['message'])) : ?>
                <span class="warning"><?= $errors['message'] ?></span>
            <?php endif; ?><br><br>

            <input type="submit" name="form_submit" value="Submit">
        </form>
    </div>
</main>
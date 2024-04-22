<?php

declare(strict_types=1);

/**
 * @var bool $password_change_success Whether password was successfully sent
 * @var string $error
 */
?>

<main class="container">
    <h2>Enter your new password</h2>

    <form method="post" action="">
        <input type="password" name="pwd" placeholder="Enter a new password" required>
        <input type="password" name="pwd-repeat" placeholder="Repeat new password" required>
        <small style="color:red;"><?= $error ?? "" ?></small>
        <button style="width: 30%" type="submit">Change password</button>
    </form>
</main>

<dialog <?= $password_change_success ? "open" : "" ?>>
    <article>
        <h3>Password changed! 🎉</h3>
        <p>Your password has been successfully changed</p>
        <footer>
            <a href="<?= ROOT ?>/login"
               role="button"
               data-target="my-modal"
            >
                Return to sign in
            </a>
        </footer>
    </article>
</dialog>
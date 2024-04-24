<?php

declare(strict_types=1);

/**
 * @var bool $email_submit_success Whether email was successfully sent
 * @var string $error
 */
?>

<main class="container">
    <h2>Reset Password</h2>
    <p>Just need to confirm your email to send you instructions to reset your password.</p>
    <form id="email-submission-form" method="post" action="">
        <label>
            <input type="email" required name="email" placeholder="Email">
            <small style="color:red;"><?= $error ?? "" ?></small>
        </label>
        <button style="width: 30%" type="submit">Reset password</button>
    </form>
</main>

<dialog <?= $email_submit_success ? "open" : "" ?>>
    <article>
        <h3>Email submitted! 🎉</h3>
        <p>Thanks - if you have a Steamy Sips account, we've sent you an email.</p>
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
<?php

declare(strict_types=1);
/**
 * View variables defined in Login controller:
 *
 * @var string $defaultEmail Email set in form
 */
?>

<main class="container">
    <article id="login-form" class="grid" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="500">
        <div>
            <h1>Sign in</h1>
            <form method="post">
                <input autofocus type="email" name="email" placeholder="Email" aria-label="Email"
                       aria-invalid="<?= isset($_POST['login_submit']) ? 'true' : '' ?>"
                       value="<?= htmlspecialchars($defaultEmail) ?>" required/>

                <input type="password" name="password" placeholder="Password" aria-label="Password"
                       aria-invalid="<?= isset($_POST['login_submit']) ? 'true' : '' ?>"
                       required/>

                <button name="login_submit" type="submit" class="contrast">Login</button>
                <small class="grid">
                    <a href="<?= ROOT ?>/register">Register</a>
                    <a style="display: flex; justify-content: flex-end" href="<?= ROOT ?>/password">Forgot Password</a>
                </small>
            </form>
        </div>
        <div></div>
    </article>
</main>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    AOS.init();
  });
</script>
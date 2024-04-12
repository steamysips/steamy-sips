<?php

declare(strict_types=1);
/**
 * Variables below are defined in Login controller
 * @var string $defaultEmail
 * @var string $defaultPassword
 * @var array $errors Error if any is available in $errors['other']
 */
?>

<main class="container">
    <article id="login-form" class="grid" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="500">
        <div>
            <h1>Sign in</h1>
            <form method="post">
                <input autofocus type="email" name="email" placeholder="Email" aria-label="Email"
                       aria-invalid="<?= isset($_POST['login_submit']) && !empty($errors['other']) ? 'true' : '' ?>"
                       value="<?= $defaultEmail ?>" required/>

                <input type="password" name="password" placeholder="Password" aria-label="Password"
                       aria-invalid="<?= isset($_POST['login_submit']) && !empty($errors['other']) ? 'true' : '' ?>"
                       value="<?= $defaultPassword ?>" required/>

                <button name="login_submit" type="submit" class="contrast">Login</button>
                <small>Don't have an account yet? <a href="<?= ROOT ?>/register">Register</a></small>
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
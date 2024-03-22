<?php
/**
 * Variables below are defined in Login controller
 * @var string $defaultEmail
 * @var string $defaultPassword
 */

?>

<main class="container">
    <article id="login-form" class="grid" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="500">
        <div>
            <h1>Sign in</h1>
            <form method="post">
                <input autofocus type="email" name="email" placeholder="Email" aria-label="Email"
                       value="<?= $defaultEmail ?>" required/>

                <input type="password" name="password" placeholder="Password" aria-label="Password"
                       value="<?= $defaultPassword ?>" required/>

                <fieldset>
                    <label for="remember">
                        <input type="checkbox" role="switch" id="remember" name="remember"/>
                        Remember me
                    </label>
                </fieldset>
                <button name="login_submit" type="submit" class="contrast">Login</button>
                <?php
                if (!empty($errors['other'])) : ?>
                    <small class="warning"><?php
                        echo $errors['other'] ?></small>
                <?php
                endif; ?>
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
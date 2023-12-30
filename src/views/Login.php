<?php
/**
 * Variables below are defined in controllers/Login.php.
 * @var string $defaultName Default name in registration form
 * @var string $defaultPassword Default password in registration form
 */
?>

<main class="container">
    <article class="grid" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="500">
        <div>
            <h1>Sign in</h1>
            <form method="post">
                <input type="text" name="name" placeholder="Name" aria-label="Name" autocomplete="nickname"
                       value="<?php echo $defaultName; ?>" required/>

                <?php if (!empty($errors['name'])) : ?>
                    <small class="warning"><?php echo $errors['name'] ?></small>
                <?php endif; ?>


                <input type="password" name="password" placeholder="Password" aria-label="Password"
                       autocomplete="current-password" value="<?php echo $defaultPassword; ?>" required/>

                <?php if (!empty($errors['password'])) : ?>
                    <small class="warning"><?php echo $errors['password'] ?></small>
                <?php endif; ?>

                <fieldset>
                    <label for="remember">
                        <input type="checkbox" role="switch" id="remember" name="remember"/>
                        Remember me
                    </label>
                </fieldset>
                <button name="login_submit" type="submit" class="contrast">Login</button>
                <?php if (!empty($errors['other'])) : ?>
                    <small class="warning"><?php echo $errors['other'] ?></small>
                <?php endif; ?>

                <div><small>Don't have an account yet? <a href="<?= ROOT ?>/register">Register</a></small></div>
            </form>
        </div>
        <div></div>
    </article>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init();
    });
</script>
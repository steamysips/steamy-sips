<?php
/**
 * Variables below are defined in controller.
 * @var string $defaultName Default name in registration form
 * @var string $defaultPassword Default password in registration form
 * @var string $defaultConfirmPassword Default password in registration form
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
                <label for="name">Name</label>
                <input id="name" type="text" name="name" aria-label="Name" autocomplete="nickname"
                       value="<?php
                       echo $defaultName; ?>" required/>
                <?php
                if (!empty($errors['name'])) : ?>
                    <small class="warning"><?php
                        echo $errors['name'] ?></small>
                <?php
                endif; ?>


                <div class="grid">
                    <div class="container">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" aria-label="Password"
                               value="<?php
                               echo $defaultPassword; ?>" autocomplete="current-password" required/>
                        <?php
                        if (!empty($errors['password'])) : ?>
                            <small class="warning"><?php
                                echo $errors['password'] ?></small>
                        <?php
                        endif; ?>
                    </div>
                    <div class="container">
                        <label for="confirmPassword">Confirm password</label>
                        <input id="confirmPassword" type="password" name="confirmPassword" aria-label="Confirm password"
                               value="<?php
                               echo $defaultConfirmPassword; ?>" required/>
                        <?php
                        if (!empty($errors['confirmPassword'])) : ?>
                            <small class="warning"><?php
                                echo $errors['confirmPassword'] ?></small>
                        <?php
                        endif; ?>
                    </div>
                </div>

                <button type="submit" name="register_submit">Register</button>
                <small>Already have an account? <a href="<?= ROOT ?>/login">Login</a></small>
            </form>
        </div>
    </article>
</main>
<main class="container">
    <article class="grid">
        <div>
            <hgroup>
                <h1>Sign up</h1>
                <h2>Create a new account for free</h2>
            </hgroup>
            <form method="post">
                <label for="name">Name</label>
                <input type="text" name="name" aria-label="Name" autocomplete="nickname" value="<?php echo $defaultName; ?>" required />

                <?php if (!empty($errors['name'])) : ?>
                    <small class="warning"><?php echo $errors['name'] ?></small>
                <?php endif; ?>

                <div class="grid">
                    <div class="container">
                        <label for="password">Password</label>
                        <input type="password" name="password" aria-label="Password" value="<?php echo $defaultPassword; ?>" autocomplete="current-password" required />
                        <?php if (!empty($errors['password'])) : ?>
                            <small class="warning"><?php echo $errors['password'] ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="container">
                        <label for="confirmPassword">Confirm password</label>
                        <input type="password" name="confirmPassword" aria-label="Confirm password" value="<?php echo $defaultPassword; ?>" autocomplete="current-password" required />
                        <?php if (!empty($errors['password'])) : ?>
                            <small class="warning"><?php echo $errors['confirmPassword'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" name="register_submit">Register</button>
            </form>
        </div>
    </article>
</main>
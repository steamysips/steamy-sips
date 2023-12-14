<h1>Register</h1>

<form name="registration-form" method="POST">
    <label for="nameInput">Name</label>
    <input type="text" name="name" id="nameInput" aria-invalid="<?php echo $errors['name'] > 0 ? "true" : "false" ?>">
    <label for="passwordInput" >Password</label>
    <input type="password" name="password" id="passwordInput" aria-invalid="<?php echo $errors['password'] > 0 ? "true" : "false" ?>">
    <button type="submit">Submit</button>

    <a href="<?= ROOT ?>/">
        <p>go home</p>
    </a>
    <?php
    echo "<h3>errors</h3>";
    show($errors);
    // echo implode("<br>", $errors);
    ?>
</form>
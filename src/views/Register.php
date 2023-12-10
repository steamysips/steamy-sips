<?php
require '../controllers/RegistrationController.php';
?>

<h1>Register 📝</h1>

<form action="../controllers/RegisterController.php" method="POST">
    <label for="nameInput">Name</label>
    <input type="text" name="name" id="nameInput">
    <label for="passwordInput">Password</label>
    <input type="password" name="password" id="passwordInput">
    <button type="submit">Submit</button>
</form>
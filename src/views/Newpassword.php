<?php

declare(strict_types=1);

?>

    <h1 class="header">Enter New Password</h1>

    <form method="post" action="">
        <input type="hidden" name="type" value="reset" />
        
        <input type="password" name="pwd" placeholder="Enter a new password...">
        <input type="password" name="pwd-repeat" placeholder="Repeat new password...">
        <button type="submit" name="submit">Reset Password</button>
    </form>
    
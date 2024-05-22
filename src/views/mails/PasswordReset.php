<?php

declare(strict_types=1);

/**
 * This file contains the template for the password reset email.
 * It will be used by the sendResetEmail method in the Password controller.
 *
 * @var string $resetLink The link to reset the password.
 * @var string $userEmail The email of the user receiving the reset link.
 */

?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .header img {
            max-width: 100px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            font-size: 0.9em;
            color: #999;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="cid:logo" alt="Steamy Sips Logo">
        </div>
        <div class="content">
            <h1>Reset Your Password</h1>
            <p>Hello,</p>
            <p>We received a request to reset the password for the account associated with <?= htmlspecialchars($userEmail) ?>. Click the button below to reset your password:</p>
            <p><a href="<?= htmlspecialchars($resetLink) ?>" class="button">Reset My Password</a></p>
            <p>If you did not request a password reset, please ignore this email. This link will expire in 24 hours.</p>
            <p>Thank you,<br>The Steamy Sips Team</p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Steamy Sips. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

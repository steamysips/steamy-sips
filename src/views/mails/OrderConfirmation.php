<?php

declare(strict_types=1);

/**
 * Email template for order confirmation.
 * The template uses only inline CSS because emails have inconsistent CSS support.
 *
 * It should have access to the following variables:
 *
 * @var Order $order order order
 * @var Client $client First name of person who placed the order
 *
 * Ref: https://github.com/leemunroe/responsive-html-email-template
 */


use Steamy\Model\Client;
use Steamy\Model\Order;

?>

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Order Confirmation</title>
</head>

<body>
<h1>Order Confirmation</h1>

<p>Dear <?= htmlspecialchars($client->getFirstName() . " " . $client->getLastName()) ?>,</p>

<p>We've received your order successfully. You can find your purchase information below.</p>

<h2>Order summary</h2>

<h3><?= $order->getCreatedDate() ?></h3>

<table>
    <thead>
    <tr>
        <th style="padding:10px; outline: 1px solid;">Product</th>
        <th style="padding:10px; outline: 1px solid;">Quantity</th>
        <th style="padding:10px; outline: 1px solid;">Unit price (Rs)</th>
        <th style="padding:10px; outline: 1px solid;">Subtotal (Rs)</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $orderProducts = $order->getLineItems();
    $total = 0;
    foreach ($orderProducts as $orderProduct) {
        $productName = htmlspecialchars($orderProduct->getProductName());
        $quantity = $orderProduct->getQuantity();
        $pricePerUnit = $orderProduct->getUnitPrice();
        $subtotal = $pricePerUnit * $quantity;
        $total += $subtotal;
        echo <<< HTML
            <tr>
                <td style="padding:10px; outline: 1px solid;">$productName</td>
                <td style="padding:10px; outline: 1px solid;">$quantity</td>
                <td style="padding:10px; outline: 1px solid;">$pricePerUnit</td>
                <td style="padding:10px; outline: 1px solid;">$subtotal</td>
            </tr>
        HTML;
    }
    ?>
    <tr>
        <td style="padding:10px; outline: 1px solid;" colspan='3'><b>Total</b></td>
        <td style="padding:10px; outline: 1px solid;"><?= $total ?></td>
    </tr>
    </tbody>
</table>

</body>
</html>

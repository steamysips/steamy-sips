<?php

declare(strict_types=1);

/**
 * Email template for order confirmation.
 * The template uses only inline CSS because emails have inconsistent CSS support.
 *
 * It should have access to the following variables:
 *
 * @var Order $order order order
 * @var Store $store Store
 * @var string $client_full_name Full name of person who placed the order
 *
 * Ref: https://github.com/leemunroe/responsive-html-email-template
 */


use Steamy\Model\Client;
use Steamy\Model\Order;
use Steamy\Model\Store;

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

<p>Dear <?= htmlspecialchars($client_full_name) ?>,</p>

<p>Thank you for your purchase at Steamy Sips! We've received your order successfully. You can find your purchase
    information
    below.</p>

<h2>Order summary</h2>

<p>Order ID: <?= $order->getOrderID() ?></p>
<p>Order Date: <?= $order->getCreatedDate()->format('Y-m-d H:i') ?></p>
<p>Store address: <?= $store->getAddress()->getFormattedAddress() ?></p>

<table>
    <thead>
    <tr style='background-color:#EDF0F3;'>
        <th style="padding:10px; outline: 1px solid;">Product</th>
        <th style="padding:10px; outline: 1px solid;">Unit price (Rs)</th>
        <th style="padding:10px; outline: 1px solid;">Size</th>
        <th style="padding:10px; outline: 1px solid;">Milk</th>
        <th style="padding:10px; outline: 1px solid;">Quantity</th>
        <th style="padding:10px; outline: 1px solid;">Subtotal (Rs)</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $orderProducts = $order->getLineItems();
    $total = 0;
    foreach ($orderProducts as $orderProduct) {
        $name = htmlspecialchars($orderProduct->getProductName());
        $quantity = $orderProduct->getQuantity();
        $pricePerUnit = $orderProduct->getUnitPrice();
        $subtotal = $pricePerUnit * $quantity;
        $size = htmlspecialchars(ucfirst($orderProduct->getCupSize()));
        $total += $subtotal;
        $milk = htmlspecialchars(
            ucfirst($orderProduct->getMilkType())
        );

        echo <<< HTML
            <tr>
                <td style="padding:10px; outline: 1px solid;">$name</td>
                <td style="padding:10px; outline: 1px solid;">$pricePerUnit</td>
                <td style="padding:10px; outline: 1px solid;">$size</td>
                <td style="padding:10px; outline: 1px solid;">$milk</td>
                <td style="padding:10px; outline: 1px solid;">$quantity</td>
                <td style="padding:10px; outline: 1px solid;">$subtotal</td>
            </tr>
        HTML;
    }
    ?>
    <tr style='background-color:#EDF0F3;'>
        <td style="padding:10px; outline: 1px solid;" colspan='5'><b>Total</b></td>
        <td style="padding:10px; outline: 1px solid;"><?= $total ?></td>
    </tr>
    </tbody>
</table>

<p>Your order is now being processed and you will receive a notification once your order is ready. If you have any
    questions, feel free to call our store at <?= $store->getPhoneNo() ?>.</p>

<p>Best Regards,</p>
<p>Steamy Sips</p>
</body>
</html>

<?php

declare(strict_types=1);

/**
 * @var Order $order Current order
 * @var OrderProduct[] $line_items Line items for current order
 */

use Steamy\Model\Order;
use Steamy\Model\OrderProduct;

?>

<main class="container">
    <h1>Order #<?= filter_var($order->getOrderID(), FILTER_SANITIZE_NUMBER_INT); ?></h1>
    <section>
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?= filter_var($order->getOrderID(), FILTER_SANITIZE_NUMBER_INT); ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order->getCreatedDate()->format('Y-m-d H:i:s')) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($order->getStatus()->value)) ?></p>
        <p><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($order->calculateTotalPrice(), 2)) ?></p>
    </section>

    <section>
        <h2>Order Items</h2>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Milk Type</th>
                <th>Cup Size</th>
                <th>Unit Price</th>
            </tr>
            <?php
            foreach ($line_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->getProductName()) ?></td>
                    <td><?= filter_var($item->getQuantity(), FILTER_SANITIZE_NUMBER_INT) ?></td>
                    <td><?= htmlspecialchars($item->getMilkType()) ?></td>
                    <td><?= htmlspecialchars($item->getCupSize()) ?></td>
                    <td>$<?= (number_format($item->getUnitPrice(), 2)) ?></td>
                </tr>
            <?php
            endforeach; ?>
        </table>
    </section>
</main>

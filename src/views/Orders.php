<?php

declare(strict_types=1);

/**
 * @var Order $orders 
 * @var OrderProduct $orderproduct 
 */

use Steamy\Model\Order;
use Steamy\Model\OrderProduct;
?>

<main class="container">
    <h1>Order #<?= filter_var($orders->getOrderID(), FILTER_SANITIZE_NUMBER_INT); ?></h1>
    <section>
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?= filter_var($orders->getOrderID(), FILTER_SANITIZE_NUMBER_INT); ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($orders->getCreatedDate()->format('Y-m-d H:i:s')) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($orders->getStatus()->value)) ?></p>
        <p><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($orders->calculateTotalPrice(), 2)) ?></p>
    </section>
    
    <section>
        <h2>Order Items</h2>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Milk Type</th>
                <th>Cup Size</th>
                <th>Price</th>
            </tr>
            <?php
            foreach ($orderproduct as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->getProductName()) ?></td>
                    <td><?= htmlspecialchars($item->getQuantity()) ?></td>
                    <td><?= htmlspecialchars($item->getMilkType()) ?></td>
                    <td><?= htmlspecialchars($item->getCupSize()) ?></td>
                    <td>$<?= htmlspecialchars(number_format($item->getPrice(), 2)) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>

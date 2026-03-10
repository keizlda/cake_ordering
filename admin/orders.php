<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$stmt = $pdo->query("
    SELECT o.*, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>All Orders</h2>

<?php if (!$orders): ?>
    <div class="alert alert-info">No orders found.</div>
<?php else: ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Delivery</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['full_name']) ?></td>
            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
            <td><?= htmlspecialchars($order['order_status']) ?></td>
            <td><?= htmlspecialchars($order['payment_status']) ?></td>
            <td><?= htmlspecialchars($order['delivery_method']) ?></td>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
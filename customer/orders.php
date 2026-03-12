<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE user_id = ?
    ORDER BY order_id DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>My Orders</h2>

<?php if (!$orders): ?>
    <div class="alert alert-info">No orders found.</div>
<?php else: ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Order Status</th>
            <th>Payment Status</th>
            <th>Payment Method</th>
            <th>Reference Number</th>
            <th>Delivery Method</th>
            <th>Delivery Address</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
            <td><?= htmlspecialchars($order['order_status']) ?></td>
            <td><?= htmlspecialchars($order['payment_status']) ?></td>
            <td><?= htmlspecialchars($order['payment_method']) ?></td>
            <td><?= htmlspecialchars($order['reference_number']) ?></td>
            <td><?= htmlspecialchars($order['delivery_method']) ?></td>
            <td><?= htmlspecialchars($order['delivery_address']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
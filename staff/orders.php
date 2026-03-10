<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('staff');

$stmt = $pdo->query("
    SELECT o.*, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>Staff - Orders</h2>

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
            <th>Update Status</th>
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
            <td>
                <form method="POST" action="update_order_status.php" class="d-flex gap-2">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <select name="order_status" class="form-control" required>
                        <option value="Pending" <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Confirmed" <?= $order['order_status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="In Preparation" <?= $order['order_status'] === 'In Preparation' ? 'selected' : '' ?>>In Preparation</option>
                        <option value="Ready" <?= $order['order_status'] === 'Ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="Completed" <?= $order['order_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
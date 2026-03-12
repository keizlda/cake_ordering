<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

if (!isset($_GET['order_id'])) {
    die("Order ID not found.");
}

$order_id = (int) $_GET['order_id'];

$stmt = $pdo->prepare("
    SELECT o.*, u.full_name, u.email, u.contact_number
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

$stmt = $pdo->prepare("
    SELECT od.*, 
           p.product_name,
           d.design_name,
           s.size_name,
           f.flavor_name,
           fi.filling_name,
           t.topper_name
    FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    JOIN cake_designs d ON od.design_id = d.design_id
    JOIN cake_sizes s ON od.size_id = s.size_id
    JOIN cake_flavors f ON od.flavor_id = f.flavor_id
    JOIN cake_fillings fi ON od.filling_id = fi.filling_id
    LEFT JOIN cake_toppers t ON od.topper_id = t.topper_id
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>Admin - Order Details</h2>

<div class="card mb-4">
    <div class="card-body">
        <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($order['contact_number']) ?></p>
        <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
        <p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
        <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>Reference Number:</strong> <?= htmlspecialchars($order['reference_number']) ?></p>
        <p><strong>Delivery Method:</strong> <?= htmlspecialchars($order['delivery_method']) ?></p>
        <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
        <p><strong>Remarks:</strong> <?= htmlspecialchars($order['remarks']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
    </div>
</div>

<h4>Ordered Items</h4>

<?php if (!$orderItems): ?>
    <div class="alert alert-info">No order items found.</div>
<?php else: ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Design</th>
            <th>Size</th>
            <th>Flavor</th>
            <th>Filling</th>
            <th>Topper</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= htmlspecialchars($item['design_name']) ?></td>
            <td><?= htmlspecialchars($item['size_name']) ?></td>
            <td><?= htmlspecialchars($item['flavor_name']) ?></td>
            <td><?= htmlspecialchars($item['filling_name']) ?></td>
            <td><?= htmlspecialchars($item['topper_name'] ?? 'None') ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['unit_price'], 2) ?></td>
            <td>₱<?= number_format($item['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<a href="orders.php" class="btn btn-secondary">Back to Orders</a>

<?php include '../includes/footer.php'; ?>
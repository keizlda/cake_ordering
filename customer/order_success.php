<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

if (!isset($_GET['order_id'])) {
    die("Order not found.");
}

$order_id = (int) $_GET['order_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE order_id = ? AND user_id = ?
    LIMIT 1
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | Sugar Delights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/customer/dashboard.php">
            <span class="brand-text">Sugar Delights</span>
        </a>
    </div>
</nav>

<div class="order-success-wrapper py-5">
    <div class="container">
        <div class="order-success-card text-center">
            <div class="success-icon">💗</div>
            <h1>Order placed successfully!</h1>
            <p>Your sweet order has been received.</p>

            <div class="success-order-info">
                <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
                <p><strong>Total:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                <p><strong>Reference Number:</strong> <?= htmlspecialchars($order['reference_number']) ?></p>
                <p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
            </div>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                <a href="/cake_ordering/customer/orders.php" class="btn btn-menu-add">View My Orders</a>
                <a href="/cake_ordering/customer/dashboard.php" class="btn btn-menu-secondary">Back to Menu</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
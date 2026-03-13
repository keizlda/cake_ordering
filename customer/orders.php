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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Sugar Delights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/customer/dashboard.php">
            <span class="brand-text">Sugar Delights</span>
        </a>

        <div class="ms-auto d-flex gap-2">
            <a href="/cake_ordering/customer/dashboard.php" class="btn btn-nav-dark">Menu</a>
            <a href="/cake_ordering/customer/cart.php" class="btn btn-nav-light">Cart</a>
        </div>
    </div>
</nav>

<div class="orders-page-wrapper py-5">
    <div class="container">
        <h2 class="section-heading mb-4">My Orders</h2>

        <?php if (!$orders): ?>
            <div class="empty-cart-card text-center p-5">
                <h4>No orders found</h4>
                <p>You haven’t placed any orders yet.</p>
                <a href="/cake_ordering/customer/dashboard.php" class="btn btn-menu-add mt-3">Browse Cakes</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-card-top">
                            <div>
                                <h4>Order #<?= $order['order_id'] ?></h4>
                                <p class="order-date"><?= htmlspecialchars($order['order_date']) ?></p>
                            </div>
                            <div class="order-status-badge">
                                <?= htmlspecialchars($order['order_status']) ?>
                            </div>
                        </div>

                        <div class="order-card-body">
                            <div class="order-meta-grid">
                                <div><strong>Total:</strong> ₱<?= number_format($order['total_amount'], 2) ?></div>
                                <div><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></div>
                                <div><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></div>
                                <div><strong>Reference Number:</strong> <?= htmlspecialchars($order['reference_number']) ?></div>
                                <div><strong>Delivery Method:</strong> <?= htmlspecialchars($order['delivery_method']) ?></div>
                                <div><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></div>
                            </div>

                            <?php if (!empty($order['remarks'])): ?>
                                <div class="order-remarks mt-3">
                                    <strong>Remarks:</strong> <?= htmlspecialchars($order['remarks']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
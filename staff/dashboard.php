<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('staff');

$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$confirmedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Confirmed'")->fetchColumn();
$preparingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'In Preparation'")->fetchColumn();
$readyOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Ready'")->fetchColumn();
$completedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Completed'")->fetchColumn();

$stmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.order_status, o.total_amount, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
    LIMIT 5
");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard | Sugar Delights</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>

<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <span class="brand-text">Sugar Delights</span>
        </a>

        <div class="ms-auto d-flex gap-2 align-items-center">
            <span class="customer-nav-name">Staff: <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="/cake_ordering/staff/orders.php" class="btn btn-nav-dark">Manage Orders</a>
            <a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">Logout</a>
        </div>
    </div>
</nav>

<div class="staff-page-wrapper py-5">
<div class="container">

    <!-- HERO -->
    <div class="staff-hero-card mb-4">
        <div>
            <div class="hero-badge">STAFF PANEL</div>
            <h1>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
            <p>Monitor cake orders and update their status efficiently.</p>
        </div>

        <div class="staff-hero-actions">
            <a href="/cake_ordering/staff/orders.php" class="btn btn-menu-add">Go to Orders</a>
        </div>
    </div>

    <!-- STATS -->
    <div class="row g-4 mb-4">

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>Total Orders</h5>
                <div class="staff-stat-value"><?= $totalOrders ?></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>Pending Orders</h5>
                <div class="staff-stat-value"><?= $pendingOrders ?></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>Confirmed</h5>
                <div class="staff-stat-value"><?= $confirmedOrders ?></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>In Preparation</h5>
                <div class="staff-stat-value"><?= $preparingOrders ?></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>Ready Orders</h5>
                <div class="staff-stat-value"><?= $readyOrders ?></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="staff-stat-card">
                <h5>Completed</h5>
                <div class="staff-stat-value"><?= $completedOrders ?></div>
            </div>
        </div>

    </div>

    <!-- RECENT ORDERS (FULL WIDTH NOW) -->
    <div class="staff-main-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3 class="mb-0">Recent Orders</h3>
            <a href="/cake_ordering/staff/orders.php" class="btn btn-menu-secondary">View All Orders</a>
        </div>

        <?php if (!$recentOrders): ?>
            <div class="alert alert-info">No recent orders found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle staff-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td><?= htmlspecialchars($order['order_date']) ?></td>
                                <td>
                                    <span class="staff-status-badge">
                                        <?= htmlspecialchars($order['order_status']) ?>
                                    </span>
                                </td>
                                <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
</div>

</body>
</html>
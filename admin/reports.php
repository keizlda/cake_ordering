<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

/* SALES SUMMARY */
$totalSales = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE payment_status = 'Paid'
      AND reference_number IS NOT NULL
      AND reference_number != ''
")->fetchColumn();

$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$paidOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE payment_status = 'Paid'
      AND reference_number IS NOT NULL
      AND reference_number != ''
")->fetchColumn();

$pendingOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE order_status = 'Pending'
")->fetchColumn();

$completedOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE order_status = 'Completed'
")->fetchColumn();

/* RECENT PAID ORDERS */
$stmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.total_amount, o.payment_method, o.reference_number, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.payment_status = 'Paid'
      AND o.reference_number IS NOT NULL
      AND o.reference_number != ''
    ORDER BY o.order_id DESC
    LIMIT 10
");
$recentPaidOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Sugar Delights</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/admin/dashboard.php">
         
            <span class="brand-text">Sugar Delights</span>
        </a>

        <div class="ms-auto d-flex gap-2 align-items-center">
            <span class="customer-nav-name">Admin: <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="/cake_ordering/admin/dashboard.php" class="btn btn-nav-dark">Dashboard</a>
            <a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">Logout</a>
        </div>
    </div>
</nav>

<div class="reports-page-wrapper py-5">
    <div class="container">

        <div class="staff-orders-header-card mb-4">
            <div class="hero-badge">ADMIN REPORTS</div>
            <h1>Sales and Order Reports</h1>
            <p>Track paid orders, total sales, and recent customer transactions.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Total Sales</h5>
                    <div class="admin-stat-value">₱<?= number_format($totalSales, 2) ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Total Orders</h5>
                    <div class="admin-stat-value"><?= $totalOrders ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Paid Orders</h5>
                    <div class="admin-stat-value"><?= $paidOrders ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Pending Orders</h5>
                    <div class="admin-stat-value"><?= $pendingOrders ?></div>
                </div>
            </div>

            <div class="col-md-12 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Completed Orders</h5>
                    <div class="admin-stat-value"><?= $completedOrders ?></div>
                </div>
            </div>
        </div>

        <div class="admin-main-card">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h3 class="mb-0">Recent Paid Orders</h3>
            </div>

            <?php if (!$recentPaidOrders): ?>
                <div class="alert alert-info">No paid orders found yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Reference Number</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPaidOrders as $order): ?>
                                <tr>
                                    <td>#<?= $order['order_id'] ?></td>
                                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($order['reference_number']) ?></td>
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
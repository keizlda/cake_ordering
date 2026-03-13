<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalStaff = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'staff'")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$completedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Completed'")->fetchColumn();
$totalSales = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE payment_status = 'Paid'
      AND reference_number IS NOT NULL
      AND reference_number != ''
")->fetchColumn();

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
    <title>Admin Dashboard | Sugar Delights</title>

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
            <a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">Logout</a>
        </div>
    </div>
</nav>

<div class="admin-page-wrapper py-5">
    <div class="container">

        <div class="admin-hero-card mb-4">
            <div>
                <div class="hero-badge">ADMIN PANEL</div>
                <h1>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
                <p>Monitor orders, users, products, options, and reports from one organized dashboard.</p>
            </div>

            <div class="admin-hero-actions">
                <a href="/cake_ordering/admin/orders.php" class="btn btn-menu-add">View Orders</a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Total Users</h5>
                    <div class="admin-stat-value"><?= $totalUsers ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Customers</h5>
                    <div class="admin-stat-value"><?= $totalCustomers ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Staff</h5>
                    <div class="admin-stat-value"><?= $totalStaff ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="admin-stat-card">
                    <h5>Total Orders</h5>
                    <div class="admin-stat-value"><?= $totalOrders ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="admin-stat-card">
                    <h5>Pending Orders</h5>
                    <div class="admin-stat-value"><?= $pendingOrders ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="admin-stat-card">
                    <h5>Completed Orders</h5>
                    <div class="admin-stat-value"><?= $completedOrders ?></div>
                </div>
            </div>

            <div class="col-md-12 col-lg-4">
                <div class="admin-stat-card">
                    <h5>Total Sales</h5>
                    <div class="admin-stat-value">₱<?= number_format($totalSales, 2) ?></div>
                </div>
            </div>
        </div>

        <div class="admin-content-grid">
            <div class="admin-main-card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h3 class="mb-0">Recent Orders</h3>
                    <a href="/cake_ordering/admin/orders.php" class="btn btn-menu-secondary">View All Orders</a>
                </div>

                <?php if (!$recentOrders): ?>
                    <div class="alert alert-info">No recent orders found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle admin-table">
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
                                            <span class="admin-status-badge">
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

            <div class="admin-side-card">
                <h3>Quick Actions</h3>

                <div class="admin-action-list">
                    <a href="/cake_ordering/admin/users.php" class="admin-action-item">
                        👥 Manage Users
                    </a>

                    <a href="/cake_ordering/admin/products.php" class="admin-action-item">
                        🎂 Manage Products
                    </a>

                   

                    <a href="/cake_ordering/admin/orders.php" class="admin-action-item">
                        📋 View All Orders
                    </a>

                    <a href="/cake_ordering/admin/reports.php" class="admin-action-item">
                        📈 View Reports
                    </a>
                </div>

                <div class="admin-note-box mt-4">
                    <h5>Admin Note</h5>
                    <p>Keep user records, cake options, and order statuses updated so reports and customer transactions remain accurate.</p>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
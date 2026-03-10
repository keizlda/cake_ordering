<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$totalSales = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status = 'Completed'")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$completedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Completed'")->fetchColumn();

include '../includes/header.php';
?>

<h2>Reports</h2>

<div class="card">
    <div class="card-body">
        <h4>Total Sales: ₱<?= number_format($totalSales, 2) ?></h4>
        <p>Total Orders: <?= $totalOrders ?></p>
        <p>Pending Orders: <?= $pendingOrders ?></p>
        <p>Completed Orders: <?= $completedOrders ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');
include '../includes/header.php';
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>.</p>

<div class="list-group">
    <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
    <a href="products.php" class="list-group-item list-group-item-action">Manage Products</a>
    <a href="options.php" class="list-group-item list-group-item-action">Manage Cake Options</a>
    <a href="orders.php" class="list-group-item list-group-item-action">View Orders</a>
    <a href="reports.php" class="list-group-item list-group-item-action">View Reports</a>
</div>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('staff');
include '../includes/header.php';
?>

<h2>Staff Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>.</p>

<div class="list-group">
    <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
</div>

<?php include '../includes/footer.php'; ?>
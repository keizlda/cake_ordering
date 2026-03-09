<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');
include '../includes/header.php';
?>

<h2>Customer Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>.</p>

<div class="list-group">
    <a href="products.php" class="list-group-item list-group-item-action">Browse Cakes</a>
    <a href="cart.php" class="list-group-item list-group-item-action">View Cart</a>
    <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
    <a href="profile.php" class="list-group-item list-group-item-action">My Profile</a>
</div>

<?php include '../includes/footer.php'; ?>
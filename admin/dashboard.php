<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');
include '../includes/header.php';
?>

<h2>📊 Admin Dashboard</h2>
<p class="lead">Welcome, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></p>
<hr class="decorative-divider">

<div class="row mt-4">
    <div class="col-md-4 mb-4">
        <a href="users.php" class="text-decoration-none">
            <div class="dashboard-card">
                <div class="icon">👥</div>
                <h3>Manage Users</h3>
                <p class="text-muted">View and manage user accounts</p>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="products.php" class="text-decoration-none">
            <div class="dashboard-card">
                <div class="icon">🎂</div>
                <h3>Manage Products</h3>
                <p class="text-muted">Add, edit or remove cake products</p>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="options.php" class="text-decoration-none">
            <div class="dashboard-card">
                <div class="icon">⚙️</div>
                <h3>Manage Cake Options</h3>
                <p class="text-muted">Configure designs, sizes, flavors</p>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="orders.php" class="text-decoration-none">
            <div class="dashboard-card">
                <div class="icon">📋</div>
                <h3>View Orders</h3>
                <p class="text-muted">View and manage customer orders</p>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="reports.php" class="text-decoration-none">
            <div class="dashboard-card">
                <div class="icon">📈</div>
                <h3>View Reports</h3>
                <p class="text-muted">View sales and analytics reports</p>
            </div>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

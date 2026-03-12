<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Ordering System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">🎂 Cake Ordering</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
                    </li>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Dashboard</a></li>
                    <?php elseif($_SESSION['role'] === 'staff'): ?>
                        <li class="nav-item"><a class="nav-link" href="staff/dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="customer/products.php">Cakes</a></li>
                        <li class="nav-item"><a class="nav-link" href="customer/cart.php">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="customer/orders.php">Orders</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">

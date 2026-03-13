<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$stmt = $pdo->query("
SELECT o.*, u.full_name
FROM orders o
JOIN users u ON o.user_id = u.user_id
ORDER BY o.order_id DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>All Orders | Sugar Delights</title>

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

<span class="customer-nav-name">
Admin: <?= htmlspecialchars($_SESSION['full_name']) ?>
</span>

<a href="/cake_ordering/admin/dashboard.php" class="btn btn-nav-dark">
Dashboard
</a>

<a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">
Logout
</a>

</div>

</div>
</nav>


<div class="container py-5">

<div class="staff-orders-header-card mb-4">

<div class="hero-badge">ADMIN PANEL</div>

<h1>All Customer Orders</h1>

<p>Monitor customer purchases, verify payments, and manage order status.</p>

</div>


<?php if(!$orders): ?>

<div class="empty-cart-card text-center p-5">

<h4>No orders found</h4>

<p>There are currently no orders in the system.</p>

</div>

<?php else: ?>


<div class="table-responsive staff-orders-table">

<table class="table align-middle">

<thead>

<tr>

<th>Order ID</th>
<th>Customer</th>
<th>Total</th>
<th>Payment</th>
<th>Reference</th>
<th>Delivery</th>
<th>Status</th>
<th>Date</th>
<th>Update</th>
<th>Details</th>

</tr>

</thead>


<tbody>

<?php foreach($orders as $order): ?>

<tr>

<td>
<strong>#<?= $order['order_id'] ?></strong>
</td>

<td>
<?= htmlspecialchars($order['full_name']) ?>
</td>

<td>
₱<?= number_format($order['total_amount'],2) ?>
</td>

<td>
<?= htmlspecialchars($order['payment_method']) ?><br>
<small><?= htmlspecialchars($order['payment_status']) ?></small>
</td>

<td>
<?= htmlspecialchars($order['reference_number']) ?>
</td>

<td>
<?= htmlspecialchars($order['delivery_method']) ?>
</td>

<td>
<span class="staff-status-badge">
<?= htmlspecialchars($order['order_status']) ?>
</span>
</td>

<td>
<?= htmlspecialchars($order['order_date']) ?>
</td>

<td>

<form method="POST" action="update_order_status.php" class="d-flex gap-2">

<input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

<select name="order_status" class="form-select staff-status-select" required>

<option value="Pending" <?= $order['order_status']=='Pending'?'selected':'' ?>>Pending</option>

<option value="Confirmed" <?= $order['order_status']=='Confirmed'?'selected':'' ?>>Confirmed</option>

<option value="In Preparation" <?= $order['order_status']=='In Preparation'?'selected':'' ?>>In Preparation</option>

<option value="Ready" <?= $order['order_status']=='Ready'?'selected':'' ?>>Ready</option>

<option value="Completed" <?= $order['order_status']=='Completed'?'selected':'' ?>>Completed</option>

<option value="Cancelled" <?= $order['order_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>

</select>

<button class="btn btn-menu-add">
Save
</button>

</form>

</td>

<td>

<a href="view_order.php?order_id=<?= $order['order_id'] ?>" class="btn btn-menu-secondary btn-sm">
View
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</div>

</body>

</html>
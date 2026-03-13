<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

if(!isset($_GET['order_id'])){
    die("Order not found.");
}

$order_id = (int) $_GET['order_id'];

$stmt = $pdo->prepare("
SELECT o.*, u.full_name, u.email, u.contact_number
FROM orders o
JOIN users u ON o.user_id = u.user_id
WHERE o.order_id = ?
");

$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    die("Order not found.");
}

$stmt = $pdo->prepare("
SELECT od.*, 
p.product_name,
d.design_name,
s.size_name,
f.flavor_name,
fi.filling_name,
t.topper_name

FROM order_details od

JOIN products p ON od.product_id = p.product_id
JOIN cake_designs d ON od.design_id = d.design_id
JOIN cake_sizes s ON od.size_id = s.size_id
JOIN cake_flavors f ON od.flavor_id = f.flavor_id
JOIN cake_fillings fi ON od.filling_id = fi.filling_id
LEFT JOIN cake_toppers t ON od.topper_id = t.topper_id

WHERE od.order_id = ?
");

$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Order Details | Sugar Delights</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/cake_ordering/assets/css/style.css" rel="stylesheet">

</head>

<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">

<div class="container">

<a class="navbar-brand brand-logo" href="/cake_ordering/admin/dashboard.php">

<span class="brand-text">Sugar Delights</span>
</a>

<div class="ms-auto d-flex gap-2">

<a href="/cake_ordering/admin/orders.php" class="btn btn-nav-dark">
Back to Orders
</a>

<a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">
Logout
</a>

</div>

</div>
</nav>


<div class="container py-5">

<h2 class="section-heading mb-4">
Order #<?= $order['order_id'] ?> Details
</h2>

<div class="row g-4">

<div class="col-lg-6">

<div class="order-info-card">

<h4>Customer Information</h4>

<p><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>

<p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>

<p><strong>Contact:</strong> <?= htmlspecialchars($order['contact_number']) ?></p>

<p><strong>Delivery Method:</strong> <?= htmlspecialchars($order['delivery_method']) ?></p>

<p><strong>Delivery Address:</strong><br>
<?= htmlspecialchars($order['delivery_address']) ?>
</p>

</div>

</div>


<div class="col-lg-6">

<div class="order-info-card">

<h4>Payment Information</h4>

<p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

<p><strong>Reference Number:</strong> <?= htmlspecialchars($order['reference_number']) ?></p>

<p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>

<p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>

<p><strong>Total Amount:</strong>
₱<?= number_format($order['total_amount'],2) ?>
</p>

</div>

</div>

</div>


<div class="mt-5">

<h3 class="mb-3">Ordered Items</h3>

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>
<th>Product</th>
<th>Design</th>
<th>Size</th>
<th>Flavor</th>
<th>Filling</th>
<th>Topper</th>
<th>Qty</th>
<th>Price</th>
<th>Subtotal</th>
</tr>

</thead>

<tbody>

<?php foreach($items as $item): ?>

<tr>

<td><?= htmlspecialchars($item['product_name']) ?></td>

<td><?= htmlspecialchars($item['design_name']) ?></td>

<td><?= htmlspecialchars($item['size_name']) ?></td>

<td><?= htmlspecialchars($item['flavor_name']) ?></td>

<td><?= htmlspecialchars($item['filling_name']) ?></td>

<td><?= htmlspecialchars($item['topper_name']) ?></td>

<td><?= $item['quantity'] ?></td>

<td>₱<?= number_format($item['unit_price'],2) ?></td>

<td>₱<?= number_format($item['subtotal'],2) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>
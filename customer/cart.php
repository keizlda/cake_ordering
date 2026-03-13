<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT ci.*, 
           p.product_name, 
           p.image,
           d.design_name,
           s.size_name,
           f.flavor_name,
           fi.filling_name,
           t.topper_name
    FROM cart_items ci
    JOIN cart c ON ci.cart_id = c.cart_id
    JOIN products p ON ci.product_id = p.product_id
    JOIN cake_designs d ON ci.design_id = d.design_id
    JOIN cake_sizes s ON ci.size_id = s.size_id
    JOIN cake_flavors f ON ci.flavor_id = f.flavor_id
    JOIN cake_fillings fi ON ci.filling_id = fi.filling_id
    LEFT JOIN cake_toppers t ON ci.topper_id = t.topper_id
    WHERE c.user_id = ? AND c.cart_status = 'active'
    ORDER BY ci.cart_item_id DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart | Sugar Delights</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>

<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
<div class="container">

<a class="navbar-brand brand-logo" href="/cake_ordering/customer/dashboard.php">

<span class="brand-text">Sugar Delights</span>
</a>

<div class="ms-auto d-flex gap-2">
<a href="/cake_ordering/customer/dashboard.php" class="btn btn-nav-dark">Menu</a>
<a href="/cake_ordering/customer/orders.php" class="btn btn-nav-light">Orders</a>
</div>

</div>
</nav>

<div class="container py-5">

<h2 class="section-heading mb-4">My Cart</h2>

<?php if (!$cart_items): ?>

<div class="empty-cart-card text-center p-5">
    <h4>Your cart is empty</h4>
    <p>Browse our delicious cakes and add your favorites.</p>
    <a href="/cake_ordering/customer/dashboard.php" class="btn btn-menu-add mt-3">
        Browse Cakes
    </a>
</div>

<?php else: ?>

<div class="cart-items">

<?php foreach($cart_items as $item): 
    $subtotal = $item['unit_price'] * $item['quantity'];
    $total += $subtotal;
?>

<div class="cart-item-card">

    <div class="cart-image">
        <?php if (!empty($item['image'])): ?>
            <img src="/cake_ordering/assets/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
        <?php else: ?>
            🎂
        <?php endif; ?>
    </div>

    <div class="cart-details">
        <h4><?= htmlspecialchars($item['product_name']) ?></h4>

        <div class="cart-meta">
            <span><b>Design:</b> <?= htmlspecialchars($item['design_name']) ?></span>
            <span><b>Size:</b> <?= htmlspecialchars($item['size_name']) ?></span>
            <span><b>Flavor:</b> <?= htmlspecialchars($item['flavor_name']) ?></span>
            <span><b>Filling:</b> <?= htmlspecialchars($item['filling_name']) ?></span>
            <span><b>Topper:</b> <?= htmlspecialchars($item['topper_name'] ?? 'None') ?></span>
        </div>

        <div class="cart-actions">

            <form method="POST" action="update_cart_quantity.php" class="qty-form">
                <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">

                <button type="submit" name="action" value="minus" class="qty-btn">−</button>

                <span class="qty-number"><?= $item['quantity'] ?></span>

                <button type="submit" name="action" value="plus" class="qty-btn">+</button>
            </form>

            <div class="cart-price">
                <span class="price">₱<?= number_format($item['unit_price'], 2) ?></span>
                <span class="subtotal">Subtotal: ₱<?= number_format($subtotal, 2) ?></span>
            </div>

        </div>
    </div>

</div>

<?php endforeach; ?>

</div>

<div class="cart-summary">
    <div class="cart-total">
        <h3>Total: ₱<?= number_format($total, 2) ?></h3>
        <a href="checkout.php" class="btn btn-menu-add btn-lg">
            Proceed to Checkout
        </a>
    </div>
</div>

<?php endif; ?>

</div>

</body>
</html>
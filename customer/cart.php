<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ? AND cart_status = 'active' LIMIT 1");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

$items = [];
$total = 0;

if ($cart) {
    $stmt = $pdo->prepare("
        SELECT ci.*, p.product_name, d.design_name, s.size_name, f.flavor_name,
               fi.filling_name, t.topper_name
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        JOIN cake_designs d ON ci.design_id = d.design_id
        JOIN cake_sizes s ON ci.size_id = s.size_id
        JOIN cake_flavors f ON ci.flavor_id = f.flavor_id
        JOIN cake_fillings fi ON ci.filling_id = fi.filling_id
        JOIN cake_toppers t ON ci.topper_id = t.topper_id
        WHERE ci.cart_id = ?
        ORDER BY ci.cart_item_id DESC
    ");
    $stmt->execute([$cart['cart_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
}

include '../includes/header.php';
?>

<h2>My Cart</h2>

<?php if (!$items): ?>
    <div class="alert alert-warning">Your cart is empty.</div>
    <a href="products.php" class="btn btn-primary">Browse Cakes</a>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Design</th>
                <th>Size</th>
                <th>Flavor</th>
                <th>Filling</th>
                <th>Topper</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['design_name']) ?></td>
                <td><?= htmlspecialchars($item['size_name']) ?></td>
                <td><?= htmlspecialchars($item['flavor_name']) ?></td>
                <td><?= htmlspecialchars($item['filling_name']) ?></td>
                <td><?= htmlspecialchars($item['topper_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                <td>₱<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Total: ₱<?= number_format($total, 2) ?></h4>
    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
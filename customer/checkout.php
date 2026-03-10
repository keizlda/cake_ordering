<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND cart_status = 'active' LIMIT 1");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    die("No active cart found.");
}

$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ?");
$stmt->execute([$cart['cart_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cartItems) {
    die("Cart is empty.");
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['subtotal'];
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_method = sanitize($_POST['delivery_method']);
    $remarks = sanitize($_POST['remarks']);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, order_status, payment_status, delivery_method, remarks)
            VALUES (?, ?, 'Pending', 'Unpaid', ?, ?)
        ");
        $stmt->execute([$user_id, $total, $delivery_method, $remarks]);

        $order_id = $pdo->lastInsertId();

        $detailStmt = $pdo->prepare("
            INSERT INTO order_details
            (order_id, product_id, design_id, size_id, flavor_id, filling_id, topper_id, quantity, unit_price, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($cartItems as $item) {
            $detailStmt->execute([
                $order_id,
                $item['product_id'],
                $item['design_id'],
                $item['size_id'],
                $item['flavor_id'],
                $item['filling_id'],
                $item['topper_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['subtotal']
            ]);
        }

        $stmt = $pdo->prepare("UPDATE cart SET cart_status = 'checked_out' WHERE cart_id = ?");
        $stmt->execute([$cart['cart_id']]);

        $pdo->commit();
        redirect('/cake_ordering/customer/orders.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Checkout failed: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<h2>Checkout</h2>

<?php if ($message): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<p><strong>Total Amount:</strong> ₱<?= number_format($total, 2) ?></p>

<form method="POST">
    <div class="mb-3">
        <label>Delivery Method</label>
        <select name="delivery_method" class="form-control" required>
            <option value="Pickup">Pickup</option>
            <option value="Delivery">Delivery</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Place Order</button>
</form>

<?php include '../includes/footer.php'; ?>
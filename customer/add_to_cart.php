<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/cake_ordering/customer/products.php');
}

$user_id = $_SESSION['user_id'];
$product_id = (int) $_POST['product_id'];
$design_id = (int) $_POST['design_id'];
$size_id = (int) $_POST['size_id'];
$flavor_id = (int) $_POST['flavor_id'];
$filling_id = (int) $_POST['filling_id'];
$topper_id = (int) $_POST['topper_id'];
$quantity = max(1, (int) $_POST['quantity']);

$stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ? AND cart_status = 'active' LIMIT 1");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, cart_status) VALUES (?, 'active')");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['cart_id'];
}

$stmt = $pdo->prepare("SELECT base_price FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT design_price_adjustment FROM cake_designs WHERE design_id = ?");
$stmt->execute([$design_id]);
$design = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT size_price_adjustment FROM cake_sizes WHERE size_id = ?");
$stmt->execute([$size_id]);
$size = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT topper_price_adjustment FROM cake_toppers WHERE topper_id = ?");
$stmt->execute([$topper_id]);
$topper = $stmt->fetch(PDO::FETCH_ASSOC);

$unit_price = (float)$product['base_price']
            + (float)$design['design_price_adjustment']
            + (float)$size['size_price_adjustment']
            + (float)$topper['topper_price_adjustment'];

$subtotal = $unit_price * $quantity;

$stmt = $pdo->prepare("
    INSERT INTO cart_items
    (cart_id, product_id, design_id, size_id, flavor_id, filling_id, topper_id, quantity, unit_price, subtotal)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $cart_id,
    $product_id,
    $design_id,
    $size_id,
    $flavor_id,
    $filling_id,
    $topper_id,
    $quantity,
    $unit_price,
    $subtotal
]);

redirect('/cake_ordering/customer/cart.php');
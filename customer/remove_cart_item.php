<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/cake_ordering/customer/cart.php');
}

$user_id = $_SESSION['user_id'];
$cart_item_id = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;

$stmt = $pdo->prepare("
    DELETE ci
    FROM cart_items ci
    JOIN cart c ON ci.cart_id = c.cart_id
    WHERE ci.cart_item_id = ? AND c.user_id = ? AND c.cart_status = 'active'
");
$stmt->execute([$cart_item_id, $user_id]);

redirect('/cake_ordering/customer/cart.php');
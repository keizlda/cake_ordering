<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/cake_ordering/customer/cart.php');
}

$user_id = $_SESSION['user_id'];
$cart_item_id = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
$action = $_POST['action'] ?? '';

$stmt = $pdo->prepare("
    SELECT ci.*, c.user_id
    FROM cart_items ci
    JOIN cart c ON ci.cart_id = c.cart_id
    WHERE ci.cart_item_id = ? AND c.user_id = ? AND c.cart_status = 'active'
    LIMIT 1
");
$stmt->execute([$cart_item_id, $user_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    redirect('/cake_ordering/customer/cart.php');
}

$current_quantity = (int)$item['quantity'];

/* ADD ITEM */
if ($action === 'plus' && $current_quantity < 5) {

    $new_quantity = $current_quantity + 1;
    $new_subtotal = $item['unit_price'] * $new_quantity;

    $stmt = $pdo->prepare("
        UPDATE cart_items
        SET quantity = ?, subtotal = ?
        WHERE cart_item_id = ?
    ");
    $stmt->execute([$new_quantity, $new_subtotal, $cart_item_id]);

}

/* SUBTRACT ITEM */
elseif ($action === 'minus') {

    /* if quantity is 1 remove item */
    if ($current_quantity <= 1) {

        $stmt = $pdo->prepare("
            DELETE ci
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.cart_id
            WHERE ci.cart_item_id = ? AND c.user_id = ?
        ");
        $stmt->execute([$cart_item_id, $user_id]);

    } else {

        $new_quantity = $current_quantity - 1;
        $new_subtotal = $item['unit_price'] * $new_quantity;

        $stmt = $pdo->prepare("
            UPDATE cart_items
            SET quantity = ?, subtotal = ?
            WHERE cart_item_id = ?
        ");
        $stmt->execute([$new_quantity, $new_subtotal, $cart_item_id]);

    }

}

redirect('/cake_ordering/customer/cart.php');
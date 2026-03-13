<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
    $order_status = sanitize($_POST['order_status'] ?? '');

    $allowed = ['Pending', 'Confirmed', 'In Preparation', 'Ready', 'Completed', 'Cancelled'];

    if ($order_id > 0 && in_array($order_status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->execute([$order_status, $order_id]);
    }
}

header("Location: /cake_ordering/admin/orders.php");
exit();
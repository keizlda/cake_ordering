<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int) $_POST['order_id'];
    $order_status = sanitize($_POST['order_status']);

    $allowed = ['Pending', 'Confirmed', 'In Preparation', 'Ready', 'Completed', 'Cancelled'];

    if (in_array($order_status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->execute([$order_status, $order_id]);
    }
}

redirect('/cake_ordering/staff/orders.php');
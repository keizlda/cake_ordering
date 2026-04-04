<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../test_db.php';

requireLogin();
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $order_id = $_POST['order_id'];

    try {

        // ✅ DELETE CHILD RECORDS FIRST
        $stmt = $pdo->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // ✅ DELETE MAIN ORDER
        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);

        header("Location: orders.php");
        exit;

    } catch (PDOException $e) {
        die("Error deleting order: " . $e->getMessage());
    }
}
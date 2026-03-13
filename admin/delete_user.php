<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

if($_SERVER['REQUEST_METHOD']=="POST"){

$user_id = (int) $_POST['user_id'];

$stmt = $pdo->prepare("
DELETE FROM users
WHERE user_id = ?
");

$stmt->execute([$user_id]);

header("Location: users.php");
exit;
}
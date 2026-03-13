<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

if($_SERVER['REQUEST_METHOD']=="POST"){

$user_id = (int) $_POST['user_id'];
$role = $_POST['role'];

$stmt = $pdo->prepare("
UPDATE users
SET role = ?
WHERE user_id = ?
");

$stmt->execute([$role,$user_id]);

header("Location: users.php");
exit;
}
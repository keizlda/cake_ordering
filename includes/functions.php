<?php
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /cake_ordering/auth/login.php");
        exit();
    }
}

function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        die("Access denied.");
    }
}
?>
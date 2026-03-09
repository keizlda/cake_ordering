<?php
require_once '../includes/auth.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

      if ($user['role'] === 'admin') {
    redirect('/cake_ordering/admin/dashboard.php');
} elseif ($user['role'] === 'staff') {
    redirect('/cake_ordering/staff/dashboard.php');
} else {
    redirect('/cake_ordering/customer/dashboard.php');
}
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Login</h2>
<?php if ($message): ?>
    <div class="alert alert-danger"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
    <button type="submit" class="btn btn-success">Login</button>
</form>

<?php include '../includes/footer.php'; ?>
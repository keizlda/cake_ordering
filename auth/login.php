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
        $_SESSION['email'] = $user['email'];
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sugar Delights</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/">
           
            <span class="brand-text">Sugar Delights</span>
        </a>

        <div class="ms-auto d-flex gap-2">
            <a href="/cake_ordering/" class="btn btn-nav-dark">Home</a>
            <a href="/cake_ordering/auth/register.php" class="btn btn-nav-light">Register</a>
        </div>
    </div>
</nav>

<section class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10">
                <div class="auth-card">

                    <div class="auth-left">
                        <div class="auth-badge">WELCOME BACK</div>
                        <h1>Sign in to your account</h1>
                        <p>
                            Continue your cake orders, manage your cart, and track your sweet
                            celebrations with Sugar Delights.
                        </p>

                        <div class="auth-benefits">
                            <div class="benefit-item">🎂 Manage your orders easily</div>
                            <div class="benefit-item">🛒 Continue from your cart</div>
                            <div class="benefit-item">🚚 Check pickup or delivery updates</div>
                        </div>
                    </div>

                    <div class="auth-right">
                        <h2 class="form-title">Login</h2>
                        <p class="form-subtitle">Enter your account details to continue.</p>

                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>

                        <form method="POST" autocomplete="on">

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control custom-input"
                                    required
                                    autocomplete="email"
                                    placeholder="Enter your email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        name="password"
                                        id="loginPassword"
                                        class="form-control custom-input password-input"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Enter your password">
                                    <button type="button" class="btn btn-password-toggle" onclick="togglePassword()">
                                        Show
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-auth-submit w-100">Sign In</button>

                            <div class="auth-footer-text">
                                Don’t have an account?
                                <a href="/cake_ordering/auth/register.php">Create one</a>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
function togglePassword() {
    const passwordInput = document.getElementById("loginPassword");
    const toggleButton = document.querySelector(".btn-password-toggle");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleButton.textContent = "Hide";
    } else {
        passwordInput.type = "password";
        toggleButton.textContent = "Show";
    }
}
</script>

</body>
</html>
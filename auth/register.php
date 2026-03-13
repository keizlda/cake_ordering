<?php
require_once '../includes/auth.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = sanitize($_POST['contact_number']);
    $address = sanitize($_POST['address']);
    $role = "customer";

    if (!preg_match('/^[^@\s]+@[^@\s]+\.com$/', $email)) {
        $message = "Email must be valid and end with .com";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users
                (full_name, email, password, role, contact_number, address)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $full_name,
                $email,
                $hashedPassword,
                $role,
                $contact_number,
                $address
            ]);

            header("Location: /cake_ordering/auth/login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Sugar Delights</title>

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
            <a href="/cake_ordering/auth/login.php" class="btn btn-nav-light">Sign in</a>
        </div>
    </div>
</nav>

<section class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10">
                <div class="auth-card">

                    <div class="auth-left">
                        <div class="auth-badge">SWEET BEGINNINGS</div>
                        <h1>Create your account</h1>
                        <p>
                            Join Sugar Delights and start ordering beautifully crafted cakes for birthdays,
                            celebrations, and special moments.
                        </p>

                        <div class="auth-benefits">
                            <div class="benefit-item">🎂 Easy cake ordering</div>
                            <div class="benefit-item">💗 Delivery or pickup options</div>
                            <div class="benefit-item">✨ Smooth checkout experience</div>
                        </div>
                    </div>

                    <div class="auth-right">
                        <h2 class="form-title">Register</h2>
                        <p class="form-subtitle">Fill in your details to create your account.</p>

                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>

                        <form method="POST" id="registerForm" autocomplete="on">

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control custom-input" required autocomplete="name" placeholder="Enter your full name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control custom-input" required autocomplete="email" placeholder="Enter your email">
                                <small id="emailError" class="text-danger"></small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control custom-input" required autocomplete="new-password" placeholder="Enter password">
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control custom-input" required autocomplete="new-password" placeholder="Confirm password">
                                    <small id="passwordError" class="text-danger"></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control custom-input" autocomplete="tel" inputmode="tel" placeholder="Enter contact number">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control custom-input" rows="3" autocomplete="street-address" placeholder="Enter your address"></textarea>
                            </div>

                            <button type="submit" class="btn btn-auth-submit w-100">Create Account</button>

                            <div class="auth-footer-text">
                                Already have an account?
                                <a href="/cake_ordering/auth/login.php">Sign in</a>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById("email").addEventListener("input", function() {
    let email = this.value;
    let error = document.getElementById("emailError");

    if (email !== "" && !email.endsWith(".com")) {
        error.textContent = "Email must end with .com";
    } else {
        error.textContent = "";
    }
});

document.getElementById("confirm_password").addEventListener("input", function() {
    let password = document.getElementById("password").value;
    let confirm = this.value;
    let error = document.getElementById("passwordError");

    if (confirm !== "" && password !== confirm) {
        error.textContent = "Passwords do not match";
    } else {
        error.textContent = "";
    }
});
</script>

</body>
</html>
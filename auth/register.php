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

    // EMAIL VALIDATION (.com only)
    if (!preg_match('/^[^@\s]+@[^@\s]+\.com$/', $email)) {
        $message = "Email must be valid and end with .com";
    }

    // PASSWORD LENGTH
    elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    }

    // PASSWORD MATCH
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }

    else {

        // CHECK EMAIL EXISTS
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = "Email already registered.";
        }

        else {

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

            // Redirect to login
            header("Location: /cake_ordering/auth/login.php");
            exit();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="auth-container">
    <h2>Create Account</h2>

    <?php if ($message): ?>
    <div class="alert alert-danger">
    <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="registerForm">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            <small id="emailError" class="text-danger"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters" required>
            <small class="form-text">Minimum 6 characters</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
            <small id="passwordError" class="text-danger"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" placeholder="Enter contact number">
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" placeholder="Enter your address" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Register
        </button>
    </form>
    
    <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
    // EMAIL VALIDATION (.com)
    document.getElementById("email").addEventListener("input", function() {
        let email = this.value;
        let error = document.getElementById("emailError");
        if (!email.endsWith(".com")) {
            error.textContent = "Must be a valid email ending with .com";
        } else {
            error.textContent = "";
        }
    });

    // PASSWORD MATCH VALIDATION
    document.getElementById("confirm_password").addEventListener("input", function() {
        let password = document.getElementById("password").value;
        let confirm = this.value;
        let error = document.getElementById("passwordError");
        if (password !== confirm) {
            error.textContent = "Passwords do not match";
        } else {
            error.textContent = "";
        }
    });
</script>

<?php include '../includes/footer.php'; ?>

<?php
require_once '../includes/auth.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $contact_number = sanitize($_POST['contact_number']);
    $address = sanitize($_POST['address']);
    $role = "customer";

    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "Please fill in all required fields.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, contact_number, address)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashedPassword, $role, $contact_number, $address]);

        $message = "Registration successful.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Register</h2>
<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
    <input type="text" name="contact_number" class="form-control mb-2" placeholder="Contact Number">
    <textarea name="address" class="form-control mb-2" placeholder="Address"></textarea>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php include '../includes/footer.php'; ?>
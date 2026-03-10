<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $contact_number = sanitize($_POST['contact_number']);
    $address = sanitize($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, contact_number = ?, address = ? WHERE user_id = ?");
    $stmt->execute([$full_name, $contact_number, $address, $user_id]);

    $_SESSION['full_name'] = $full_name;
    $message = "Profile updated successfully.";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>My Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label>Contact Number</label>
        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($user['contact_number']) ?>">
    </div>

    <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<?php include '../includes/footer.php'; ?>
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

    $stmt = $pdo->prepare("
        UPDATE users
        SET full_name = ?, contact_number = ?, address = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$full_name, $contact_number, $address, $user_id]);

    $_SESSION['full_name'] = $full_name;
    $message = "Profile updated successfully.";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Sugar Delights</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/customer/dashboard.php">
            <span class="brand-text">Sugar Delights</span>
        </a>

        <div class="ms-auto d-flex gap-2">
            <a href="/cake_ordering/customer/dashboard.php" class="btn btn-nav-dark">Menu</a>
            <a href="/cake_ordering/customer/cart.php" class="btn btn-nav-light">Cart</a>
            <a href="/cake_ordering/customer/orders.php" class="btn btn-nav-light">Orders</a>
        </div>
    </div>
</nav>

<div class="profile-page-wrapper py-5">
    <div class="container">
        <div class="profile-layout">

            <div class="profile-side-card">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                </div>

                <h3><?= htmlspecialchars($user['full_name']) ?></h3>
                <p class="profile-role">Customer Account</p>

                <div class="profile-side-info">
                    <div><strong>Email:</strong><br><?= htmlspecialchars($user['email']) ?></div>
                    <div><strong>Contact:</strong><br><?= htmlspecialchars($user['contact_number'] ?: 'Not set') ?></div>
                </div>

                <a href="/cake_ordering/customer/dashboard.php" class="btn btn-menu-secondary w-100 mt-3">Back to Menu</a>
            </div>

            <div class="profile-main-card">
                <h2 class="section-heading mb-2">My Profile</h2>
                <p class="profile-subtext">Manage your personal information for easier ordering and delivery.</p>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" class="mt-4">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input
                            type="text"
                            name="full_name"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($user['full_name']) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($user['email']) ?>"
                            disabled>
                        <small class="text-muted">Email cannot be changed here.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input
                            type="text"
                            name="contact_number"
                            class="form-control custom-input"
                            value="<?= htmlspecialchars($user['contact_number']) ?>"
                            placeholder="Enter contact number">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea
                            name="address"
                            class="form-control custom-input"
                            rows="4"
                            placeholder="Enter your address"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <button type="submit" class="btn btn-menu-add">Save Changes</button>
                        <a href="/cake_ordering/customer/orders.php" class="btn btn-menu-secondary">My Orders</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$stmt = $pdo->query("
SELECT user_id, full_name, email, contact_number, role
FROM users
ORDER BY user_id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Sugar Delights</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>

<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
<div class="container">

<a class="navbar-brand brand-logo" href="/cake_ordering/admin/dashboard.php">

<span class="brand-text">Sugar Delights</span>
</a>

<div class="ms-auto d-flex gap-2 align-items-center">
<span class="customer-nav-name">
Admin: <?= htmlspecialchars($_SESSION['full_name']) ?>
</span>

<a href="/cake_ordering/admin/dashboard.php" class="btn btn-nav-dark">
Dashboard
</a>

<a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">
Logout
</a>
</div>

</div>
</nav>

<div class="container py-5">

<div class="staff-orders-header-card mb-4">
<div class="hero-badge">ADMIN PANEL</div>
<h1>Manage Users</h1>
<p>View and manage all registered users of the system.</p>
</div>

<?php if(!$users): ?>

<div class="empty-cart-card text-center p-5">
<h4>No users found</h4>
<p>There are currently no users registered.</p>
</div>

<?php else: ?>

<div class="table-responsive staff-orders-table">
<table class="table align-middle">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Contact</th>
<th>Role</th>
<th>Update Role</th>
<th>Delete</th>
</tr>
</thead>

<tbody>

<?php foreach($users as $user): ?>
<tr>

<td><?= $user['user_id'] ?></td>

<td><?= htmlspecialchars($user['full_name']) ?></td>

<td><?= htmlspecialchars($user['email']) ?></td>

<td><?= htmlspecialchars($user['contact_number']) ?></td>

<td>
<span class="staff-status-badge">
<?= htmlspecialchars($user['role']) ?>
</span>
</td>

<td>
<form method="POST" action="update_user_role.php" class="d-flex gap-2">
<input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

<select name="role" class="form-select staff-status-select">
<option value="customer" <?= $user['role']=='customer'?'selected':'' ?>>Customer</option>
<option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff</option>
<option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
</select>

<button class="btn btn-menu-add">
Save
</button>
</form>
</td>

<td>
<form method="POST" action="delete_user.php" onsubmit="return confirm('Delete this user?');">
<input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
<button class="btn btn-menu-secondary btn-sm">
Delete
</button>
</form>
</td>

</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<?php endif; ?>

</div>

</body>
</html>
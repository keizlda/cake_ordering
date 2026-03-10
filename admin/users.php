<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$users = $pdo->query("
    SELECT user_id, full_name, email, role, contact_number, created_at
    FROM users
    ORDER BY user_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>Manage Users</h2>

<?php if (!$users): ?>
    <div class="alert alert-info">No users found.</div>
<?php else: ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Contact</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['user_id'] ?></td>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= htmlspecialchars($user['contact_number']) ?></td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
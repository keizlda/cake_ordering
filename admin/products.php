<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = sanitize($_POST['product_name']);
    $description = sanitize($_POST['description']);
    $base_price = (float) $_POST['base_price'];

    if ($product_name !== '' && $base_price >= 0) {
        $stmt = $pdo->prepare("
            INSERT INTO products (product_name, description, base_price, availability_status)
            VALUES (?, ?, ?, 'available')
        ");
        $stmt->execute([$product_name, $description, $base_price]);
        $message = "Product added successfully.";
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>Manage Products</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" class="mb-4">
    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label>Base Price</label>
        <input type="number" step="0.01" name="base_price" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Description</th>
            <th>Base Price</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['product_id'] ?></td>
            <td><?= htmlspecialchars($product['product_name']) ?></td>
            <td><?= htmlspecialchars($product['description']) ?></td>
            <td>₱<?= number_format($product['base_price'], 2) ?></td>
            <td><?= htmlspecialchars($product['availability_status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
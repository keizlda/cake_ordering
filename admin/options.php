<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $name = sanitize($_POST['name']);
    $adjustment = isset($_POST['adjustment']) ? (float) $_POST['adjustment'] : 0;

    if ($type === 'design') {
        $stmt = $pdo->prepare("INSERT INTO cake_designs (design_name, design_price_adjustment) VALUES (?, ?)");
        $stmt->execute([$name, $adjustment]);
    } elseif ($type === 'size') {
        $stmt = $pdo->prepare("INSERT INTO cake_sizes (size_name, size_price_adjustment) VALUES (?, ?)");
        $stmt->execute([$name, $adjustment]);
    } elseif ($type === 'flavor') {
        $stmt = $pdo->prepare("INSERT INTO cake_flavors (flavor_name) VALUES (?)");
        $stmt->execute([$name]);
    } elseif ($type === 'filling') {
        $stmt = $pdo->prepare("INSERT INTO cake_fillings (filling_name) VALUES (?)");
        $stmt->execute([$name]);
    } elseif ($type === 'topper') {
        $stmt = $pdo->prepare("INSERT INTO cake_toppers (topper_name, topper_price_adjustment) VALUES (?, ?)");
        $stmt->execute([$name, $adjustment]);
    }

    $message = "Option added successfully.";
}

include '../includes/header.php';
?>

<h2>Manage Cake Options</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Option Type</label>
        <select name="type" class="form-control" required>
            <option value="design">Design</option>
            <option value="size">Size</option>
            <option value="flavor">Flavor</option>
            <option value="filling">Filling</option>
            <option value="topper">Topper</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Price Adjustment</label>
        <input type="number" step="0.01" name="adjustment" class="form-control" value="0">
    </div>

    <button type="submit" class="btn btn-primary">Add Option</button>
</form>

<?php include '../includes/footer.php'; ?>
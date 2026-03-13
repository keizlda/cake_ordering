<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

if (!isset($_GET['product_id'])) {
    die("Product not found.");
}

$product_id = (int) $_GET['product_id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND availability_status = 'available' LIMIT 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

$designs = $pdo->query("SELECT * FROM cake_designs ORDER BY design_name")->fetchAll(PDO::FETCH_ASSOC);
$sizes = $pdo->query("SELECT * FROM cake_sizes ORDER BY size_id")->fetchAll(PDO::FETCH_ASSOC);
$flavors = $pdo->query("SELECT * FROM cake_flavors ORDER BY flavor_name")->fetchAll(PDO::FETCH_ASSOC);
$fillings = $pdo->query("SELECT * FROM cake_fillings ORDER BY filling_name")->fetchAll(PDO::FETCH_ASSOC);
$toppers = $pdo->query("SELECT * FROM cake_toppers ORDER BY topper_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> | Sugar Delights</title>

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
        </div>
    </div>
</nav>

<div class="products-page-wrapper py-5">
    <div class="container">
        <div class="single-product-card">
            <div class="single-product-image-wrap">
                <?php if (!empty($product['image'])): ?>
                    <img src="/cake_ordering/assets/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="single-product-image">
                <?php else: ?>
                    <div class="single-product-placeholder">🎂</div>
                <?php endif; ?>
            </div>

            <div class="single-product-body">
                <a href="/cake_ordering/customer/dashboard.php" class="back-link">← Back to Menu</a>

                <h1><?= htmlspecialchars($product['product_name']) ?></h1>
                <p class="single-product-desc"><?= htmlspecialchars($product['description']) ?></p>
                <div class="single-product-price">Base Price: ₱<?= number_format($product['base_price'], 2) ?></div>

                <form method="POST" action="/cake_ordering/customer/add_to_cart.php" class="mt-4">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cake Design</label>
                            <select name="design_id" class="form-select custom-select" required>
                                <?php foreach ($designs as $design): ?>
                                    <option value="<?= $design['design_id'] ?>">
                                        <?= htmlspecialchars($design['design_name']) ?> (+₱<?= number_format($design['design_price_adjustment'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <select name="size_id" class="form-select custom-select" required>
                                <?php foreach ($sizes as $size): ?>
                                    <option value="<?= $size['size_id'] ?>">
                                        <?= htmlspecialchars($size['size_name']) ?> (+₱<?= number_format($size['size_price_adjustment'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Flavor</label>
                            <select name="flavor_id" class="form-select custom-select" required>
                                <?php foreach ($flavors as $flavor): ?>
                                    <option value="<?= $flavor['flavor_id'] ?>">
                                        <?= htmlspecialchars($flavor['flavor_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Filling</label>
                            <select name="filling_id" class="form-select custom-select" required>
                                <?php foreach ($fillings as $filling): ?>
                                    <option value="<?= $filling['filling_id'] ?>">
                                        <?= htmlspecialchars($filling['filling_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Topper</label>
                            <select name="topper_id" class="form-select custom-select" required>
                                <?php foreach ($toppers as $topper): ?>
                                    <option value="<?= $topper['topper_id'] ?>">
                                        <?= htmlspecialchars($topper['topper_name']) ?> (+₱<?= number_format($topper['topper_price_adjustment'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control custom-input" min="1" max="5" value="1" required>
                            <small class="text-muted">Maximum 5 per item</small>
                        </div>
                    </div>

                    <div class="menu-product-actions mt-4">
                        <button type="submit" class="btn btn-menu-add">Add to Cart</button>
                        <a href="/cake_ordering/customer/cart.php" class="btn btn-menu-secondary">View Cart</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
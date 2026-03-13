<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$products = $pdo->query("SELECT * FROM products WHERE availability_status = 'available' ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$designs = $pdo->query("SELECT * FROM cake_designs ORDER BY design_name")->fetchAll(PDO::FETCH_ASSOC);
$sizes = $pdo->query("SELECT * FROM cake_sizes ORDER BY size_id")->fetchAll(PDO::FETCH_ASSOC);
$flavors = $pdo->query("SELECT * FROM cake_flavors ORDER BY flavor_name")->fetchAll(PDO::FETCH_ASSOC);
$fillings = $pdo->query("SELECT * FROM cake_fillings ORDER BY filling_name")->fetchAll(PDO::FETCH_ASSOC);
$toppers = $pdo->query("SELECT * FROM cake_toppers ORDER BY topper_name")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="products-page-wrapper py-5">
    <div class="container">

        <div class="products-page-header text-center mb-5">
            <h1>Our Cake Menu</h1>
            <p>Choose your favorite cake and customize it with your preferred design, size, flavor, filling, and topper.</p>
        </div>

        <?php if (!$products): ?>
            <div class="alert alert-warning text-center">No available cake products found.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-6">
                        <div class="menu-product-card h-100">

                            <div class="menu-product-image-wrap">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="/cake_ordering/assets/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="menu-product-image">
                                <?php else: ?>
                                    <div class="menu-product-placeholder">🎂</div>
                                <?php endif; ?>
                            </div>

                            <div class="menu-product-body">
                                <div class="menu-product-top">
                                    <div>
                                        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                                        <p class="menu-product-desc"><?= htmlspecialchars($product['description']) ?></p>
                                    </div>
                                    <div class="menu-price-badge">
                                        ₱<?= number_format($product['base_price'], 2) ?>
                                    </div>
                                </div>

                                <form method="POST" action="add_to_cart.php" class="mt-3">
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
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
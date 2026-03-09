<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$products = $pdo->query("SELECT * FROM products WHERE availability_status = 'available'")->fetchAll(PDO::FETCH_ASSOC);
$designs = $pdo->query("SELECT * FROM cake_designs ORDER BY design_name")->fetchAll(PDO::FETCH_ASSOC);
$sizes = $pdo->query("SELECT * FROM cake_sizes ORDER BY size_id")->fetchAll(PDO::FETCH_ASSOC);
$flavors = $pdo->query("SELECT * FROM cake_flavors ORDER BY flavor_name")->fetchAll(PDO::FETCH_ASSOC);
$fillings = $pdo->query("SELECT * FROM cake_fillings ORDER BY filling_name")->fetchAll(PDO::FETCH_ASSOC);
$toppers = $pdo->query("SELECT * FROM cake_toppers ORDER BY topper_name")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2>Browse Cakes</h2>

<?php if (!$products): ?>
    <div class="alert alert-warning">No available products found.</div>
<?php endif; ?>

<?php foreach ($products as $product): ?>
<div class="card mb-4">
    <div class="card-body">
        <h4><?= htmlspecialchars($product['product_name']) ?></h4>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <p><strong>Base Price:</strong> ₱<?= number_format($product['base_price'], 2) ?></p>

        <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

            <div class="mb-3">
                <label>Cake Design</label>
                <select name="design_id" class="form-control" required>
                    <?php foreach ($designs as $design): ?>
                        <option value="<?= $design['design_id'] ?>">
                            <?= htmlspecialchars($design['design_name']) ?> (+₱<?= number_format($design['design_price_adjustment'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Size</label>
                <select name="size_id" class="form-control" required>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size['size_id'] ?>">
                            <?= htmlspecialchars($size['size_name']) ?> (+₱<?= number_format($size['size_price_adjustment'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Flavor</label>
                <select name="flavor_id" class="form-control" required>
                    <?php foreach ($flavors as $flavor): ?>
                        <option value="<?= $flavor['flavor_id'] ?>">
                            <?= htmlspecialchars($flavor['flavor_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Filling</label>
                <select name="filling_id" class="form-control" required>
                    <?php foreach ($fillings as $filling): ?>
                        <option value="<?= $filling['filling_id'] ?>">
                            <?= htmlspecialchars($filling['filling_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Topper</label>
                <select name="topper_id" class="form-control" required>
                    <?php foreach ($toppers as $topper): ?>
                        <option value="<?= $topper['topper_id'] ?>">
                            <?= htmlspecialchars($topper['topper_name']) ?> (+₱<?= number_format($topper['topper_price_adjustment'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
            </div>

            <button type="submit" class="btn btn-primary">Add to Cart</button>
        </form>
    </div>
</div>
<?php endforeach; ?>

<?php include '../includes/footer.php'; ?>
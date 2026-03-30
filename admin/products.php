<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('admin');

$message = "";

/* ADD PRODUCT */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = sanitize($_POST['product_name']);
    $description = sanitize($_POST['description']);
    $base_price = (float) $_POST['base_price'];
    $availability_status = sanitize($_POST['availability_status']);

    $imageName = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = "../assets/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];

        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExt, $allowedExt)) {
            $message = "Only JPG, JPEG, PNG, and WEBP files are allowed.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $message = "Image size must not exceed 5MB.";
        } else {
            $imageName = time() . "" . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $originalName);
            $targetPath = $uploadDir . $imageName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $message = "Failed to upload image.";
            }
        }
    }

    if ($message === "") {
        $stmt = $pdo->prepare("
            INSERT INTO products (product_name, description, base_price, image, availability_status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$product_name, $description, $base_price, $imageName, $availability_status]);
        $message = "Product added successfully.";
    }
}

/* DELETE PRODUCT */
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    //Check if used in orders
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_details WHERE product_id = ?");
    $stmt->execute([$delete_id]);
    $orderCount = $stmt->fetchColumn();

    if ($orderCount > 0) {
        $message = "❌ Cannot delete: Product is already used in orders.";
    } else {

        // delete related cart items
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE product_id = ?");
        $stmt->execute([$delete_id]);

        // delete image
        $stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
        $stmt->execute([$delete_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && !empty($product['image'])) {
            $imagePath = "../assets/uploads/" . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$delete_id]);

        $message = "✅ Product deleted successfully.";
    }
}

/* UPDATE PRODUCT */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = (int) $_POST['product_id'];
    $product_name = sanitize($_POST['product_name']);
    $description = sanitize($_POST['description']);
    $base_price = (float) $_POST['base_price'];
    $availability_status = sanitize($_POST['availability_status']);
    $current_image = sanitize($_POST['current_image']);

    $imageName = $current_image;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = "../assets/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];

        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExt, $allowedExt)) {
            $message = "Only JPG, JPEG, PNG, and WEBP files are allowed.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $message = "Image size must not exceed 5MB.";
        } else {
            $imageName = time() . "" . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $originalName);
            $targetPath = $uploadDir . $imageName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $message = "Failed to upload new image.";
            }
        }
    }

    if ($message === "") {
        $stmt = $pdo->prepare("
            UPDATE products
            SET product_name = ?, description = ?, base_price = ?, image = ?, availability_status = ?
            WHERE product_id = ?
        ");
        $stmt->execute([$product_name, $description, $base_price, $imageName, $availability_status, $product_id]);
        $message = "Product updated successfully.";
    }
}

/* ADD CAKE OPTION */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_option'])) {
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

    $message = "Cake option added successfully.";
}

$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$designs = $pdo->query("SELECT * FROM cake_designs ORDER BY design_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$sizes = $pdo->query("SELECT * FROM cake_sizes ORDER BY size_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$flavors = $pdo->query("SELECT * FROM cake_flavors ORDER BY flavor_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$fillings = $pdo->query("SELECT * FROM cake_fillings ORDER BY filling_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$toppers = $pdo->query("SELECT * FROM cake_toppers ORDER BY topper_id DESC")->fetchAll(PDO::FETCH_ASSOC);

$editProduct = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$edit_id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Sugar Delights</title>

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
            <span class="customer-nav-name">Admin: <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="/cake_ordering/admin/dashboard.php" class="btn btn-nav-dark">Dashboard</a>
            <a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">Logout</a>
        </div>
    </div>
</nav>

<div class="products-page-wrapper py-5">
    <div class="container">

        <div class="staff-orders-header-card mb-4">
            <div class="hero-badge">ADMIN PANEL</div>
            <h1>Manage Products</h1>
            <p>Add, update, and organize products and cake options in one page.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="admin-product-layout">

            <!-- LEFT COLUMN -->
            <div class="admin-product-form-card">
                <h3 class="mb-3"><?= $editProduct ? 'Update Product' : 'Add New Product' ?></h3>

                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="update_product" value="1">
                        <input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?>">
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($editProduct['image']) ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_product" value="1">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control custom-input" required
                               value="<?= $editProduct ? htmlspecialchars($editProduct['product_name']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control custom-input" rows="4"><?= $editProduct ? htmlspecialchars($editProduct['description']) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Base Price</label>
                        <input type="number" step="0.01" name="base_price" class="form-control custom-input" required
                               value="<?= $editProduct ? htmlspecialchars($editProduct['base_price']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <select name="availability_status" class="form-select custom-select" required>
                            <option value="available" <?= ($editProduct && $editProduct['availability_status'] === 'available') ? 'selected' : '' ?>>Available</option>
                            <option value="unavailable" <?= ($editProduct && $editProduct['availability_status'] === 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control custom-input" accept=".jpg,.jpeg,.png,.webp">
                        <?php if ($editProduct && !empty($editProduct['image'])): ?>
                            <small class="text-muted d-block mt-2">Current: <?= htmlspecialchars($editProduct['image']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-menu-add">
                            <?= $editProduct ? 'Update Product' : 'Add Product' ?>
                        </button>

                        <?php if ($editProduct): ?>
                            <a href="/cake_ordering/admin/products.php" class="btn btn-menu-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>

                <hr class="my-4">

                <h3 class="mb-3">Add Cake Option</h3>

                <form method="POST">
                    <input type="hidden" name="add_option" value="1">

                    <div class="mb-3">
                        <label class="form-label">Option Type</label>
                        <select name="type" class="form-select custom-select" required>
                            <option value="design">Design</option>
                            <option value="size">Size</option>
                            <option value="flavor">Flavor</option>
                            <option value="filling">Filling</option>
                            <option value="topper">Topper</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Option Name</label>
                        <input type="text" name="name" class="form-control custom-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Price Adjustment</label>
                        <input type="number" step="0.01" name="adjustment" class="form-control custom-input" value="0">
                    </div>

                    <button type="submit" class="btn btn-menu-add">Add Option</button>
                </form>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="admin-product-list-card">
                <h3 class="mb-3">Existing Products</h3>

                <?php if (!$products): ?>
                    <div class="alert alert-info">No products found.</div>
                <?php else: ?>
                    <div class="admin-products-grid mb-4">
                        <?php foreach ($products as $product): ?>
                            <div class="admin-product-card">
                                <div class="admin-product-image-wrap">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="/cake_ordering/assets/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="admin-product-image">
                                    <?php else: ?>
                                        <div class="admin-product-placeholder">🎂</div>
                                    <?php endif; ?>
                                </div>

                                <div class="admin-product-body">
                                    <h4><?= htmlspecialchars($product['product_name']) ?></h4>
                                    <p><?= htmlspecialchars($product['description']) ?></p>

                                    <div class="admin-product-meta">
                                        <span class="product-price">₱<?= number_format($product['base_price'], 2) ?></span>
                                        <span class="admin-status-badge"><?= htmlspecialchars($product['availability_status']) ?></span>
                                    </div>

<div class="mt-3 d-flex align-items-center">
    <a href="/cake_ordering/admin/products.php?edit=<?= $product['product_id'] ?>" 
       class="btn btn-menu-secondary btn-sm">
       Edit
    </a>

    <a href="/cake_ordering/admin/products.php?delete=<?= $product['product_id'] ?>" 
       class="btn btn-menu-secondary btn-sm ms-auto"
       onclick="return confirm('Are you sure you want to delete this product?');">
       Delete
    </a>
</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 class="mb-3">Cake Options</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="option-box">
                            <h5>Designs</h5>
                            <?php foreach ($designs as $item): ?>
                                <div><?= htmlspecialchars($item['design_name']) ?> (+₱<?= number_format($item['design_price_adjustment'], 2) ?>)</div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="option-box">
                            <h5>Sizes</h5>
                            <?php foreach ($sizes as $item): ?>
                                <div><?= htmlspecialchars($item['size_name']) ?> (+₱<?= number_format($item['size_price_adjustment'], 2) ?>)</div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="option-box">
                            <h5>Flavors</h5>
                            <?php foreach ($flavors as $item): ?>
                                <div><?= htmlspecialchars($item['flavor_name']) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="option-box">
                            <h5>Fillings</h5>
                            <?php foreach ($fillings as $item): ?>
                                <div><?= htmlspecialchars($item['filling_name']) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="option-box">
                            <h5>Toppers</h5>
                            <?php foreach ($toppers as $item): ?>
                                <div><?= htmlspecialchars($item['topper_name']) ?> (+₱<?= number_format($item['topper_price_adjustment'], 2) ?>)</div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>
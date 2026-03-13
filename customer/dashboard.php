<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$stmt = $pdo->query("SELECT * FROM products WHERE availability_status = 'available' ORDER BY product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Sugar Delights</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand brand-logo" href="/cake_ordering/customer/dashboard.php">
           
            <span class="brand-text">Sugar Delights</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customerNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="customerNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link active" href="/cake_ordering/customer/dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/cake_ordering/customer/cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/cake_ordering/customer/orders.php">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/cake_ordering/customer/profile.php">Profile</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                <span class="customer-nav-name">Hi, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
                <a href="/cake_ordering/auth/logout.php" class="btn btn-nav-light">Logout</a>
            </div>
        </div>
    </div>
</nav>

<section class="hero-wrapper">
    <div class="container-fluid px-0">
        <div id="customerHeroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#customerHeroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#customerHeroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#customerHeroCarousel" data-bs-slide-to="2"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="hero-slide">
                        <div class="hero-split hero-left">
                            <div class="hero-badge">SWEET FAVORITES</div>
                            <h1>Discover your next favorite cake</h1>
                            <p>Choose from our available cakes and customize each order for your special celebration.</p>
                            <div class="hero-actions">
                                <a href="#menuSection" class="btn btn-hero-primary">Browse Menu</a>
                                <a href="/cake_ordering/customer/cart.php" class="btn btn-hero-secondary">View Cart</a>
                            </div>
                        </div>
                        <div class="hero-split hero-right">
                            <img src="/cake_ordering/assets/uploads/slide1.jpg" alt="Cake Slide 1" class="hero-img">
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="hero-slide">
                        <div class="hero-split hero-left">
                            <div class="hero-badge">POPULAR</div>
                            <h1>Beautiful cakes for every occasion</h1>
                            <p>Pick the design, size, flavor, filling, and topper that best matches your event.</p>
                            <div class="hero-actions">
                                <a href="#menuSection" class="btn btn-hero-primary">See Cakes</a>
                                <a href="/cake_ordering/customer/orders.php" class="btn btn-hero-secondary">My Orders</a>
                            </div>
                        </div>
                        <div class="hero-split hero-right">
                            <img src="/cake_ordering/assets/uploads/slide2.jpg" alt="Cake Slide 2" class="hero-img">
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="hero-slide">
                        <div class="hero-split hero-left">
                            <div class="hero-badge">DELIGHTFUL</div>
                            <h1>Order quickly and checkout easily</h1>
                            <p>Enjoy a cleaner ordering experience with cart, checkout, pickup, and delivery options.</p>
                            <div class="hero-actions">
                                <a href="#menuSection" class="btn btn-hero-primary">Start Ordering</a>
                                <a href="/cake_ordering/customer/profile.php" class="btn btn-hero-secondary">My Profile</a>
                            </div>
                        </div>
                        <div class="hero-split hero-right">
                            <img src="/cake_ordering/assets/uploads/slide3.jpg" alt="Cake Slide 3" class="hero-img">
                        </div>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#customerHeroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#customerHeroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>

<section class="products-preview-section py-5" id="menuSection">
    <div class="container">
        <div class="section-title text-center mb-4">
            <h2>Our Menu</h2>
            <p>Click a product to open its page and customize your order.</p>
        </div>

        <div class="row g-4">
            <?php if (!$products): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No products available yet.</div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="product-preview-card h-100 position-relative">
                            <div class="product-preview-image-wrap">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="/cake_ordering/assets/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-preview-image">
                                <?php else: ?>
                                    <div class="product-placeholder">🎂</div>
                                <?php endif; ?>
                            </div>

                            <div class="product-preview-body">
                                <h4><?= htmlspecialchars($product['product_name']) ?></h4>
                                <p><?= htmlspecialchars($product['description']) ?></p>

                                <div class="product-preview-footer">
                                    <span class="product-price">₱<?= number_format($product['base_price'], 2) ?></span>
                                    <a href="/cake_ordering/customer/product_view.php?product_id=<?= $product['product_id'] ?>" class="btn btn-product-card stretched-link">View Product</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
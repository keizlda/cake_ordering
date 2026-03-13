<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Ordering System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/cake_ordering/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container">
           <a class="navbar-brand brand-logo" href="/cake_ordering/">
    
    <span class="brand-text">Sugar Delights</span>
</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="/cake_ordering/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cake_ordering/customer/products.php">Menu</a>
                    </li>
                </ul>

                <div class="d-flex gap-2">
                    <a href="/cake_ordering/auth/login.php" class="btn btn-nav-light">Sign in</a>
                    <a href="/cake_ordering/auth/register.php" class="btn btn-nav-dark">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <section class="hero-wrapper">
        <div class="container-fluid px-0">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
                </div>

                <div class="carousel-inner">

                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="hero-slide">
                            <div class="hero-split hero-left">
                                <div class="hero-badge">NEW</div>
                                <h1>Delicious Cakes<br>for Every Celebration</h1>
                                <p>Order premium cakes with your preferred design, size, flavor, filling, and topper.</p>
                                <div class="hero-actions">
                                    <a href="/cake_ordering/auth/register.php" class="btn btn-hero-primary">Order Now</a>
                                    <a href="/cake_ordering/customer/products.php" class="btn btn-hero-secondary">Browse Cakes</a>
                                </div>
                            </div>
                            <div class="hero-split hero-right">
                                <img src="/cake_ordering/assets/uploads/slide1.jpg" alt="Cake Slide 1" class="hero-img">
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="hero-slide">
                            <div class="hero-split hero-left">
                                <div class="hero-badge">POPULAR</div>
                                <h1>Beautiful Designs,<br>Easy Ordering</h1>
                                <p>Create your order in a few clicks and manage everything from cart to checkout smoothly.</p>
                                <div class="hero-actions">
                                    <a href="/cake_ordering/auth/register.php" class="btn btn-hero-primary">Get Started</a>
                                    <a href="/cake_ordering/auth/login.php" class="btn btn-hero-secondary">Login</a>
                                </div>
                            </div>
                            <div class="hero-split hero-right">
                                <img src="/cake_ordering/assets/uploads/slide2.jpg" alt="Cake Slide 2" class="hero-img">
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="carousel-item">
                        <div class="hero-slide">
                            <div class="hero-split hero-left">
                                <div class="hero-badge">FAST</div>
                                <h1>Pickup or Delivery,<br>Your Choice</h1>
                                <p>Choose the most convenient option and enjoy a simpler, cleaner cake ordering experience.</p>
                                <div class="hero-actions">
                                    <a href="/cake_ordering/customer/products.php" class="btn btn-hero-primary">See Menu</a>
                                    <a href="/cake_ordering/auth/register.php" class="btn btn-hero-secondary">Create Account</a>
                                </div>
                            </div>
                            <div class="hero-split hero-right">
                                <img src="/cake_ordering/assets/uploads/slide3.jpg" alt="Cake Slide 3" class="hero-img">
                            </div>
                        </div>
                    </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Quick CTA Strip -->
    <section class="cta-strip">
        <div class="container">
            <div class="cta-box">
                <div>
                    <h3>Ready to order your cake?</h3>
                    <p>Customize your order and checkout in just a few steps.</p>
                </div>
                <a href="/cake_ordering/auth/register.php" class="btn btn-cta">Order Now</a>
            </div>
        </div>
    </section>

    <!-- Feature / Category Cards -->
    <section class="features-section">
        <div class="container">
            <div class="section-title text-center">
                <h2>Why customers choose us</h2>
                <p>Simple ordering, beautiful cakes, and convenient delivery options.</p>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🎨</div>
                        <h4>Custom Designs</h4>
                        <p>Choose from different designs, sizes, flavors, fillings, and toppers for every occasion.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🛒</div>
                        <h4>Easy Ordering</h4>
                        <p>Add items to cart, update quantities, checkout, and track your order in one place.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🚚</div>
                        <h4>Pickup or Delivery</h4>
                        <p>Choose pickup or delivery and complete your payment through the available payment options.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="bottom-cta">
        <div class="container text-center">
            <h2>Make every celebration sweeter</h2>
            <p>Start your order now and customize your cake the way you want it.</p>
            <a href="/cake_ordering/auth/register.php" class="btn btn-bottom-cta">Start Ordering</a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
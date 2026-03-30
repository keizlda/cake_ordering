<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.cart_id, ci.*, p.product_name
    FROM cart c
    JOIN cart_items ci ON c.cart_id = ci.cart_id
    JOIN products p ON ci.product_id = p.product_id
    WHERE c.user_id = ? AND c.cart_status = 'active'
    ORDER BY ci.cart_item_id DESC
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cartItems) {
    die("Your cart is empty.");
}

$stmt = $pdo->prepare("SELECT full_name, email, contact_number, address FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['subtotal'];
}

$delivery_fee = 49;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_method = sanitize($_POST['delivery_method']);
    $remarks = sanitize($_POST['remarks']);
    $payment_method = sanitize($_POST['payment_method']);
    $reference_number = sanitize($_POST['reference_number']);
    $address_option = $_POST['address_option'] ?? 'registered';

    if ($delivery_method === "Delivery") {
        if ($address_option === 'registered') {
            $delivery_address = sanitize($user['address']);
        } else {
            $delivery_address = sanitize($_POST['another_address'] ?? '');
        }
    } else {
        $delivery_address = 'Pickup - no delivery address needed';
    }

    $final_total = $total;
    if ($delivery_method === "Delivery") {
        $final_total += $delivery_fee;
    }

    if ($delivery_method === "Delivery" && empty($delivery_address)) {
        $message = "Please provide a delivery address.";
    } elseif (empty($payment_method)) {
        $message = "Please choose a payment method.";
    } elseif (empty($reference_number)) {
        $message = "Please enter your payment reference number.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmtCart = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ? AND cart_status = 'active' LIMIT 1");
            $stmtCart->execute([$user_id]);
            $cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

            if (!$cart) {
                throw new Exception("Active cart not found.");
            }

            $stmt = $pdo->prepare("
                INSERT INTO orders
                (user_id, total_amount, order_status, payment_status, delivery_method, remarks, payment_method, reference_number, delivery_address)
                VALUES (?, ?, 'Pending', 'Paid', ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $final_total,
                $delivery_method,
                $remarks,
                $payment_method,
                $reference_number,
                $delivery_address
            ]);

            $order_id = $pdo->lastInsertId();

            $detailStmt = $pdo->prepare("
                INSERT INTO order_details
                (order_id, product_id, design_id, size_id, flavor_id, filling_id, topper_id, quantity, unit_price, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($cartItems as $item) {
                $detailStmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['design_id'],
                    $item['size_id'],
                    $item['flavor_id'],
                    $item['filling_id'],
                    $item['topper_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['subtotal']
                ]);
            }

            $stmt = $pdo->prepare("UPDATE cart SET cart_status = 'checked_out' WHERE cart_id = ?");
            $stmt->execute([$cart['cart_id']]);

            $pdo->commit();

            header("Location: /cake_ordering/customer/order_success.php?order_id=" . $order_id);
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Checkout failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Sugar Delights</title>
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

<div class="checkout-page-wrapper py-5">
    <div class="container">
        <div class="checkout-layout">

            <div class="checkout-main-card">
                <h2 class="section-heading mb-4">Checkout</h2>

                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <div class="checkout-info-card mb-4">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Contact Number:</strong> <?= htmlspecialchars($user['contact_number']) ?></p>
                </div>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Delivery Method</label>
                        <select name="delivery_method" id="deliveryMethod" class="form-select custom-select" required>
                            <option value="Pickup">Pickup</option>
                            <option value="Delivery">Delivery</option>
                        </select>
                    </div>

                    <div class="checkout-info-card mb-4" id="deliveryAddressSection" style="display:none;">
                        <h5>Delivery Address</h5>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="address_option" id="registeredAddress" value="registered" checked>
                            <label class="form-check-label" for="registeredAddress">Use Registered Address</label>
                        </div>

                        <textarea class="form-control custom-input mb-3" rows="3" readonly><?= htmlspecialchars($user['address']) ?></textarea>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="address_option" id="anotherAddressOption" value="another">
                            <label class="form-check-label" for="anotherAddressOption">Add Another Address</label>
                        </div>

                        <textarea name="another_address" id="another_address" class="form-control custom-input" rows="3" placeholder="Enter another delivery address" disabled></textarea>
                    </div>

                    <div class="checkout-info-card mb-4">
                        <h5>Payment Option</h5>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="GCash" required>
                            <label class="form-check-label" for="gcash">GCash</label>
                        </div>

                        <div class="mb-3 checkout-qr-wrap">
                            <img src="/cake_ordering/assets/uploads/gcash.jpg" alt="GCash QR" class="checkout-qr-img">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="maya" value="Maya" required>
                            <label class="form-check-label" for="maya">GoTyme</label>
                        </div>

                        <div class="mb-3 checkout-qr-wrap">
                            <img src="/cake_ordering/assets/uploads/gotyme.jpg" alt="Maya QR" class="checkout-qr-img">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control custom-input" placeholder="Enter payment reference number" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control custom-input" rows="3"></textarea>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <button type="submit" class="btn btn-menu-add">Place Order</button>
                        <a href="cart.php" class="btn btn-menu-secondary">Back to Cart</a>
                    </div>

                </form>
            </div>

            <div class="checkout-summary-card">
                <h4>Order Summary</h4>

                <?php foreach ($cartItems as $item): ?>
                    <div class="summary-item">
                        <div>
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                            <div class="summary-qty">Qty: <?= $item['quantity'] ?></div>
                        </div>
                        <span>₱<?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                <?php endforeach; ?>

                <hr>

                <div class="summary-item">
                    <strong>Subtotal</strong>
                    <span>₱<span id="subtotalAmount"><?= number_format($total, 2) ?></span></span>
                </div>

                <div class="summary-item" id="deliveryFeeRow" style="display:none;">
                    <strong>Delivery Fee</strong>
                    <span>₱49.00</span>
                </div>

                <div class="summary-item total-row">
                    <strong>Total</strong>
                    <span>₱<span id="finalTotal"><?= number_format($total, 2) ?></span></span>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const deliveryMethod = document.getElementById("deliveryMethod");
const deliveryAddressSection = document.getElementById("deliveryAddressSection");
const deliveryFeeRow = document.getElementById("deliveryFeeRow");
const finalTotal = document.getElementById("finalTotal");
const baseTotal = <?= $total ?>;
const deliveryFee = 49;

const registeredRadio = document.getElementById("registeredAddress");
const anotherRadio = document.getElementById("anotherAddressOption");
const anotherAddress = document.getElementById("another_address");

function toggleAddressField() {
    if (anotherRadio.checked) {
        anotherAddress.disabled = false;
        anotherAddress.required = true;
    } else {
        anotherAddress.disabled = true;
        anotherAddress.required = false;
        anotherAddress.value = "";
    }
}

function updateDeliveryUI() {
    if (deliveryMethod.value === "Delivery") {
        deliveryAddressSection.style.display = "block";
        deliveryFeeRow.style.display = "flex";
        finalTotal.textContent = (baseTotal + deliveryFee).toFixed(2);
    } else {
        deliveryAddressSection.style.display = "none";
        deliveryFeeRow.style.display = "none";
        finalTotal.textContent = baseTotal.toFixed(2);
    }
}

deliveryMethod.addEventListener("change", updateDeliveryUI);
registeredRadio.addEventListener("change", toggleAddressField);
anotherRadio.addEventListener("change", toggleAddressField);

toggleAddressField();
updateDeliveryUI();
</script>

<!-- QR Zoom Modal -->
<div id="qrModal" class="qr-modal">
    <span class="qr-close">&times;</span>
    <img class="qr-modal-content" id="qrModalImg">
</div>

<script>
const modal = document.getElementById("qrModal");
const modalImg = document.getElementById("qrModalImg");
const closeBtn = document.querySelector(".qr-close");

document.querySelectorAll(".zoomable").forEach(img => {
    img.addEventListener("click", function () {
        modal.style.display = "block";
        modalImg.src = this.src;
    });
});

closeBtn.onclick = function () {
    modal.style.display = "none";
};

modal.onclick = function (e) {
    if (e.target !== modalImg) {
        modal.style.display = "none";
    }
};
</script>

</body>
</html>
<?php
require_once '../includes/auth.php';
requireLogin();
requireRole('customer');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND cart_status = 'active' LIMIT 1");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    die("No active cart found.");
}

$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ?");
$stmt->execute([$cart['cart_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cartItems) {
    die("Cart is empty.");
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['subtotal'];
}

$delivery_fee = 49;

$stmt = $pdo->prepare("SELECT full_name, email, contact_number, address FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $delivery_method = sanitize($_POST['delivery_method']);
    $remarks = sanitize($_POST['remarks']);
    $payment_method = sanitize($_POST['payment_method']);
    $reference_number = sanitize($_POST['reference_number']);
    $address_option = sanitize($_POST['address_option']);

    if ($address_option === 'registered') {
        $delivery_address = sanitize($user['address']);
    } else {
        $delivery_address = sanitize($_POST['another_address']);
    }

    if ($delivery_method === "Delivery") {
        $total += $delivery_fee;
    }

    if ($delivery_method === "Delivery" && empty($delivery_address)) {
        $message = "Please provide a delivery address.";
    }
    elseif (empty($payment_method)) {
        $message = "Please choose a payment method.";
    }
    elseif (empty($reference_number)) {
        $message = "Please enter your payment reference number.";
    }
    else {

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO orders
                (user_id, total_amount, order_status, payment_status, delivery_method, remarks, payment_method, reference_number, delivery_address)
                VALUES (?, ?, 'Pending', 'Paid', ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $total,
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

            redirect('/cake_ordering/customer/orders.php');

        } catch (Exception $e) {

            $pdo->rollBack();
            $message = "Checkout failed: " . $e->getMessage();

        }
    }
}

include '../includes/header.php';
?>

<h2>Checkout</h2>

<?php if ($message): ?>
<div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card mb-4">
<div class="card-body">
<h5>Customer Information</h5>
<p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
<p><strong>Contact Number:</strong> <?= htmlspecialchars($user['contact_number']) ?></p>
</div>
</div>

<div class="card mb-4">
<div class="card-body">

<h5>Order Total</h5>

<p>
<strong>Total Amount:</strong>
₱<span id="orderTotal"><?= number_format($total,2) ?></span>
</p>

<p id="deliveryFeeText" style="display:none;">
Delivery Fee: ₱49
</p>

</div>
</div>

<form method="POST">

<div class="mb-3">

<label class="form-label">Delivery Method</label>

<select name="delivery_method" id="deliveryMethod" class="form-control" required>

<option value="Pickup">Pickup</option>
<option value="Delivery">Delivery</option>

</select>

</div>


<div class="card mb-4" id="deliveryAddressSection" style="display:none;">
<div class="card-body">

<h5>Delivery Address</h5>

<div class="form-check mb-2">
<input class="form-check-input" type="radio" name="address_option" id="registeredAddress" value="registered" checked>
<label class="form-check-label">Use Registered Address</label>
</div>

<textarea class="form-control mb-3" rows="3" readonly><?= htmlspecialchars($user['address']) ?></textarea>

<div class="form-check mb-2">
<input class="form-check-input" type="radio" name="address_option" id="anotherAddressOption" value="another">
<label class="form-check-label">Add Another Address</label>
</div>

<textarea name="another_address" id="another_address" class="form-control" rows="3" placeholder="Enter another delivery address" disabled></textarea>

</div>
</div>


<div class="card mb-4">
<div class="card-body">

<h5>Payment Option</h5>

<div class="form-check mb-3">
<input class="form-check-input" type="radio" name="payment_method" value="GCash" required>
<label class="form-check-label">GCash</label>
</div>

<div class="mb-3">
<img src="/cake_ordering/assets/uploads/qr_gcash.png" style="max-width:250px;border:1px solid #ccc;padding:10px;">
</div>

<div class="form-check mb-3">
<input class="form-check-input" type="radio" name="payment_method" value="Maya" required>
<label class="form-check-label">Maya</label>
</div>

<div class="mb-3">
<img src="/cake_ordering/assets/uploads/qr_maya.png" style="max-width:250px;border:1px solid #ccc;padding:10px;">
</div>

<div class="mb-3">
<label>Reference Number</label>
<input type="text" name="reference_number" class="form-control" required>
</div>

</div>
</div>


<div class="mb-3">
<label>Remarks</label>
<textarea name="remarks" class="form-control"></textarea>
</div>

<button type="submit" class="btn btn-success">Place Order</button>
<a href="cart.php" class="btn btn-secondary">Back to Cart</a>

</form>


<script>

const deliveryMethod = document.getElementById("deliveryMethod");
const deliveryAddressSection = document.getElementById("deliveryAddressSection");
const deliveryFeeText = document.getElementById("deliveryFeeText");
const orderTotal = document.getElementById("orderTotal");

const baseTotal = <?= $total ?>;
const deliveryFee = 49;

function updateDeliveryUI(){

if(deliveryMethod.value === "Delivery"){

deliveryAddressSection.style.display="block";
deliveryFeeText.style.display="block";

orderTotal.textContent=(baseTotal + deliveryFee).toFixed(2);

}else{

deliveryAddressSection.style.display="none";
deliveryFeeText.style.display="none";

orderTotal.textContent=baseTotal.toFixed(2);

}

}

deliveryMethod.addEventListener("change",updateDeliveryUI);

updateDeliveryUI();


const registeredRadio = document.getElementById("registeredAddress");
const anotherRadio = document.getElementById("anotherAddressOption");
const anotherAddress = document.getElementById("another_address");

function toggleAddressField(){

if(anotherRadio.checked){
anotherAddress.disabled=false;
anotherAddress.required=true;
}else{
anotherAddress.disabled=true;
anotherAddress.required=false;
anotherAddress.value="";
}

}

registeredRadio.addEventListener("change",toggleAddressField);
anotherRadio.addEventListener("change",toggleAddressField);

toggleAddressField();

</script>

<?php include '../includes/footer.php'; ?>
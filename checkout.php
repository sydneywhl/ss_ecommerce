<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Checkout | SS_ECOMMERCE</title>
</head>
<body class="checkout-body">
    <header class="index-header">
        <div class="logo">
            <h1>SS E-Commerce</h1>
        </div>

        <form class="search-bar" action="" method="GET">
            <input type="text" name="search" placeholder="Search products...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <div class="header-icons">
            <a href="shopping_cart.php"><i class="fas fa-shopping-cart"></i></a>
            <a href="log_out.php"><i class="fas fa-user"></i></a>
        </div>
    </header>

    <main>
        <section class="checkout-section">

            <!-- Order items being purchased -->
            <div class="checkout-items" id="checkoutItems">
                <!-- injected by JS -->
            </div>

            <aside class="checkout-box">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">RM0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="shipping">RM0.00</span>
                </div>
                <div class="total-row">
                    <span>Total</span>
                    <span id="total">RM0.00</span>
                </div>
                <button class="pay-btn" id="payBtn">Pay</button>
                <a href="shopping_cart.php" class="back-btn">
                    <p>Go back</p>
                </a>
            </aside>

        </section>
    </main>

<?php
    session_start();
    include("connection.php");
    include("session_check.php");

    $load_user_id = "select user_id from users where username = '".$_SESSION['username']."'";
    $execute_user_id = mysqli_query($condb, $load_user_id);
    $user_row = mysqli_fetch_assoc($execute_user_id);
    $user_id = $user_row['user_id'];

    $load_shopping_cart = "select product.product_id, product.product_name, product.product_price,
                        product.product_img, cart_item.item_quantity
                        from cart_item, shopping_cart, users, product
                        where cart_item.cart_id = shopping_cart.cart_id
                        and users.user_id = shopping_cart.user_id
                        and cart_item.product_id = product.product_id
                        and shopping_cart.user_id = '$user_id'";
    $execute_shopping_cart = mysqli_query($condb, $load_shopping_cart);

    $cart_items_array = [];

    if (mysqli_num_rows($execute_shopping_cart) > 0){
        while ($n = mysqli_fetch_array($execute_shopping_cart)){
            $cart_items_array[] = array(
                'id' => (int)$n['product_id'],
                'name' => $n['product_name'],
                'price' => (float)$n['product_price'],
                'image' => $n['product_img'],
                'quantity' => (int)$n['item_quantity']
            );
        }
    }
?>

<script>
// ---- LOAD CART FROM db (same source as shopping_cart.php) ----
let cartItems = <?php echo json_encode($cart_items_array); ?>;

const SHIPPING_FEE = 5.00;

const checkoutItemsContainer = document.getElementById('checkoutItems');
const subtotalEl = document.getElementById('subtotal');
const shippingEl = document.getElementById('shipping');
const totalEl = document.getElementById('total');
const payBtn = document.getElementById('payBtn');

function renderCheckout() {
    if (cartItems.length === 0) {
        checkoutItemsContainer.innerHTML = `<p class="empty-cart">Your cart is empty.</p>`;
        payBtn.disabled = true;
        return;
    }

    checkoutItemsContainer.innerHTML = cartItems.map(item => `
        <div class="checkout-item">
            <img src="${item.image}" alt="${item.name}">
            <div class="checkout-item-info">
                <h4>${item.name}</h4>
                <p>Qty: ${item.quantity}</p>
            </div>
            <p class="item-total">RM${(item.price * item.quantity).toFixed(2)}</p>
        </div>
    `).join('');

    updateSummary();
}

function updateSummary() {
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const shipping = cartItems.length > 0 ? SHIPPING_FEE : 0;
    const total = subtotal + shipping;

    subtotalEl.textContent = `RM${subtotal.toFixed(2)}`;
    shippingEl.textContent = `RM${shipping.toFixed(2)}`;
    totalEl.textContent = `RM${total.toFixed(2)}`;
}

// ---- PAY BUTTON ----
payBtn.addEventListener('click', () => {
    fetch('clear_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Payment successful!');
            window.location.href = 'index.php';
        } else {
            alert('Something went wrong finishing your order.');
        }
    });
});

// ---- INITIAL RENDER ----
renderCheckout();
</script>
</body>
</html>
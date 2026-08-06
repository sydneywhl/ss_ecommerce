<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Shopping Cart | SS_ECOMMERCE</title>
</head>
<body class="cart-body">
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
        <section class="cart-section">
            <h2>Your Cart</h2>
            <div class="cart-layout">
                <!-- Cart items -->
                <div class="cart-items" id="cartItems">
                    <!-- cart items injected by JS -->
                </div>

                <!-- Order summary -->
                <aside class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">RM0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shipping">RM0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span id="total">RM0.00</span>
                    </div>
                    <button class="checkout-btn" id="checkoutBtn">Proceed to Checkout</button>
                    <a href="index.php" class="continue-shopping">Continue Shopping</a>
                </aside>
            </div>

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
let cartItems = <?php echo json_encode($cart_items_array); ?>;

const SHIPPING_FEE = 5.00;

const cartItemsContainer = document.getElementById('cartItems');
const subtotalEl = document.getElementById('subtotal');
const shippingEl = document.getElementById('shipping');
const totalEl = document.getElementById('total');
const checkoutBtn = document.getElementById('checkoutBtn');


function renderCart() {
    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = `<p class="empty-cart">Your cart is empty.</p>`;
        subtotalEl.textContent = "RM0.00";
        shippingEl.textContent = "RM0.00";
        totalEl.textContent = "RM0.00";
        checkoutBtn.disabled = true;
        return;
    }

    checkoutBtn.disabled = false;

    cartItemsContainer.innerHTML = cartItems.map(item => `
        <div class="cart-item" data-id="${item.id}">
            <img src="${item.image}" alt="${item.name}">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p class="item-price">RM${item.price.toFixed(2)}</p>
            </div>
            <div class="quantity-controls">
                <button class="qty-btn decrease" data-id="${item.id}">-</button>
                <span class="qty-value">${item.quantity}</span>
                <button class="qty-btn increase" data-id="${item.id}">+</button>
            </div>
            <p class="item-total">RM${(item.price * item.quantity).toFixed(2)}</p>
            <button class="remove-btn" data-id="${item.id}"><i class="fas fa-trash"></i></button>
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

// ---- EVENT HANDLING (delegated, since items are generated dynamically) ----
cartItemsContainer.addEventListener('click', (e) => {
    const id = Number(e.target.dataset.id || e.target.closest('[data-id]')?.dataset.id);
    if (!id) return;

    const item = cartItems.find(p => p.id === id);
    if (!item) return;

    if (e.target.classList.contains('increase')) {
        updateQuantity(id, 1);
    } else if (e.target.classList.contains('decrease')) {
        updateQuantity(id, -1);
    } else if (e.target.closest('.remove-btn')) {
        removeItem(id);
    }
});

function updateQuantity(productId, change) {
    fetch('update_cart_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, change: change })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Could not update cart.');
            return;
        }
        const item = cartItems.find(p => p.id === productId);
        if (data.removed) {
            cartItems = cartItems.filter(p => p.id !== productId);
        } else {
            item.quantity = data.new_quantity;
        }
        renderCart();
    });
}

function removeItem(productId) {
    fetch('remove_cart_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cartItems = cartItems.filter(p => p.id !== productId);
            renderCart();
        }
    });
}

checkoutBtn.addEventListener('click', () => {
    if (cartItems.length > 0) {
        window.location.href = 'checkout.php';
    }
});

// ---- INITIAL RENDER ----
renderCart();
</script>

    </section>
</main>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Shopping Cart</title>
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
            <a href="log_in.php"><i class="fas fa-user"></i></a>
        </div>
    </header>

    <main>
        <section class="cart-section">
            <h2>Your Cart</h2>
            <a href="index.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
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
                    <button class="checkout-btn">Proceed to Checkout</button>
                </aside>
            </div>

<script>
// ---- PLACEHOLDER DATA ----
// replace this with data fetched from the backend/session/database,
// e.g. const cartItems = await fetch('/api/cart').then(res => res.json());

let cartItems = [
    {
        id: 1,
        name: "Wireless Headphones",
        price: 49.99,
        image: "https://placehold.co/120x120",
        quantity: 1
    },
    {
        id: 2,
        name: "Cotton T-Shirt",
        price: 19.99,
        image: "https://placehold.co/120x120",
        quantity: 2
    },
    {
        id: 3,
        name: "Desk Lamp",
        price: 29.99,
        image: "https://placehold.co/120x120",
        quantity: 1
    }
];
const SHIPPING_FEE = 5.00;

const cartItemsContainer = document.getElementById('cartItems');
const subtotalEl = document.getElementById('subtotal');
const shippingEl = document.getElementById('shipping');
const totalEl = document.getElementById('total');

function renderCart() {
    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = `<p class="empty-cart">Your cart is empty.</p>`;
        subtotalEl.textContent = "RM0.00";
        shippingEl.textContent = "RM0.00";
        totalEl.textContent = "RM0.00";
        return;
    }

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
        item.quantity++;
    } else if (e.target.classList.contains('decrease')) {
        item.quantity--;
        if (item.quantity <= 0) {
            cartItems = cartItems.filter(p => p.id !== id);
        }
    } else if (e.target.closest('.remove-btn')) {
        cartItems = cartItems.filter(p => p.id !== id);
    }

    renderCart();
});

// ---- INITIAL RENDER ----
renderCart();
</script>

        </section>
    </main>
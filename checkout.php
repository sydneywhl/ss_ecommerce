<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Checkout</title>
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
            <a href="log_in.php"><i class="fas fa-user"></i></a>
        </div>
    </header>

    <main>
        <section class="checkout-section">

        <aside class="checkout-box">
        <h3>Order Summary</h3>
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
        <button class="pay-btn">Pay</button>
        <a href="shopping_cart.php" class="back-btn">
            <p>Go back</p>
        </a>
        </section>
    </main>
</body>
</html>
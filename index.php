<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Document</title>
</head>
<body class="index-body">
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
        <section class="products-section">
        <div class="products-layout">

            <!-- Sidebar will be generated here -->
            <aside class="category-sidebar">
                <h3>Categories</h3>
                <ul class="category-list" id="categoryList">
                <!-- categories injected by JS -->
                </ul>
            </aside>

            <!-- Products will be generated here -->
            <div class="product-grid" id="productGrid">
            <!-- products injected by JS -->
            </div>
        </div>
<script>
// ---- PLACEHOLDER DATA (matches your database column names) ----
// replace these with the actual API/database response,
// e.g. const products = await fetch('/api/products').then(res => res.json());

const categories = [
    { category_id: 0, category_name: "All" },
    { category_id: 1, category_name: "Electronics" },
    { category_id: 2, category_name: "Clothing" },
    { category_id: 3, category_name: "Home" }
];

const products = [
    {
        product_id: 1,
        product_name: "Wireless Headphones",
        product_price: 49.99,
        product_quantity: 25,
        product_img: "https://placehold.co/400x500",
        product_desc: "Comfortable over-ear wireless headphones with noise cancellation.",
        category_id: 1
    },
    {
        product_id: 2,
        product_name: "Cotton T-Shirt",
        product_price: 19.99,
        product_quantity: 100,
        product_img: "https://placehold.co/400x500",
        product_desc: "Soft, breathable 100% cotton t-shirt.",
        category_id: 2
    },
    {
        product_id: 3,
        product_name: "Desk Lamp",
        product_price: 29.99,
        product_quantity: 40,
        product_img: "https://placehold.co/400x500",
        product_desc: "Adjustable LED desk lamp with 3 brightness settings.",
        category_id: 3
    }
];

// ---- HELPER: look up a category's name from its id ----
function getCategoryName(category_id) {
    const match = categories.find(c => c.category_id === category_id);
    return match ? match.category_name : "Uncategorized";
}

// ---- RENDER CATEGORY SIDEBAR ----
const categoryList = document.getElementById('categoryList');

function renderCategories() {
    categoryList.innerHTML = categories.map(cat => `
        <li>
            <button class="category-btn ${cat.category_id === 0 ? 'active' : ''}" data-category="${cat.category_id}">
                ${cat.category_name}
            </button>
        </li>
    `).join('');
}

// ---- RENDER PRODUCT GRID ----
const productGrid = document.getElementById('productGrid');

function renderProducts(filterId = 0) {
    const filtered = filterId === 0
        ? products
        : products.filter(p => p.category_id === Number(filterId));

    productGrid.innerHTML = filtered.map(p => `
        <div class="product-card" data-category="${p.category_id}">
            <div class="product-image">
                <img src="${p.product_img}" alt="${p.product_name}">
            </div>
            <div class="product-info">
                <span class="category-tag">${getCategoryName(p.category_id)}</span>
                <h3>${p.product_name}</h3>
                <p class="price">RM${p.product_price.toFixed(2)}</p>
            </div>
        </div>
    `).join('');
}

// ---- EVENT HANDLING ----
categoryList.addEventListener('click', (e) => {
    if (!e.target.classList.contains('category-btn')) return;

    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    renderProducts(e.target.dataset.category);
});

// ---- INITIAL RENDER ----
renderCategories();
renderProducts();

</script>

        </section>
    </main>
</body>
</html> 
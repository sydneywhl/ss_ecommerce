<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>SS_ECOMMERCE</title>
</head>
<body class="index-body">
    <header class="index-header">
        <div class="logo">
            <h1>SS E-Commerce</h1>
        </div>
        
        <form class="search-bar" id="searchForm" action="" method="GET">
            <input type="text" name="search" id="searchInput" placeholder="Search products...">
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

            <!-- Product Detail Modal Container -->
            <div id="productModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close-btn" id="closeModal">&times;</span>
                    <div id="modalDetails"></div>   
                </div>
            </div>
        </section> <!-- Fixed: Shifted section tag to properly wrap the layout elements -->
    </main>

<?php
    session_start();
    include("connection.php");
    include("session_check.php");

    # load all data from the product table in the database
    $load_product_data = "select product_id,product_name,product_price,product_quantity,
                    product_img,product_desc,product.category_id, category.category_name
                    from product, category
                    where product.category_id = category.category_id";
        
    $execute_load = mysqli_query($condb, $load_product_data);

    if(mysqli_num_rows($execute_load)>0){
        while ($n = mysqli_fetch_array($execute_load)){
            $products_array[]=array(
                'product_id' => (int)$n['product_id'],
                'product_name' => $n['product_name'],
                'product_price' => (float)$n['product_price'],
                'product_quantity' => (int)$n['product_quantity'],
                'product_img' => $n['product_img'],
                'product_desc' => $n['product_desc'],
                'category_id' => (int)$n['category_id'],
                'category_name' => $n['category_name']
            );
        }
    }
    else{
        echo "No products found";
    }

# ----------------------------------------------------------------
    # load all categories from category table
    $categories_array = [];
    # this is for loading all products regardless of category, UI-based (not in db)
    $categories_array[] = array(
    'category_id' => 0,
    'category_name' => "All"
    );
    
    $load_categories = "select * from category";
    $execute_cat = mysqli_query($condb, $load_categories);

    if(mysqli_num_rows($execute_cat)>0){
        while ($n = mysqli_fetch_array($execute_cat)){
            $categories_array[]=array(
                'category_id' => (int)$n['category_id'],
                'category_name' => $n['category_name']
            );
        }
    }
?>

<script>
// ---- PLACEHOLDER DATA ----
const categories = <?php echo json_encode($categories_array); ?>

const products = <?php echo json_encode($products_array); ?>;

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
    const currentFilter = Number(filterId);

    const filtered = currentFilter === 0
        ? products
        : products.filter(p => p.category_id === currentFilter);

    productGrid.innerHTML = filtered.map(p => `
        <!-- FIXED: Added data-id="${p.product_id}" so the click listener can find the product -->
        <div class="product-card" data-id="${p.product_id}" data-category="${p.category_id}" style="cursor: pointer;">
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

// ---- SEARCH FUNCTIONALITY ----
const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('searchInput');

function renderProductsBySearch(query) {
    const lowerQuery = query.toLowerCase().trim();

    const filtered = lowerQuery === ''
        ? products
        : products.filter(p => p.product_name.toLowerCase().includes(lowerQuery));

    productGrid.innerHTML = filtered.map(p => `
        <div class="product-card" data-id="${p.product_id}" data-category="${p.category_id}" style="cursor: pointer;">
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

    if (filtered.length === 0) {
        productGrid.innerHTML = `<p class="no-results">No products found for "${query}"</p>`;
    }
}

searchForm.addEventListener('submit', (e) => {
    e.preventDefault(); // stop page reload

    // reset category filter to "All" so search checks the whole catalog
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.category-btn[data-category="0"]').classList.add('active');

    renderProductsBySearch(searchInput.value);
});

// optional: live search as they type, instead of waiting for submit
searchInput.addEventListener('input', () => {
    renderProductsBySearch(searchInput.value);
});

// ---- MODAL & ACTION VARIABLES ----
const productModal = document.getElementById('productModal');
const modalDetails = document.getElementById('modalDetails');
const closeModal = document.getElementById('closeModal');

// ---- OPEN MODAL ----
function openProductModal(product) {
    modalDetails.innerHTML = `
        <div class="modal-flex">
            <img src="${product.product_img}" alt="${product.product_name}">
            <div class="modal-info">
                <h2>${product.product_name}</h2>
                <p class="modal-price">RM${product.product_price.toFixed(2)}</p>
                <p class="modal-desc">${product.product_desc || 'No description available.'}</p>
                
                <form action="shopping_cart.php" method="POST"> ***
                    <input type="hidden" name="product_id" value="${product.product_id}">
                    <input type="hidden" name="action" value="add">
                    <button type="button" class="add-to-cart-btn" onclick="addToCart(${product.product_id})">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    `;
    productModal.style.display = 'block';
}

// ---- INTERACTION LISTENERS ----
productGrid.addEventListener('click', (e) => {
    const card = e.target.closest('.product-card');
    if (!card) return;

    const productId = Number(card.dataset.id);
    const selectedProduct = products.find(p => p.product_id === productId);

    if (selectedProduct) {
        openProductModal(selectedProduct);
    }
});

closeModal.addEventListener('click', () => productModal.style.display = 'none');
window.addEventListener('click', (e) => { 
    if (e.target === productModal) productModal.style.display = 'none'; 
});

categoryList.addEventListener('click', (e) => {
    if (!e.target.classList.contains('category-btn')) return;

    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    renderProducts(e.target.dataset.category);
});

// ---- CART ACTION EXECUTOR ----
function addToCart(productId) {
    // Placeholder check — replace with real session/login check later
    const isLoggedIn = false; // your friend will replace this with a real PHP session check

    if (!isLoggedIn) {
        alert('Please log in to add items to your cart.');
        window.location.href = 'log_in.php';
        return;
    }
    
    const product = products.find(p => p.product_id === productId);
    if (!product) return;

    // Get existing cart from localStorage, or start a new one
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Check if this product is already in the cart
    const existingItem = cart.find(item => item.id === product.product_id);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: product.product_id,
            name: product.product_name,
            price: product.product_price,
            image: product.product_img,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));

    alert(`${product.product_name} added to cart!`);
    productModal.style.display = 'none'; // close modal after adding
}

// ---- RUN ----
renderCategories();
renderProducts();
</script>
</body>
</html>

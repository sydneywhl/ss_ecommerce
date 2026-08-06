<?php
session_start();
include("connection.php");
include("session_check.php");

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Read the JSON body sent by fetch()
$input = json_decode(file_get_contents('php://input'), true);
$product_id = (int)$input['product_id'];
$quantity = (int)($input['quantity'] ?? 1);

// Find the user_id
$load_user_id = "select user_id from users where username = '".$_SESSION['username']."'";
$execute_user_id = mysqli_query($condb, $load_user_id);
$user_row = mysqli_fetch_assoc($execute_user_id);
$user_id = $user_row['user_id'];

// Find or create their cart
$find_existing_cart = "select * from shopping_cart where user_id = '$user_id'";
$execute_find_existing_cart = mysqli_query($condb, $find_existing_cart);

if (mysqli_num_rows($execute_find_existing_cart) == 1) {
    $cart_row = mysqli_fetch_assoc($execute_find_existing_cart);
    $cart_id = $cart_row['cart_id'];
} else {
    $create_new_cart = "insert into shopping_cart (`user_id`) values ('$user_id')";
    mysqli_query($condb, $create_new_cart);
    $cart_id = mysqli_insert_id($condb); // grab the id we just created
}

// Check if this product is already in the cart
$find_item = "select * from cart_item where cart_id = '$cart_id' and product_id = '$product_id'";
$execute_find_item = mysqli_query($condb, $find_item);

if (mysqli_num_rows($execute_find_item) == 1) {
    // reduce stock BEFORE updating cart_item, so a failed guard doesn't leave things inconsistent
    $reduce_stock = "update product set product_quantity = product_quantity - $quantity
                    where product_id = '$product_id' and product_quantity >= $quantity";
    mysqli_query($condb, $reduce_stock);

    if (mysqli_affected_rows($condb) === 0) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    $update_item = "update cart_item set item_quantity = item_quantity + $quantity
                    where cart_id = '$cart_id' and product_id = '$product_id'";
    mysqli_query($condb, $update_item);
} else {
    $reduce_stock = "update product set product_quantity = product_quantity - $quantity
                    where product_id = '$product_id' and product_quantity >= $quantity";
    mysqli_query($condb, $reduce_stock);

    if (mysqli_affected_rows($condb) === 0) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    $insert_item = "insert into cart_item (`cart_id`, `product_id`, `item_quantity`)
                    values ('$cart_id', '$product_id', '$quantity')";
    mysqli_query($condb, $insert_item);
}

echo json_encode(['success' => true]);
<?php
session_start();
include("connection.php");
include("session_check.php");

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = (int)$input['product_id'];

$load_user_id = "select user_id from users where username = '".$_SESSION['username']."'";
$execute_user_id = mysqli_query($condb, $load_user_id);
$user_row = mysqli_fetch_assoc($execute_user_id);
$user_id = $user_row['user_id'];

$find_cart = "select cart_id from shopping_cart where user_id = '$user_id'";
$execute_find_cart = mysqli_query($condb, $find_cart);
$cart_row = mysqli_fetch_assoc($execute_find_cart);
$cart_id = $cart_row['cart_id'];

// Find how many were in the cart, so we know how much stock to restore
$find_item = "select item_quantity from cart_item where cart_id = '$cart_id' and product_id = '$product_id'";
$execute_find_item = mysqli_query($condb, $find_item);
$item_row = mysqli_fetch_assoc($execute_find_item);

if ($item_row) {
    $restored_quantity = (int)$item_row['item_quantity'];

    $delete_item = "delete from cart_item where cart_id = '$cart_id' and product_id = '$product_id'";
    mysqli_query($condb, $delete_item);

    $restore_stock = "update product set product_quantity = product_quantity + $restored_quantity
                    where product_id = '$product_id'";
    mysqli_query($condb, $restore_stock);
}

echo json_encode(['success' => true]);
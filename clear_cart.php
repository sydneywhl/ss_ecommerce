<?php
session_start();
include("connection.php");
include("session_check.php");

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$load_user_id = "select user_id from users where username = '".$_SESSION['username']."'";
$execute_user_id = mysqli_query($condb, $load_user_id);
$user_row = mysqli_fetch_assoc($execute_user_id);
$user_id = $user_row['user_id'];

$find_cart = "select cart_id from shopping_cart where user_id = '$user_id'";
$execute_find_cart = mysqli_query($condb, $find_cart);
$cart_row = mysqli_fetch_assoc($execute_find_cart);
$cart_id = $cart_row['cart_id'];

$clear_items = "delete from cart_item where cart_id = '$cart_id'";
mysqli_query($condb, $clear_items);

echo json_encode(['success' => true]);
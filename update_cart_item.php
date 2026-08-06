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
$change = (int)$input['change']; // +1 or -1

$load_user_id = "select user_id from users where username = '".$_SESSION['username']."'";
$execute_user_id = mysqli_query($condb, $load_user_id);
$user_row = mysqli_fetch_assoc($execute_user_id);
$user_id = $user_row['user_id'];

$find_cart = "select cart_id from shopping_cart where user_id = '$user_id'";
$execute_find_cart = mysqli_query($condb, $find_cart);
$cart_row = mysqli_fetch_assoc($execute_find_cart);
$cart_id = $cart_row['cart_id'];

$find_item = "select item_quantity from cart_item where cart_id = '$cart_id' and product_id = '$product_id'";
$execute_find_item = mysqli_query($condb, $find_item);
$item_row = mysqli_fetch_assoc($execute_find_item);

if (!$item_row) {
    echo json_encode(['success' => false, 'message' => 'Item not in cart']);
    exit;
}

$new_quantity = (int)$item_row['item_quantity'] + $change;

if ($new_quantity <= 0) {
    // Removing the last unit — delete the row and restore full stock
    $delete_item = "delete from cart_item where cart_id = '$cart_id' and product_id = '$product_id'";
    mysqli_query($condb, $delete_item);

    $restore_stock = "update product set product_quantity = product_quantity + " . (int)$item_row['item_quantity'] . "
                    where product_id = '$product_id'";
    mysqli_query($condb, $restore_stock);

    echo json_encode(['success' => true, 'removed' => true]);
    exit;
}

if ($change < 0) {
    // Decreasing, but not to zero — restore the difference to stock
    $update_item = "update cart_item set item_quantity = $new_quantity
                    where cart_id = '$cart_id' and product_id = '$product_id'";
    mysqli_query($condb, $update_item);

    $restore = abs($change);
    $restore_stock = "update product set product_quantity = product_quantity + $restore
                    where product_id = '$product_id'";
    mysqli_query($condb, $restore_stock);

    echo json_encode(['success' => true, 'removed' => false, 'new_quantity' => $new_quantity]);
    exit;
}

// Increasing — reduce stock, but only if enough is available
$adjust_stock = "update product set product_quantity = product_quantity - $change
                where product_id = '$product_id' and product_quantity >= $change";
mysqli_query($condb, $adjust_stock);

if (mysqli_affected_rows($condb) === 0) {
    // not enough stock — do NOT change the cart quantity at all
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

$update_item = "update cart_item set item_quantity = $new_quantity
                where cart_id = '$cart_id' and product_id = '$product_id'";
mysqli_query($condb, $update_item);

echo json_encode(['success' => true, 'removed' => false, 'new_quantity' => $new_quantity]);
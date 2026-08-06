<?php

session_start();

#checks if there is POST
if(!empty($_POST)){
    include('connection.php');

    $check_username = "select user_id from users where username = '".$_POST['username']."'";
    $execute_check = mysqli_query($condb, $check_username);

    if (mysqli_num_rows($execute_check) > 0) {
        die("<script>alert('That username is already taken.');
        window.history.back();</script>");
    }

$new_user = "insert into users (username, password) values ('".$_POST['username']."','".$_POST['password']."')";
$execute_query_user = mysqli_query($condb, $new_user);

    #if password is not between 8 to 20 characters then return to register
    $passLen = strlen($_POST['password']);
    if ($passLen < 8 || $passLen > 20) {
        die("<script>alert('Password must be between 8 and 20 characters.');
        window.history.back();</script>");
    }

    #saving new user's data
    $new_user = "insert into users (username, password)
    values ('".$_POST['username']."','".$_POST['password']."')";

    #executing the query
    $execute_query_user = mysqli_query($condb, $new_user);

    if ($execute_query_user){
        $_SESSION['username'] = $_POST['username'];
        echo "<script>alert('User successfully registered!');
        window.location.href='log_in.php';</script>";
    }
    else {
        // mysqli_errno 1062 = duplicate entry for a unique key
        if (mysqli_errno($condb) === 1062) {
            echo "<script>alert('That username is already taken. Please choose another.');
            window.location.href = 'create_account.php';</script>";
        } else {
            echo "<script>alert('Registration failed');
            window.location.href = 'create_account.php';</script>";
        }
    }
}

#if there is empty POST
else{
    echo"<script>alert('Please fill in all text fields');
    window.location.href='create_account.php';</script>";
}
?>
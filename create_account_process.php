<?php

session_start();

#checks if there is POST
if(!empty($_POST)){
    include('connection.php');

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
    $execute_query_user=mysqli_query($condb, $new_user);

    #(WIP)
    if ($execute_query_user){
        $_SESSION['username'] = $_POST['username'];
        echo "<script>alert('User successfully registered!');
        window.location.href='log_in.php';</script>";
        }
    
    else{
        echo"<script>alert('Registration failed');
        window.location.href = 'create_account.php';</script>";
    }
}

#if there is empty POST
else{
    echo"<script>alert('Please fill in all text fields');
    window.location.href='create_account.php';</script>";
}
?>
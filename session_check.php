<?php

include('connection.php');

$check_user = "select username from user
where username = '".$_SESSION['username']."'";

$execute_query = mysqli_query($condb, $check_user);
    
if(mysqli_num_rows($execute_query)!=1)
{
    die("<script>alert('Please log in.'));
    window.location.href='log_out.php';</script>");
}

?>
<?php
session_start();

if (!empty($_POST['username']) and !empty($_POST['password']))
{
    include ('connection.php');

    $query_login_user = "select * from users
    where username = '".$_POST['username']."'
    and password = '".$_POST['password']."'";

    $execute_query_user = mysqli_query(mysql:$condb, query:$query_login_user);

    if (mysqli_num_rows(result:$execute_query_user)==1)
    {
        $m = mysqli_fetch_array(result: $execute_query_user);
        $_SESSION['username'] = $_POST['username'];
        echo "<script>window.location.href = 'index.php';</script>";
    }

    else
    {
        #echo"<script>alert('Wrong log in details. Please try again');
        #window.history.back(); </script>";
        header("Location:log_in.php?error=1");
        exit();
    }
    

}
else
{
    echo "<script>alert('Please enter your username and password');
    window.location.href = 'log_in.php'</script>";
}

?>

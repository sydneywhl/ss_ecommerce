<?php

#establishing connection between database and php

$hostname = "localhost";
$sqlname = "root";
$sqlpass = "";

#database name
$dbname = "ss_ecommerce";

#connection to database
$condb = mysqli_connect($hostname,$sqlname,$sqlpass,$dbname);

#if connection is unsuccessful, following messages will be displayed
if (!$condb)
{
    die("Connection to database failed.");
}

# this is a temporary chunk of code for testing in order to test if a connection is made
else
{
    #echo("Connection to database successful.");
}

?>
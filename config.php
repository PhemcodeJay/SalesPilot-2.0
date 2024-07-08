<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "salespilot";


$connection = mysqli_connect($hostname, $username, $password, $database);

if (!$connection) {
    exit("Error: " . mysqli_connect_error());
}
?>

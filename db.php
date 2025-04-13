<?php
session_start();
$servername = "localhost";    // or 127.0.0.1
$username = "root";           // default XAMPP username
$password = "";               // default XAMPP password (empty)
$dbname = "auction";      // your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

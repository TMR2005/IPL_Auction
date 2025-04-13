<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "auction"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $password = $_POST['password'];  // Storing password as plain text (Not Secure!)

    // Prevent SQL Injection
    $username = $conn->real_escape_string($username);
    $dob = $conn->real_escape_string($dob);
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    // Insert user into database
    $sql = "INSERT INTO users (username, dob, email, password) VALUES ('$username', '$dob', '$email', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!'); window.location='login.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

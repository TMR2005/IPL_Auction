<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "auction";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login request
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = $_POST["username"];
    $password = $_POST["password"];

    // ❗️ Use prepared statements to avoid SQL injection (recommended)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password); // Bind input
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Set session variables
        $_SESSION["username"] = $user["username"];
        $_SESSION["userid"] = $user["userid"];

        // ✅ Debug: Uncomment if needed
        echo "Login successful. User ID: " . $_SESSION["userid"];

        // Redirect to dashboard
        header("Location: dashboard.html");
        exit();
    } else {
        echo "<script>alert('Invalid username or password');</script>";
        echo "<script>window.location.href='login.html';</script>";
    }
}

$conn->close();
?>

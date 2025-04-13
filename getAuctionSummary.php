<?php
// getAuctionSummary.php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "auction";

// Check if session variable exists
if (!isset($_SESSION['current_players'])) {
  die(json_encode(["error" => "Auction table not found in session."]));
}

$tableName = $_SESSION['current_players'];

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Escape the table name to prevent SQL injection
$tableNameSafe = $conn->real_escape_string($tableName);
$sql = "SELECT * FROM `$tableNameSafe`";  // Backticks ensure it works with special characters

$result = $conn->query($sql);

$players = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $players[] = $row;
  }
  echo json_encode($players);
} else {
  echo json_encode(["error" => "Query failed: " . $conn->error]);
}

$conn->close();
?>

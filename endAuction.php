<?php
session_start();
require 'db_connection.php'; 

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(["error" => "User not logged in"]);
  exit;
}

$userId = $_SESSION['userid'];

$sql = "UPDATE users SET no_of_auctions = no_of_auctions + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "Auction ended and count updated"]);
} else {
  echo json_encode(["success" => false, "message" => "Database error"]);
}

$stmt->close();
$conn->close();
?>

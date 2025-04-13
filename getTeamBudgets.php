<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['current_teams'])) {
  echo json_encode(["error" => "Auction session not initialized"]);
  exit;
}

$teamsTable = $_SESSION['current_teams'];

$query = "SELECT team_name, budget FROM `$teamsTable`";
$result = $conn->query($query);

if (!$result) {
  echo json_encode(["error" => "Failed to fetch team budgets"]);
  exit;
}

$budgets = [];
while ($row = $result->fetch_assoc()) {
  $budgets[$row['team_name']] = (int)$row['budget'];
}

echo json_encode($budgets);
?>

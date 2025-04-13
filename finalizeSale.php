<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set("display_errors", 1);
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['playerId']) || !isset($data['team']) || !isset($data['amount'])) {
  http_response_code(400);
  echo "Invalid request.";
  exit;
}

$playerId = $data['playerId'];
$team = $data['team'];
$amount = $data['amount'];

if (!isset($_SESSION['current_players']) || !isset($_SESSION['current_teams'])) {
  http_response_code(400);
  echo "Auction session not initialized.";
  exit;
}

$playersTable = $_SESSION['current_players'];
$teamsTable = $_SESSION['current_teams'];

// ✅ Step 1: Fetch player
$stmt = $conn->prepare("SELECT * FROM players WHERE S_No = ?");
$stmt->bind_param("i", $playerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  http_response_code(404);
  echo "Player not found.";
  exit;
}

$player = $result->fetch_assoc();
$playerName = $player['First Name'] . " " . $player['Last Name'];

// ✅ Step 2: Adjust price if UNSOLD
if (strtoupper($team) === "UNSOLD") {
  $amount = $player['Reserve Price'];
}

// ✅ Step 3: Insert into current_players table
$insert = $conn->prepare("
  INSERT INTO `$playersTable` (
    player_id, player_name, team, amount, role, base_price, country,
    batting_style, bowling_style, ipl_matches, capped_status
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$insert->bind_param(
  "ississsssis",
  $playerId,
  $playerName,
  $team,
  $amount,
  $player['Specialism'],
  $player['Reserve Price'],
  $player['Country'],
  $player['Batting Style'],
  $player['Bowling Style'],
  $player['IPL Matches'],
  $player['Capped Status']
);

if ($insert->execute()) {
  echo "✅ Player sale recorded in $playersTable.";
} else {
  http_response_code(500);
  echo "❌ Error inserting player: " . $insert->error;
}

$insert->close();


  $updateTeam = $conn->prepare("
    UPDATE `$teamsTable`
    SET total_spent = total_spent + ?, players_count = players_count + 1, budget = budget - ?
    WHERE team_name = ?
  ");
  $updateTeam->bind_param("iis", $amount, $amount, $team);
  if ($updateTeam->execute()) {
    echo " ✅ Team $team updated in $teamsTable.";
  } else {
    echo " ❌ Error updating team: " . $updateTeam->error;
  }
  $updateTeam->close();

?>

<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "auction";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Step 1: Generate unique table names
$timestamp = time();
$playersTable = "current_players_" . $timestamp;
$teamsTable = "current_teams_" . $timestamp;

// Step 2: Store in session
if (!isset($_SESSION['userid'])) {
    die("User not logged in.");
}
$userId = $_SESSION['userid'];
$_SESSION['current_players'] = $playersTable;
$_SESSION['current_teams'] = $teamsTable;

// Step 3: Create current_players table
$sql1 = "CREATE TABLE `$playersTable` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  player_id INT NOT NULL,
  player_name VARCHAR(255) NOT NULL,
  team VARCHAR(100) NOT NULL,
  amount INT NOT NULL,
  role VARCHAR(100),
  base_price INT,
  country VARCHAR(100),
  batting_style VARCHAR(100),
  bowling_style VARCHAR(100),
  ipl_matches INT,
  capped_status VARCHAR(20)
)";
if (!$conn->query($sql1)) {
    die("❌ Error creating players table: " . $conn->error);
}

// Step 4: Create current_teams table
$sql2 = "CREATE TABLE `$teamsTable` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team_name VARCHAR(255) NOT NULL,
  budget INT NOT NULL,
  players_count INT DEFAULT 0,
  total_spent INT DEFAULT 0
)";
if (!$conn->query($sql2)) {
    die("❌ Error creating teams table: " . $conn->error);
}

// Step 5: Insert 10 IPL teams with initial 12000L budget
$teams = [
  'CSK',
  'MI',
  'RCB',
  'KKR',
  'RR',
  'SRH',
  'DC',
  'PBKS',
  'LSG',
  'GT'
];

$insertQuery = $conn->prepare("INSERT INTO `$teamsTable` (team_name, budget) VALUES (?, 12000)");
foreach ($teams as $teamName) {
    $insertQuery->bind_param("s", $teamName);
    if (!$insertQuery->execute()) {
        die("❌ Failed to insert team $teamName: " . $insertQuery->error);
    }
}
$insertQuery->close();

// Step 6: Record the session
$recordSession = $conn->prepare("INSERT INTO auction_sessions(user_id, table_name, created_at) VALUES (?, ?, NOW())");
$recordSession->bind_param("is", $userId, $playersTable);
if ($recordSession->execute()) {
    echo "✅ Tables '$playersTable' and '$teamsTable' created. Session recorded successfully.";
} else {
    echo "❌ Error recording session: " . $recordSession->error;
}
$recordSession->close();

$conn->close();
?>

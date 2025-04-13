<?php
require 'db.php'; // Adjust the path if needed

header('Content-Type: application/json');

$response = [];
$tableName = $_SESSION['current_players'];

// Fetch all players and their assigned teams
$soldQuery = "SELECT player_name AS name, team FROM `$tableName` WHERE team != 'UNSOLD' ORDER BY player_name";
$soldResult = $conn->query($soldQuery);

$soldPlayers = [];
if ($soldResult && $soldResult->num_rows > 0) {
    while ($row = $soldResult->fetch_assoc()) {
        $soldPlayers[] = $row;
    }
}

$response['soldPlayers'] = $soldPlayers;

echo json_encode($response);
$conn->close();
?>

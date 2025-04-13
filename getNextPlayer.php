<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if (!isset($_SESSION['current_players'])) {
    echo json_encode(["error" => "No active auction session found."]);
    exit;
}

$currentTable = $_SESSION['current_players'];

// Query to get a random player NOT in current_players (i.e. unsold)
$sql = "
    SELECT * FROM players 
    WHERE S_No NOT IN (
        SELECT player_id FROM `$currentTable`
    ) 
    ORDER BY RAND() 
    LIMIT 1
";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $player = mysqli_fetch_assoc($result);

    echo json_encode([
        "id" => $player["S_No"],
        "name" => $player["First Name"] . " " . $player["Last Name"],
        "country" => $player["Country"],
        "role" => $player["Specialism"],
        "batting_style" => $player["Batting Style"],
        "bowling_style" => $player["Bowling Style"],
        "ipl_matches" => $player["IPL Matches"],
        "capped_status" => $player["Capped Status"],
        "base_price" => $player["Reserve Price"]
    ]);
} else {
    echo json_encode(["error" => "No unsold players found."]);
}


?>

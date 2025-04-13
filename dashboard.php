<?php
header('Content-Type: application/json');
include("db.php");

if (!isset($_SESSION['userid'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['userid'];

// Fetch user info
$user_stmt = $conn->prepare("SELECT username FROM users WHERE userid = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch user's auction sessions
$auction_stmt = $conn->prepare("SELECT id, table_name FROM auction_sessions WHERE user_id = ?");
$auction_stmt->bind_param("i", $user_id);
$auction_stmt->execute();
$auction_result = $auction_stmt->get_result();

$auctions = [];
$recent_auctions = [];

while ($row = $auction_result->fetch_assoc()) {
    $table_name = $conn->real_escape_string($row['table_name']);
    $auction_id = $row['id'];

    // ✅ Check if the auction table exists
    $check = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($check->num_rows === 0) continue;

    // ✅ Fetch top 5 sold players from this auction table
    $players_sql = "SELECT player_name, team, amount FROM `$table_name` WHERE team != 'UNSOLD' ORDER BY amount DESC LIMIT 5";
    $players_result = $conn->query($players_sql);

    while ($player = $players_result->fetch_assoc()) {
        $recent_auctions[] = [
            "auction_id" => $auction_id,
            "player_name" => $player['player_name'],
            "team" => $player['team'],
            "amount" => $player['amount']
        ];
    }

    $auctions[] = $auction_id;
}

echo json_encode([
    "username" => $user['username'],
    "no_of_auctions" => count($auctions),
    "recent_auctions" => $recent_auctions
]);

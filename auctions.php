<?php
session_start();
include("db_connection.php");

if (!isset($_GET['table'])) {
    die("Missing table name.");
}

$table = $_GET['table'];

// Sanitize table name to prevent injection
if (!preg_match('/^current_players_\d+$/', $table)) {
    die("Invalid table name.");
}

$result = $conn->query("SELECT * FROM `$table` ORDER BY amount DESC");

echo "<h1>Auction Details: $table</h1><table border='1'><tr>
<th>Player</th><th>Team</th><th>Amount (₹)</th><th>Role</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['player_name']}</td>
        <td>{$row['team']}</td>
        <td>{$row['amount']}</td>
        <td>{$row['role']}</td>
    </tr>";
}
echo "</table><br><a href='dashboard.html'>🔙 Back to Dashboard</a>";

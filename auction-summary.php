<?php
include("db.php");

if (!isset($_GET['auction_id'])) {
    echo "Auction ID is missing.";
    exit;
}

$auction_id = $_GET['auction_id'];

// Fetch auction details
$auction_stmt = $conn->prepare("SELECT table_name FROM auction_sessions WHERE id = ?");
$auction_stmt->bind_param("i", $auction_id);
$auction_stmt->execute();
$auction_result = $auction_stmt->get_result();
$auction = $auction_result->fetch_assoc();

if (!$auction) {
    echo "Auction not found.";
    exit;
}

$table_name = $conn->real_escape_string($auction['table_name']);

// Fetch all players in this auction
$players_sql = "SELECT player_name, team FROM `$table_name` ORDER BY player_name";
$players_result = $conn->query($players_sql);

$players = [];
while ($player = $players_result->fetch_assoc()) {
    $players[] = $player;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Auction Summary - IPL Admin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 20px;
    }

    .summary-container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h1, h2 {
      text-align: center;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2rem;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 0.75rem;
      text-align: center;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .back-btn {
      display: block;
      margin: 30px auto 0;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }

    select {
      padding: 10px;
      margin: 1rem auto;
      display: block;
      font-size: 16px;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="summary-container">
    <h1>IPL Auction Summary</h1>

    <!-- Team Filter Dropdown -->
    <select id="team-select">
      <option value="ALL">All Teams</option>
      <option value="CSK">CSK</option>
      <option value="MI">MI</option>
      <option value="RCB">RCB</option>
      <option value="GT">GT</option>
      <option value="RR">RR</option>
      <option value="KKR">KKR</option>
      <option value="SRH">SRH</option>
      <option value="LSG">LSG</option>
      <option value="PBKS">PBKS</option>
      <option value="DC">DC</option>
      <option value="UNSOLD">UNSOLD</option>
    </select>

    <h2 id="team-heading">Players</h2>

    <!-- Table to display auctioned players -->
    <table id="sold-players-table">
      <thead>
        <tr>
          <th>Player Name</th>
          <th>Team</th>
        </tr>
      </thead>
      <tbody id="sold-players-body"></tbody>
    </table>

    <a href="dashboard.html" class="back-btn">Return to Dashboard</a>
  </div>

  <script>
    // Store all auction data
    let allData = <?php echo json_encode($players); ?>;

    // Render players based on selected team
    function renderPlayers(team = "ALL") {
      const soldBody = document.getElementById("sold-players-body");
      const teamHeading = document.getElementById("team-heading");
      soldBody.innerHTML = "";

      // Filter players based on selected team
      const filtered = team === "ALL"
        ? allData
        : allData.filter(player => player.team === team);

      teamHeading.textContent = `Showing players for: ${team === "ALL" ? "All Teams" : team}`;

      // If no players found, show a message
      if (filtered.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="2">No players found for selected team.</td>`;
        soldBody.appendChild(row);
        return;
      }

      // Display players in the table
      filtered.forEach(player => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${player.player_name}</td>
          <td>${player.team}</td>
        `;
        soldBody.appendChild(row);
      });
    }

    // Initial rendering of all players
    renderPlayers();

    // Event listener for team selection change
    document.getElementById("team-select").addEventListener("change", (e) => {
      renderPlayers(e.target.value);
    });
  </script>
</body>
</html>

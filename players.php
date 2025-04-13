<?php
// Include the database connection
include('db.php');

// Query to fetch player data from the 'players' table
$query = "SELECT * FROM players"; // Adjust this query to fit your database structure

// Execute the query
$result = mysqli_query($conn, $query);

// Check if any rows are returned
if ($result) {
    // Fetch all rows as an associative array
    $players = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Return the player data as a JSON response
    echo json_encode($players);
} else {
    // If the query fails, return an error message
    echo json_encode(["error" => "Error fetching player data: " . mysqli_error($conn)]);
}

// Close the database connection
mysqli_close($conn);
?>

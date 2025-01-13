<?php
// Include the database connection file
require 'db_connection.php';

// Start the session to access session variables
session_start();

// Check if the user is logged in by checking the 'user_id' session variable
if (isset($_SESSION['user_id'])) {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Prepare the SQL statement to select the crypto_id from the favorites table for the current user
    $stmt = $conn->prepare("SELECT crypto_id FROM favorites WHERE user_id = ?");
    
    // Bind the user_id parameter to the prepared statement
    $stmt->bind_param("i", $user_id);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result of the query
    $result = $stmt->get_result();

    // Initialize an empty array to hold the favorite crypto IDs
    $favorites = [];
    
    // Fetch the results and store each crypto_id in the $favorites array
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row['crypto_id'];
    }

    // Encode the $favorites array as a JSON response and output it
    echo json_encode($favorites);

    // Close the prepared statement and the database connection
    $stmt->close();
    $conn->close();
}
?>

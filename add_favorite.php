<?php
// Include the database connection file
require 'db_connection.php';

// Start the session to access session variables
session_start();

// Check if the user is logged in by verifying the session variable 'user_id'
if (isset($_SESSION['user_id'])) {
    // Retrieve the logged-in user's ID from the session
    $user_id = $_SESSION['user_id'];

    // Retrieve the cryptocurrency ID from the POST request
    $crypto_id = $_POST['crypto_id'];

    // Prepare an SQL statement to check if the cryptocurrency is already in the user's favorites
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND crypto_id = ?");
    $checkStmt->bind_param("is", $user_id, $crypto_id); // Bind user ID and crypto ID as parameters
    $checkStmt->execute(); // Execute the prepared statement
    $checkStmt->bind_result($count); // Bind the result to the variable $count
    $checkStmt->fetch(); // Fetch the result
    $checkStmt->close(); // Close the statement

    // Check if the cryptocurrency is already in the user's favorites
    if ($count > 0) {
        // If the cryptocurrency is already in the favorites, display a message
        echo "This coin is already in your favorites.";
    } else {
        // If the cryptocurrency is not in the favorites, prepare to insert it
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, crypto_id) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $crypto_id); // Bind user ID and crypto ID as parameters

        // Execute the insert statement and provide feedback based on success or failure
        if ($stmt->execute()) {
            echo "Added to favorites."; // Success message
        } else {
            // Display an error message if the query fails
            echo "Error: " . $conn->error;
        }

        $stmt->close(); // Close the statement
    }

    $conn->close(); // Close the database connection
}
?>


<?php
// Include the database connection file
require 'db_connection.php';

// Start the session to access session variables
session_start();

// Check if the user is logged in by checking the 'user_id' session variable
if (isset($_SESSION['user_id'])) {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Get the crypto_id from the POST request data
    $crypto_id = $_POST['crypto_id'];

    // Prepare the SQL statement to delete the selected crypto from the favorites table
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND crypto_id = ?");
    
    // Bind the parameters (user_id as integer, crypto_id as string) to the prepared statement
    $stmt->bind_param("is", $user_id, $crypto_id);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        // If successful, output a message indicating the item was removed from favorites
        echo "Removed from favorites.";
    } else {
        // If an error occurs, output the error message from the database
        echo "Error: " . $conn->error;
    }

    // Close the prepared statement and the database connection
    $stmt->close();
    $conn->close();
}
?>


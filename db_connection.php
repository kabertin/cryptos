<?php
// Database connection settings
$dbHost = 'localhost';
$dbUsername = 'uqbrlykt_admin';
$dbPassword = '*****************';
$dbName = 'uqbrlykt_cryptos';

// Create a new MySQLi connection object
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check if the connection was successful
if ($conn->connect_error) { // If there's a connection error
    die("Connection failed: " . $conn->connect_error); // Terminate the script and display an error message
}

// If no error, the connection is established successfully
?>

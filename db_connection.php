<?php
// Database connection settings
$host = 'localhost'; // Hostname of the database server
$user = 'root'; // Database username
$password = 'Olivakarinda1.'; // Database password
$dbname = 'cryptos'; // Name of the database to connect to

// Create a new MySQLi connection object
$conn = new mysqli($host, $user, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) { // If there's a connection error
    die("Connection failed: " . $conn->connect_error); // Terminate the script and display an error message
}

// If no error, the connection is established successfully
?>


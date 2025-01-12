<?php
// Include the database connection file to interact with the database
include 'db_connection.php';

// Start the session to store the CSRF token for form security
session_start();

// Function to generate a CSRF token for securing the form
function generateCsrfToken() {
    return bin2hex(random_bytes(32)); // Securely generate a random token
}

// Check if the form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF Token verification to protect against CSRF attacks
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch!'); // Terminate if token doesn't match
    }

    // Sanitize and validate the input data
    $crypto = filter_var($_POST['crypto'], FILTER_SANITIZE_STRING); // Sanitize cryptocurrency symbol
    $price_level = filter_var($_POST['price_level'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT); // Sanitize user ID

    // Check for invalid input
    if (empty($crypto) || empty($price_level) || !is_numeric($price_level) || $price_level <= 0) {
        echo "Invalid input. Please try again."; // Error message for invalid input
        exit;
    }

    // Check if the user already has a price alert for this cryptocurrency
    $checkQuery = $conn->prepare("SELECT alert_id, alert_status FROM price_alerts WHERE user_id = ? AND crypto_symbol = ?");
    $checkQuery->bind_param('is', $user_id, $crypto); // Bind parameters
    $checkQuery->execute();
    $checkQuery->bind_result($alert_id, $alert_status);
    $checkQuery->fetch();
    $checkQuery->close();

    $current_timestamp = date('Y-m-d H:i:s'); // Get current timestamp

    if ($alert_id) {
        // If an alert already exists, update the price level and status (if needed)
        $updateQuery = $conn->prepare("UPDATE price_alerts SET price_level = ?, updated_at = ?, alert_status = ? WHERE alert_id = ?");
        $updateQuery->bind_param('dsis', $price_level, $current_timestamp, 'active', $alert_id); // Bind parameters for the update query

        if ($updateQuery->execute()) {
            echo "<script>alert('Price alert updated successfully!');</script>"; // Success message
        } else {
            echo "<script>alert('Failed to update price alert. Please try again.');</script>"; // Error message
        }

        $updateQuery->close();
    } else {
        // If no alert exists, insert a new price alert with the necessary fields
        $stmt = $conn->prepare("INSERT INTO price_alerts (user_id, crypto_symbol, price_level, alert_status, created_at, updated_at, email_sent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isdssss', $user_id, $crypto, $price_level, 'active', $current_timestamp, $current_timestamp, 'no'); // Bind parameters for the insert query

        if ($stmt->execute()) {
            echo "<script>alert('Price alert set successfully!');</script>"; // Success message
        } else {
            echo "<script>alert('Failed to set price alert. Please try again.');</script>"; // Error message
        }

        $stmt->close();
    }
}

// Fetch available cryptocurrencies from the CoinGecko API using cURL
$cryptoList = [];
$apiUrl = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd"; // URL for CoinGecko API

// Initialize cURL session to fetch the cryptocurrency data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl); // Set the API URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: MyCryptoApp" // Set a custom User-Agent header
]);

// Disable SSL verification (only for local development)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable peer SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host SSL verification

// Execute the cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors and handle the response
if ($response === false) {
    echo "cURL Error: " . curl_error($ch); // If there was an error, display it
} else {
    // Decode the JSON response and store it in $cryptoList
    $cryptoList = json_decode($response, true);
}

// Close the cURL session
curl_close($ch);

// Generate a CSRF token for the form and store it in the session
$_SESSION['csrf_token'] = generateCsrfToken();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Price Alert</title>
    <style>
        /* Styles for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #4CAF50;
            margin: 60px;
        }

        #navBar {
            width: 100%;
            background-color: #333;
            display: flex;
            justify-content: center;
            padding: 10px 0;
            position: relative;
        }

        #navBar a {
            color: #fff;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
            font-size: 18px;
            border-right: 1px solid #444;
        }

        #navBar a:last-child {
            border-right: none;
        }

        #navBar a:hover {
            background-color: #ddd;
            color: #000;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            max-width: 600px;
            margin-top: 40px;
            color: #fff;
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
        }

        select, input[type="number"] {
            width: 90%;
            max-width: 500px;
            padding: 12px;
            margin: 10px 0 20px;
            font-size: 16px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #222;
            color: #fff;
        }

        button {
            padding: 10px 15px;
            font-size: 14px;
            color: #000000;
            background-color: #b46c4a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #64ad62;
        }
    </style>
</head>
<body>
    <h1>Set Price Alert</h1>
    <!-- Navigation Bar -->
    <div id="navBar">
        <a href="dashboard.html">All Coins</a>
        <a href="favorites.html">Favorites</a>
        <a href="charts.html">Charts</a>
        <a href="set_notification.php">Notifications</a>
    </div>

    <!-- Form for setting price alerts -->
    <form action="set_notification.php" method="POST">
        <!-- CSRF Token field to protect the form -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <label for="crypto">Select Cryptocurrency:</label>
        <select name="crypto" id="crypto" onchange="updatePrice()">
            <?php
            // Loop through the available cryptocurrencies and populate the dropdown
            foreach ($cryptoList as $crypto) {
                echo "<option value=\"" . htmlspecialchars($crypto['id']) . "\">" . htmlspecialchars($crypto['name']) . " (" . htmlspecialchars($crypto['symbol']) . ")</option>";
            }
            ?>
        </select>

        <label for="price_level">Price Level (USD):</label>
        <input type="number" name="price_level" id="price_level" step="any" min="0" required>

        <!-- Hidden input for user ID -->
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>"> <!-- user ID is stored in the SESSION since login process -->

        <button type="submit">Set Alert</button>
    </form>
    
    <script>
    // Function to fetch the current price based on the selected cryptocurrency
    function updatePrice() {
        var crypto = document.getElementById('crypto').value;
        var priceInput = document.getElementById('price_level');

        // Ensure the price is set with full precision
        fetch(`https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=${crypto}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    var currentPrice = data[0].current_price;
                    priceInput.value = currentPrice.toFixed(5); // Limit precision here if needed
                }
            })
            .catch(error => {
                console.error('Error fetching the cryptocurrency data:', error);
            });
    }

    // Initial price update on page load (set default to Bitcoin or a predefined value)
    document.addEventListener("DOMContentLoaded", function() {
        updatePrice(); // Run on page load
    });
    </script>
</body>
</html>

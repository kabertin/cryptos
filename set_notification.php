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
    // CSRF Token verification
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch!');
    }

    // Sanitize and validate inputs
    $crypto_id = filter_var($_POST['crypto'], FILTER_SANITIZE_STRING); // Sanitize cryptocurrency ID
    $crypto_symbol = filter_var($_POST['crypto_symbol'], FILTER_SANITIZE_STRING); // Sanitize cryptocurrency symbol
    $price_level = filter_var($_POST['price_level'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $instant_price = filter_var($_POST['instant_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);


    if (empty($crypto_id) || empty($crypto_symbol) || empty($price_level) || !is_numeric($price_level) || $price_level <= 0) {
        echo "Invalid input. Please try again.";
        exit;
    }

    // Check if the user already has a price alert for this cryptocurrency
    $checkQuery = $conn->prepare("SELECT alert_id, alert_status FROM price_alerts WHERE user_id = ? AND crypto_symbol = ?");
    $checkQuery->bind_param('is', $user_id, $crypto_symbol);
    $checkQuery->execute();
    $checkQuery->bind_result($alert_id, $alert_status);
    $checkQuery->fetch();
    $checkQuery->close();

    $current_timestamp = date('Y-m-d H:i:s');
    $alert_status = 'active';
    $email_sent = 'no';
    $alert_type='';

    $alert_type = ($price_level >= $instant_price) ? 'above' : 'below'; // assign alert type according to the data submitted from the form

    if ($alert_id) {
        // Update existing alert
        $updateQuery = $conn->prepare("UPDATE price_alerts SET price_level = ?, alert_type = ?, updated_at = ?, alert_status = ? WHERE alert_id = ?");
        $updateQuery->bind_param('dsssi', $price_level, $alert_type, $current_timestamp, $alert_status, $alert_id);

        if ($updateQuery->execute()) {
            echo "<script>alert('Price alert updated successfully!');</script>";
        } else {
            echo "<script>alert('Failed to update price alert. Please try again.');</script>";
        }

        $updateQuery->close();
    } else {
        // Insert new alert
        $stmt = $conn->prepare("INSERT INTO price_alerts (user_id, crypto_id, crypto_symbol, price_level, alert_type, alert_status, created_at, updated_at, email_sent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issdsssss', $user_id, $crypto_id, $crypto_symbol, $price_level, $alert_type, $alert_status, $current_timestamp, $current_timestamp, $email_sent);

        if ($stmt->execute()) {
            echo "<script>alert('Price alert set successfully!');</script>";
        } else {
            echo "<script>alert('Failed to set price alert. Please try again.');</script>";
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
        /* General body styling */
        body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #000; /* Black background */
                color: #fff; /* White text */
                display: flex;
                flex-direction: column;
                align-items: center;
            }

        /* Main title styling */
        h1 {
                color: #4CAF50; /* Green color */
                margin: 40px 20px;
                text-align: center;
                font-size: 2rem;
            }
    
            /* Navigation bar styling */
            #navBar {
                width: 100%;
                background-color: #333; /* Dark background */
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px 20px;
            }
    
            /* Styling for navigation links */
            #navBar a {
                color: #fff;
                padding: 10px 15px;
                text-decoration: none;
                text-align: center;
                font-size: 1rem;
                border-right: 1px solid #444;
            }
    
            /* Remove border from last nav link */
            #navBar a:last-child {
                border-right: none;
            }
    
            /* Hover effect for navigation links */
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
            max-width: 700px;
            margin-top: 60px;
            margin-left: 4px;
            margin-right: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 16px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #222;
            color: #fff;
        }

        input[type="number"] {
            width: 96%;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            h1 {
                    font-size: 1.5rem;
                }
    
                #navBar a {
                    font-size: 0.9rem;
                    padding: 8px 12px;
                }

            form {
                margin-left: 10px;
                margin-right: 10px;
                max-width: 90%;
            }

            form {
                padding: 15px;
            }

            button {
                font-size: 14px;
            }

            label {
                font-size: 14px;
            }

            select, input[type="number"] {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                    font-size: 1.2rem;
                    margin: 20px 10px;
                }
    
                #navBar a {
                    font-size: 0.8rem;
                    padding: 6px 10px;
                }

            form {
                margin-left: 10px;
                margin-right: 10px;
                max-width: 90%;
            }
            label {
                font-size: 12px;
            }

            select, input[type="number"] {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <h1>Set Price Alert</h1>
    <!-- Navigation Bar -->
    <div id="navBar">
        <a href="dashboard.html">All Coins</a>
        <a href="favorites.php">Favorites</a>
        <a href="charts.html">Charts</a>
        <a href="set_notification.php">Notifications</a>
        <a href="logout.php" style="background-color: #963d3d; border-radius: 5px;">Logout</a>
    </div>

    <!-- Form for setting price alerts -->
    <form action="set_notification.php" method="POST">
        <!-- CSRF Token field to protect the form -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <label for="crypto">Select Cryptocurrency:</label>
        <select name="crypto" id="crypto" onchange="updatePrice()">
            <?php
            foreach ($cryptoList as $crypto) {
                echo "<option value=\"" . htmlspecialchars($crypto['id']) . "\"
                    data-symbol=\"" . htmlspecialchars($crypto['symbol']) . "\">"
                    . htmlspecialchars($crypto['name']) . " (" . htmlspecialchars($crypto['symbol']) . ")</option>";
            }
            ?>
        </select>
        <input type="hidden" id="crypto_symbol" name="crypto_symbol"> <!-- Hidden input for the symbol -->
        
        <input type="hidden" id="instant_price" name="instant_price"> <!-- Hidden input for the alert type (above or below) -->

        <label for="price_level">Price Level (USD):</label>
        <input type="number" name="price_level" id="price_level" step="any" min="0" required>

        <!-- Hidden input for user ID -->
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">

        <button type="submit">Set Alert</button>
    </form>

    <script>
        const PRICE_CACHE_KEY = 'crypto_prices';
        const PRICE_TIMESTAMP_KEY = 'crypto_prices_last_updated';
        const PRICE_CACHE_EXPIRY = 60000; // 60 seconds

        function updatePrice() {
            const cryptoSelect = document.getElementById('crypto');
            const cryptoId = cryptoSelect.value; // Get the cryptocurrency ID
            const cryptoSymbol = cryptoSelect.options[cryptoSelect.selectedIndex].getAttribute('data-symbol'); // Get the symbol
            const priceInput = document.getElementById('price_level');
            const instantPrice = document.getElementById('instant_price');
            const symbolInput = document.getElementById('crypto_symbol'); // Hidden input for symbol

            symbolInput.value = cryptoSymbol; // Update hidden input with the selected symbol

            // Retrieve cached prices
            const cachedPrices = JSON.parse(localStorage.getItem(PRICE_CACHE_KEY) || '{}');
            const lastUpdated = localStorage.getItem(PRICE_TIMESTAMP_KEY);

            // Check if the price is cached and still valid
            if (cachedPrices[cryptoId] && lastUpdated && (Date.now() - lastUpdated < PRICE_CACHE_EXPIRY)) {
                console.log('Using cached price data');
                priceInput.value = cachedPrices[cryptoId].toFixed(5);
                instantPrice.value = cachedPrices[cryptoId].toFixed(5);
            } else {
                // Fetch fresh price data
                fetch(`https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=${cryptoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const currentPrice = data[0].current_price;
                            priceInput.value = currentPrice.toFixed(5);
                            instantPrice.value = currentPrice.toFixed(5);

                            // Update cache
                            cachedPrices[cryptoId] = currentPrice;
                            localStorage.setItem(PRICE_CACHE_KEY, JSON.stringify(cachedPrices));
                            localStorage.setItem(PRICE_TIMESTAMP_KEY, Date.now());
                        }
                    })
                    .catch(error => console.error('Error fetching cryptocurrency data:', error));
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            updatePrice();

            // Add event listener to update price when the cryptocurrency selection changes
            document.getElementById('crypto').addEventListener('change', updatePrice);
        });
    </script>
</body>
</html>

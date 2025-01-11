<?php
// Include the database connection file to interact with the database
include 'db_connection.php';

// Include PHPMailer for email sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';  // Make sure you have PHPMailer and Monolog installed via Composer

// Include Monolog for logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create a logger instance
$log = new Logger('email_logger');

// Add a file handler (log file: email_logs.log)
$log->pushHandler(new StreamHandler('email_logs.log', Logger::INFO));

// Function to send email notifications
function sendEmailNotification($to, $crypto, $currentPrice, $priceLevel, $log, $alertId, $conn) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'cryptos.mercato.rw';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'alerts@cryptos.mercato.rw';
        $mail->Password   = 'Olivakarinda1.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('alerts@cryptos.mercato.rw', 'Cryptos Price Alert');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Cryptocurrency Price Alert';
        $mail->Body    = "Hello, the price of \" $crypto \" has reached your set alert price of $priceLevel USD. Current price: $currentPrice USD.";

        // Send the email
        $mail->send();

        // Log success
        $log->info("SUCCESS: Email sent to $to for $crypto. Current Price: $currentPrice, Alert Price: $priceLevel.");

        // Update the price_alerts table: Set email_sent to 'yes' and alert_status to 'triggered'
        $updateSql = "UPDATE price_alerts SET email_sent = 'yes', alert_status = 'triggered' WHERE alert_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("i", $alertId);
        $stmt->execute();
        $stmt->close();

        echo "Email has been sent to $to.";
    } catch (Exception $e) {
        // Log failure
        $log->error("FAILURE: Email failed for $to. Error: {$mail->ErrorInfo}");

        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch all price alerts from the database
$sql = "SELECT * FROM price_alerts WHERE email_sent = 'no'";  // Only fetch alerts that haven't been emailed
$result = $conn->query($sql);

// If there are any price alerts set
if ($result->num_rows > 0) {
    // Fetch the cryptocurrency prices from the CoinGecko API
    $apiUrl = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: CryptoAlertApp"]);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        echo "cURL Error: " . curl_error($ch);
    } else {
        $cryptoList = json_decode($response, true);

        // Loop through each price alert
        while ($row = $result->fetch_assoc()) {
            $cryptoSymbol = $row['crypto_symbol'];
            $priceLevel = $row['price_level'];
            $userId = $row['user_id'];
            $alertId = $row['alert_id'];  // Get the alert ID

            // Find the current price of the selected cryptocurrency
            foreach ($cryptoList as $crypto) {
                if ($crypto['symbol'] === $cryptoSymbol) {
                    $currentPrice = $crypto['current_price'];

                    // If the current price is greater than or equal to the set price level, send an email
                    if ($currentPrice >= $priceLevel) {
                        // Fetch user email from the database
                        $userSql = "SELECT email FROM users WHERE id = $userId";
                        $userResult = $conn->query($userSql);
                        if ($userResult->num_rows > 0) {
                            $userRow = $userResult->fetch_assoc();
                            $userEmail = $userRow['email'];

                            // Send email notification and update the database
                            sendEmailNotification($userEmail, $crypto['name'], $currentPrice, $priceLevel, $log, $alertId, $conn);
                        }
                    }
                }
            }
        }
    }
} else {
    echo "No price alerts set.";
}

$conn->close();

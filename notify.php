<?php
// Include the database connection file to interact with the database
include 'db_connection.php';

// Include PHPMailer for email sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';  // Make sure you have PHPMailer installed via Composer

// Function to send email notifications
function sendEmailNotification($to, $crypto, $currentPrice, $priceLevel) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'cryptos.mercato.rw';                       // Set the SMTP server
        $mail->SMTPAuth   = true;                                     // Enable SMTP authentication
        $mail->Username   = 'alerts@cryptos.mercato.rw';                 // SMTP username (from Mailtrap or SMTP service)
        $mail->Password   = '*************';                 // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Enable SSL encryption
        $mail->Port       = 465;                                      // TCP port for sending email

        // Recipients
        $mail->setFrom('no-reply@yourdomain.com', 'Crypto Alert');
        $mail->addAddress($to);                                       // Add a recipient

        // Content
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->Subject = 'Cryptocurrency Price Alert';
        $mail->Body    = "Hello, the price of $crypto has reached your set alert price of $priceLevel USD. Current price: $currentPrice USD.";

        // Send the email
        $mail->send();
        echo "Email has been sent to $to.";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch all price alerts from the database
$sql = "SELECT * FROM price_alerts";
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
                            
                            // Send email notification
                            sendEmailNotification($userEmail, $crypto['name'], $currentPrice, $priceLevel);
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
?>

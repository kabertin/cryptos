<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'cryptos.mercato.rw';
    $mail->SMTPAuth = true;
    $mail->Username = 'alerts@cryptos.mercato.rw';
    $mail->Password = 'Olivakarinda1.';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('alerts@cryptos.mercato.rw', 'Crypto Alert');
    $mail->addAddress('karindabertin35@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email.';

    $mail->send();
    echo 'Test email sent successfully.';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>

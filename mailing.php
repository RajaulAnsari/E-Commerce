<?php
// Include PHPMailer classes
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $subject, $html) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true); // Passing true enables exceptions

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cleckhub2@gmail.com'; // SMTP username
        $mail->Password   = 'ixhx kauk bjwc rkta';     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port       = 587;                // TCP port to connect to

        // Sender info
        $mail->setFrom('cleckhub2@gmail.com', 'CLECKHUB');

        // Receiver info
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->AddEmbeddedImage (dirname(__FILE__). '/images/icons/IcoLogo.png', 'logo_ico', 'IcoLogo.png');
        $mail->Body = $html;

        // Send email
        $mail->send();
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
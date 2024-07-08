<?php

require 'C:\\xampp\\htdocs\\WEB\\vendor\\autoload.php'; // Include the Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include the database connection settings
include('config.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    
    // Add your email address where you want to receive messages
    $to = "olphemie@gmail.com";
    $email_subject = "New Contact Form Submission: $subject";
    $headers = "From: $email";

    // Construct the email message
    $email_message = "Name: $name\n";
    $email_message .= "Email: $email\n";
    $email_message .= "Subject: $subject\n\n";
    $email_message .= "Message:\n$message";

    // Send the email
    if (mail($to, $email_subject, $email_message, $headers)) {
        echo "Your message has been sent successfully!";
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->Username = 'olphemie@gmail.com'; // Replace with your Gmail email
    $mail->Password = 'itak uyjg empc blnp'; // Replace with your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    // Email details
    $mail->setFrom('olphemie@gmail.com', 'SalesPilot');
    $mail->addAddress($email); // Use $email variable instead of undefined $Email
    $mail->Subject = 'Activate Your Account';
    $mail->isHTML(true); // Set email format to HTML
    $mail->Body = 'Hello,<br>Click the link below to activate your account:<br><a href="https://localhost/WEB/activate.php?token=your_activation_token">Activate Account</a>';

    if ($mail->send()) {
        header("Location: reg-success.html"); // Redirect after sending activation email
        exit(); // Add exit to stop the script execution
    } else {
        echo 'Error sending activation email: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'Mailer Error: ' . $e->getMessage();
}
?>
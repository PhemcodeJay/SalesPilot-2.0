<?php

require 'C:\\xampp\\htdocs\\project\\vendor\\autoload.php'; // Include the Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include the database connection settings
include('config.php');

// Initialize variables
$name = $email = $subject = $message = '';
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Validate input
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // If no validation errors, send the email
    if (empty($errors)) {
        $mail = new PHPMailer(true); // Create a new PHPMailer instance

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.ionos.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@cybertrendhub.store'; // Replace with your email
            $mail->Password = 'kokochulo@1987#'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            // Set email sender and recipient
            $mail->setFrom('phemcodejay@gmail.com', 'SalesPilot'); // Replace with your email and name
            $mail->addAddress($to) ; // Add a recipient

            // Email subject and body
            $mail->Subject = "New Contact Form Submission: $subject";
            $mail->Body = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

            // Send the email
            $mail->send();
            echo "Your message has been sent successfully!";
        } catch (Exception $e) {
            echo "Oops! Something went wrong. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<div class='text-danger'>$error</div>";
        }
    }
}
?>

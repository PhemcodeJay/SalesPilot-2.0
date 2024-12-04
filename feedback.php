<?php

// Include the PHPMailer settings
require __DIR__ . '/vendor/autoload.php'; // Include the Composer autoloader
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';
require '../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include the database connection settings
include('config.php');

// Initialize variables and error array
$name = $email = $phone = $message = '';
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign input values
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Validate inputs
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($phone)) $errors[] = 'Phone number is required.';
    if (empty($message)) $errors[] = 'Message is required.';

    // Process the form if no validation errors
    if (empty($errors)) {
        try {
            // Prepare the insert query with placeholders
            $stmt = $connection->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (:name, :email, :phone, :message)");

            // Bind values to the placeholders
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':message', $message);

            // Execute the query and check for success
            if ($stmt->execute()) {
                // Set up PHPMailer for sending the email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.ionos.com'; // Use your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'admin@cybertrendhub.store'; // SMTP username
                $mail->Password = 'Kokochulo@1987#'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('admin@cybertrendhub.store', 'CyberTrendHub');
                $mail->addAddress('phemcodejay@gmail.com'); // Recipient's email address
                $mail->Subject = 'New Feedback Message from ' . $name . ' (Phone: ' . $phone . ')';
                $mail->Body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

                // Send the email
                if ($mail->send()) {
                    echo "Email sent successfully.<br>";
                } else {
                    echo "Mailer error: {$mail->ErrorInfo}<br>";
                }

                // Redirect after successful form submission
                echo "<script>setTimeout(() => { window.location.href = 'index.html'; }, 2000);</script>";
            } else {
                echo "<div class='text-danger'>Database error: Unable to insert data.</div>";
            }
        } catch (PDOException $e) {
            // Handle PDO exceptions (e.g., database issues)
            echo "<div class='text-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
        } 
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<div class='text-danger'>$error</div>";
        }
    }
}
?>

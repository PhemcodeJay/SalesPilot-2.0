<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration and PHPMailer classes
require 'config.php'; // Include the database configuration
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables and error array
$name = $email = $phone = $message = '';
$errors = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    // Validate input fields
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($message)) {
        $errors[] = 'Message is required.';
    }

    // If no errors, insert data into the database and send email
    if (empty($errors)) {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        // Execute the statement
        if ($stmt->execute()) {
            // Setup PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
$mail->Host = 'smtp.ionos.com';
$mail->SMTPAuth = true;
$mail->Username = 'admin@cybertrendhub.store';  // IONOS email address
$mail->Password = 'Kokochulo@1987#';             // IONOS email password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
$mail->Port = 587;                               // Port 587 for TLS
$mail->SMTPDebug = 0;                            // Enable debug output for troubleshooting

                // Set email format and recipient details
                $mail->setFrom('admin@cybertrendhub.store', 'CyberTrendHub');
                $mail->addAddress('phemcodejay@gmail.com');       // Primary recipient
                $mail->addReplyTo($email, $name);                 // Reply-to email and name

                // Email content
                $mail->Subject = 'New Feedback Message from ' . $name;
                $mail->Body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

                // Send the email
                $mail->send();
                echo "Email sent successfully.<br>"; // Debug message

                // Success message and page reload script
                echo "<div class='alert alert-success'>Thank you, $name! Your message has been sent and saved in our database.</div>";
                echo "<script>setTimeout(function() { window.location.reload(); }, 2000);</script>"; // Refresh page after 2 seconds
            } catch (Exception $e) {
                echo "<div class='text-danger'>There was an error sending your message via email: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='text-danger'>Error: " . $stmt->error . "</div>"; // Display SQL errors
        }

        // Close the statement
        $stmt->close();
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<div class='text-danger'>$error</div>";
        }
    }
}

// Close the database connection
$conn->close();
?>

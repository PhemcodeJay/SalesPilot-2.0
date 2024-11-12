<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer's autoloader

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file is uploaded
    if (isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['paymentProof']['tmp_name'];
        $fileName = $_FILES['paymentProof']['name'];
        $fileSize = $_FILES['paymentProof']['size'];
        $fileType = $_FILES['paymentProof']['type'];

        // Define the upload directory (make sure it's writable)
        $uploadDir = 'uploads/payment_proofs/';
        $destPath = $uploadDir . basename($fileName);

        // Check if the file type is allowed (adjust as necessary)
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type. Only JPG, PNG, and PDF are allowed.";
            exit;
        }

        // Move the file to the upload directory
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Save the file information to the database (example for demonstration)
            // Example query:
            // $query = "INSERT INTO payments (payment_method, payment_proof, payment_id) VALUES ('$paymentMethod', '$destPath', '$paymentId')";
            // $result = mysqli_query($conn, $query);

            echo "Payment proof uploaded successfully.";

            // Send the email with the proof to admin@cybertrendhub.store
            try {
                $mail = new PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.yourserver.com'; // Set the SMTP server to use (change to your server)
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com'; // SMTP username
                $mail->Password = 'your_email_password'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('your_email@example.com', 'Your Name');
                $mail->addAddress('admin@cybertrendhub.store'); // Admin's email address

                // Attachments
                $mail->addAttachment($destPath, $fileName); // Attach the uploaded file

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'New Payment Proof Uploaded';
                $mail->Body    = 'A new payment proof has been uploaded. Please review the attached file.';

                $mail->send();
                echo 'Message has been sent to admin@cybertrendhub.store';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "There was an error uploading the file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
} else {
    echo "Invalid request.";
}
?>

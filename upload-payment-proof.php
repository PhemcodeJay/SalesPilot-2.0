<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer's autoloader

header('Content-Type: application/json');

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if payment proof file is uploaded
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['payment_proof']['tmp_name'];
        $fileName = $_FILES['payment_proof']['name'];
        $fileSize = $_FILES['payment_proof']['size'];
        $fileType = $_FILES['payment_proof']['type'];

        // Define the upload directory (make sure it's writable)
        $uploadDir = 'uploads/payment_proofs/';
        $destPath = $uploadDir . basename($fileName);

        // Validate file type (JPG, PNG, PDF)
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and PDF are allowed.']);
            exit;
        }

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($fileTmpPath, $destPath)) {

            // Get the selected payment method from the form
            if (isset($_POST['payment_method']) && in_array($_POST['payment_method'], ['paypal', 'binance', 'mpesa', 'naira'])) {
                $paymentMethod = $_POST['payment_method'];

                // Save the payment information to the database
                // Example: Using a prepared statement to insert into the database
                $conn = new mysqli('localhost', 'username', 'password', 'database'); // Update with actual DB connection details
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $stmt = $conn->prepare("INSERT INTO payments (payment_method, payment_proof) VALUES (?, ?)");
                $stmt->bind_param("ss", $paymentMethod, $destPath);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Payment proof uploaded successfully.']);

                    // Send email with the payment proof
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.yourserver.com'; // Set your SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'your_email@example.com'; // SMTP username
                        $mail->Password = 'your_email_password'; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipients
                        $mail->setFrom('your_email@example.com', 'Your Name');
                        $mail->addAddress('admin@cybertrendhub.store'); // Admin email

                        // Attachments
                        $mail->addAttachment($destPath, $fileName);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'New Payment Proof Uploaded';
                        $mail->Body = 'A new payment proof has been uploaded for payment method: ' . ucfirst($paymentMethod) . '. Please review the attached file.';

                        $mail->send();
                        echo json_encode(['success' => true, 'message' => 'Email sent to admin@cybertrendhub.store.']);
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error saving payment information to the database.']);
                }

                $stmt->close();
                $conn->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid payment method selected.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error moving the uploaded file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or there was an upload error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require 'C:\xampp\htdocs\project\vendor\autoload.php'; // Include the Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_reset'])) {
    $email = htmlspecialchars($_POST["Email"]);

    if (empty($email)) {
        echo 'Email is required!';
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format!';
        return;
    }

    $stmt = $connection->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 0) {
        echo 'Email not found!';
        return;
    }

    $userId = $stmt->fetchColumn();
    $resetToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $resetStmt = $connection->prepare('INSERT INTO password_resets (user_id, reset_code, expires_at) VALUES (?, ?, ?)');
    if ($resetStmt->execute([$userId, $resetToken, $expiresAt])) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->Username = 'olphemie@gmail.com';
            $mail->Password = 'itak uyjg empc blnp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->setFrom('olphemie@gmail.com', 'SalesPilot');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = 'Click the link below to reset your password:<br><a href="http://localhost/project/reset_password.php?token=' . $resetToken . '">Reset Password</a>';

            if ($mail->send()) {
                echo 'Password reset email sent!';
            } else {
                echo 'Error sending email: ' . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo 'Mailer Error: ' . $e->getMessage();
        }
    } else {
        echo 'Error inserting reset token into the database: ' . $resetStmt->errorInfo()[2];
    }
}
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Password Recovery</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="http://localhost/project/assets/images/favicon.ico" />
      <link rel="stylesheet" href="http://localhost/project/assets/css/backend-plugin.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/remixicon/fonts/remixicon.css">  </head>
  <body class=" ">
    <!-- loader Start -->
    <div id="loading">
          <div id="loading-center">
          </div>
    </div>
    <!-- loader END -->
    
      <div class="wrapper">
      <section class="login-content">
         <div class="container">
            <div class="row align-items-center justify-content-center height-self-center">
               <div class="col-lg-8">
                  <div class="card auth-card">
                     <div class="card-body p-0">
                        <div class="d-flex align-items-center auth-content">
                           <div class="col-lg-7 align-self-center">
                              <div class="p-3">
                                 <h2 class="mb-2">Reset Password</h2>
                                 <p>Enter your email address and we'll send you an email with instructions to reset your password.</p>
                                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <label>Email:</label>
                                    <input type="email" name="Email" required>
                                    <button type="submit" name="request_reset">Request Password Reset</button>
                                </form>
                              </div>
                           </div>
                           <div class="col-lg-5 content-right">
                              <img src="http://localhost/project/assets/images/login/01.png" class="img-fluid image-right" alt="">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      </div>
    
    <!-- Backend Bundle JavaScript -->
    <script src="http://localhost/project/assets/js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="http://localhost/project/assets/js/table-treeview.js"></script>
    
    <!-- app JavaScript -->
    <script src="http://localhost/project/assets/js/app.js"></script>
  </body>
</html>
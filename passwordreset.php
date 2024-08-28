<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $password = htmlspecialchars($_POST["Password"]);
    $confirmPassword = htmlspecialchars($_POST["ConfirmPassword"]);
    $resetToken = htmlspecialchars($_POST["reset_code"]);

    if (empty($password) || empty($confirmPassword) || empty($resetToken)) {
        echo 'All fields are required!';
        return;
    }

    if ($password !== $confirmPassword) {
        echo 'Passwords do not match!';
        return;
    }

    if (strlen($password) > 20 || strlen($password) < 5) {
        echo 'Password must be between 5 and 20 characters!';
        return;
    }

    $stmt = $connection->prepare('SELECT user_id, expires_at FROM password_resets WHERE reset_code = ?');
    $stmt->execute([$resetToken]);

    if ($stmt->rowCount() == 0) {
        echo 'Invalid or expired reset token!';
        return;
    }

    $resetData = $stmt->fetch();
    if (new DateTime() > new DateTime($resetData['expires_at'])) {
        echo 'Reset token has expired!';
        return;
    }

    $userId = $resetData['user_id'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $updateStmt = $connection->prepare('UPDATE users SET password = ? WHERE id = ?');
    if ($updateStmt->execute([$passwordHash, $userId])) {
        $deleteStmt = $connection->prepare('DELETE FROM password_resets WHERE reset_code = ?');
        $deleteStmt->execute([$resetToken]);

        echo 'Password has been reset successfully!';
    } else {
        echo 'Error updating password: ' . $updateStmt->errorInfo()[2];
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
                                 <h2 class="mb-2" style="font-weight: bold; text-decoration: underline;">Reset Password</h2>
                                 <p style="font-weight: bold; text-decoration: underline;">Enter your New Password</p>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <label style="font-weight: bold; text-decoration: underline;" >New Password</label>
                                        <input type="password" name="Password" required>
                                        <label style="font-weight: bold; text-decoration: underline;">Confirm Password</label>
                                        <input type="password" name="ConfirmPassword" required>
                                    </form>
                                    </div>
                                    <button class="btn btn-primary mt-3" type="submit" name="reset_password">Reset Password</button>
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
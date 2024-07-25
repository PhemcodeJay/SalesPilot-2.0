<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require 'C:\xampp\htdocs\project\vendor\autoload.php'; // Include the Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include the database connection settings
include('config.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Initialize variables with default values
    $username = $password = $email = $confirmpassword = "";

    // Check if the keys exist in the $_POST array before accessing them
    if (isset($_POST["Username"])) {
        $username = htmlspecialchars($_POST["Username"]);
    }

    if (isset($_POST["Password"])) {
        $password = htmlspecialchars($_POST["Password"]);
    }

    if (isset($_POST["Email"])) {
        $email = htmlspecialchars($_POST["Email"]);
    }

    if (isset($_POST["confirmpassword"])) {
        $confirmpassword = htmlspecialchars($_POST["confirmpassword"]);
    }

    if (isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {
        header("Location: reg-success.html");
        exit(); // Add exit to stop the script execution
    }

    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    // Call the function to handle form submission
    handleFormSubmission($username, $password, $email, $confirmpassword, $connection, $mail);
}

// Function to handle form submission and insert data in the database
function handleFormSubmission($username, $password, $email, $confirmpassword, $connection, $mail)
{
    // Validate form data
    if (empty($username) || empty($password) || empty($email) || empty($confirmpassword)) {
        echo 'All fields are required!';
        return;
    }
    
    if (strlen($password) > 20 || strlen($password) < 5) {
        echo 'Password must be between 5 and 20 characters!';
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format!';
        return;
    }
    
    if (preg_match('/^[a-zA-Z0-9]+$/', $username) == 0) {
        echo 'Username can only contain letters and numbers!';
        return;
    }

    if ($password !== $confirmpassword) {
        echo 'Passwords do not match!';
        return;
    }

    // Check if Username or Email already exists
    $stmt = $connection->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        echo 'Username or Email already exists, please choose another!';
    } else {
        // Insert new user record
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $activationCode = uniqid();

        $insertStmt = $connection->prepare('INSERT INTO users (username, password, email, confirmpassword) VALUES (?, ?, ?, ?)');
        if ($insertStmt->execute([$username, $passwordHash, $email, $confirmpassword])) {
            $userId = $connection->lastInsertId();

            // Insert activation code
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
            $activationStmt = $connection->prepare('INSERT INTO activation_codes (user_id, activation_code, expires_at) VALUES (?, ?, ?)');
            if ($activationStmt->execute([$userId, $activationCode, $expiresAt])) {
                // Send activation email
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 465;
                    $mail->SMTPAuth = true;
                    $mail->Username = 'olphemie@gmail.com'; // Replace with your Gmail email
                    $mail->Password = 'itak uyjg empc blnp'; // Replace with your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

                    $mail->setFrom('olphemie@gmail.com', 'SalesPilot');
                    $mail->addAddress($email);
                    $mail->Subject = 'Activate Your Account';
                    $mail->Body = 'Hello,<br>Click the link below to activate your account:<br><a href="http://localhost/project/activate.php?token=' . $activationCode . '">Activate Account</a>';

                    if ($mail->send()) {
                        header("Location: reg-success.html"); // Redirect after sending activation email
                        exit(); // Add exit to stop the script execution
                    } else {
                        echo 'Error sending activation email: ' . $mail->ErrorInfo;
                    }
                } catch (Exception $e) {
                    echo 'Mailer Error: ' . $e->getMessage();
                }
            } else {
                echo 'Error inserting activation code into the database: ' . $activationStmt->errorInfo()[2];
            }
        } else {
            echo 'Error inserting user record into the database: ' . $insertStmt->errorInfo()[2];
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="http://localhost/project/home_assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="http://localhost/project/home_assets/img/favicon.png">
  <title>Sales Pilot - Sign Up</title>
  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="http://localhost/project/home_assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="http://localhost/project/home_assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="http://localhost/project/home_assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body>
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12"></div>
    </div>
  </div>
  <main class="main-content mt-0">
  <section>
  <div class="page-header min-vh-100">
    <div class="container">
      <div class="row">
        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
          <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80'); background-size: cover;"></div>
        </div>
        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="font-weight-bolder">Sign Up</h4>
              <p class="mb-0">Enter your details to register</p>
            </div>
            <div class="card-body">
              <form role="form" id="registrationForm" method="POST" action="sign-up.php" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="input-group input-group-outline mb-3">
                  <input type="text" class="form-control" id="Username" name="Username" placeholder="Enter your username" required>
                </div>
                <div class="input-group input-group-outline mb-3">
                  <input type="password" class="form-control" id="Password" name="Password" placeholder="Enter your password" required>
                </div>
                <div class="input-group input-group-outline mb-3">
                  <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm your password" required>
                </div>
                <div class="input-group input-group-outline mb-3">
                  <input type="email" class="form-control" id="Email" name="Email" placeholder="Enter your email" required>
                </div>
                <div class="form-check form-check-info text-start ps-0">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
                  <label class="form-check-label" for="flexCheckDefault">
                    I agree to the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                  </label>
                </div>
                <div class="text-center">
                  <button type="submit" name="signup" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Sign Up</button>
                </div>
              </form>
            </div>
            <div class="card-footer text-center pt-0 px-lg-2 px-1">
              <p class="mb-2 text-sm mx-auto">
                Already have an account?
                <a href="http://localhost/project/loginpage.php" class="text-primary text-gradient font-weight-bold">Sign in</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
  </main>
  <!-- Core JS Files -->
  <script src="http://localhost/project/home_assets/js/core/popper.min.js"></script>
  <script src="http://localhost/project/home_assets/js/core/bootstrap.min.js"></script>
  <script src="http://localhost/project/home_assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="http://localhost/project/home_assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
</body>

</html>

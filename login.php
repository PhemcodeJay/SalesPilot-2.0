<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

include('config.php'); // This file should set up the PDO connection as $connection

// Check if the user is already logged in, if yes, redirect to the dashboard page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: user-confirm.html");
    exit();
}

$username_err = $password_err = $login_err = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

    if (empty($username)) {
        $username_err = "Please enter a username.";
    }

    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    if (empty($username_err) && empty($password_err)) {
        try {
            $stmt = $connection->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_user = $user['id'];
                $db_username = $user['username'];
                $passwordHash = $user['password'];

                if (password_verify($password, $passwordHash)) {
                    // Set session variables only after successful login
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id_user"] = $id_user;
                    $_SESSION["username"] = $db_username;

                    // Debugging
                    error_log("Login successful. Session ID: " . session_id());
                    error_log("Session variables: " . print_r($_SESSION, true));

                    header("Location: user-confirm.html");
                    exit();
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                $login_err = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            exit("Database error: " . $e->getMessage());
        }
    }
}

// Close the database connection
$connection = null;
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>POS Dash | Responsive Bootstrap 4 Admin Dashboard Template</title>
      
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
                                 <h2 class="mb-2">Sign In</h2>
                                 <p>Login to stay connected.</p>
                                 <form>
                                    <div class="row">
                                       <div class="col-lg-12">
                                          <div class="floating-label form-group">
                                             <input class="floating-input form-control" type="username" placeholder=" ">
                                             <label>Username</label>
                                          </div>
                                       </div>
                                       <div class="col-lg-12">
                                          <div class="floating-label form-group">
                                             <input class="floating-input form-control" type="password" placeholder=" ">
                                             <label>Password</label>
                                          </div>
                                       </div>
                                       <div class="col-lg-6">
                                          <div class="custom-control custom-checkbox mb-3">
                                             <input type="checkbox" class="custom-control-input" id="customCheck1">
                                             <label class="custom-control-label control-label-1" for="customCheck1">Remember Me</label>
                                          </div>
                                       </div>
                                       <div class="col-lg-6">
                                          <a href="auth-recoverpw.html" class="text-primary float-right">Forgot Password?</a>
                                       </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Sign In</button>
                                    <p class="mt-3">
                                       Create an Account <a href="auth-sign-up.html" class="text-primary">Sign Up</a>
                                    </p>
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
    
    <!-- Chart Custom JavaScript -->
    <script src="http://localhost/project/assets/js/customizer.js"></script>
    
    <!-- Chart Custom JavaScript -->
    <script async src="http://localhost/project/assets/js/chart-custom.js"></script>
    
    <!-- app JavaScript -->
    <script src="http://localhost/project/assets/js/app.js"></script>
  </body>
</html>
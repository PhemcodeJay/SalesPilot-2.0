<?php
include('config.php');

// First, check if the email and code exist in the query parameters.
if (isset($_GET['Email'], $_GET['code'])) {
    $email = $_GET['Email'];
    $code = $_GET['code'];

    // Prepare and execute a SELECT query to check if the activation code exists for the given email.
    if ($stmt = $con->prepare('SELECT * FROM dbs13455438.activation_codes WHERE Email = ? AND activation_code = ?')) {
        $stmt->bind_param('ss', $email, $code);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Activation code exists, proceed to activate the account.
            // Prepare an UPDATE query to set the account as activated in the users table.
            if ($updateStmt = $con->prepare('UPDATE dbs13455438.users SET activation_code = ? WHERE Email = ?')) {
                $newcode = 'activated';
                $updateStmt->bind_param('ss', $newcode, $email);
                $updateStmt->execute();
                
                // Delete the activation code entry after successful activation
                if ($deleteStmt = $con->prepare('DELETE FROM dbs13455438.activation_codes WHERE Email = ? AND activation_code = ?')) {
                    $deleteStmt->bind_param('ss', $email, $code);
                    $deleteStmt->execute();
                }
                
                // Display the success message with a link to login
                echo '<div style="text-align: center; padding: 20px; font-family: Arial, sans-serif;">
                        <h2>Your account is now activated!</h2>
                        <p>You can now log in to your account.</p>
                        <a href="loginpage.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; font-weight: bold; border-radius: 5px;">Go to Login</a>
                      </div>';
            } else {
                echo 'Database update error.';
            }
        } else {
            echo 'The account is already activated or doesn\'t exist!';
        }
    } else {
        echo 'Database query error.';
    }

    // Check activation status and display content accordingly.
    $query = "SELECT activation_code FROM dbs13455438.users WHERE Email = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($activationStatus);
        $stmt->fetch();
        
        if ($activationStatus == 'activated') {
            echo 'Your account has already been activated!';
        }
    }
} else {
    echo 'Invalid activation link.';
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Account Activation</title>
    <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend-plugin.min.css">
    <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend.css?v=1.0.0">
    <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/remixicon/fonts/remixicon.css">
</head>
<body>
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
                                            <h2 class="mb-2" style="font-weight: bold; text-decoration: underline;">Activate Your Account</h2>
                                            <p style="font-weight: bold; text-decoration: underline;">Click below to log in now</p>
                                            <a href="loginpage.php" class="btn btn-primary mt-3" style="font-weight: bold; text-decoration: underline;">Go to Login</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 content-right">
                                        <img src="https://salespilot.cybertrendhub.store/assets/images/login/01.png" class="img-fluid image-right" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://salespilot.cybertrendhub.store/assets/js/backend-bundle.min.js"></script>
    <script src="https://salespilot.cybertrendhub.store/assets/js/table-treeview.js"></script>
    <script src="https://salespilot.cybertrendhub.store/assets/js/app.js"></script>
</body>
</html>

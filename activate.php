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
                
                echo 'Your account is now activated! You can now <a href="loginpage.php">Login</a>!';
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
            echo 'user-confirm.html.';
        }
    }
} else {
    echo 'Invalid activation link.';
}
?>



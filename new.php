<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            margin-top: 100px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        .message {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message">
            <?php
            include('config.php');

            if (isset($_GET['Email'], $_GET['code'])) {
                $email = $_GET['Email'];
                $code = $_GET['code'];

                if ($stmt = $con->prepare('SELECT * FROM sales_pilot.activation_codes WHERE Email = ? AND activation_code = ?')) {
                    $stmt->bind_param('ss', $email, $code);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows > 0) {
                        if ($updateStmt = $con->prepare('UPDATE sales_pilot.users SET activation_code = ? WHERE Email = ?')) {
                            $newcode = 'activated';
                            $updateStmt->bind_param('ss', $newcode, $email);
                            $updateStmt->execute();
                            
                            if ($deleteStmt = $con->prepare('DELETE FROM sales_pilot.activation_codes WHERE Email = ? AND activation_code = ?')) {
                                $deleteStmt->bind_param('ss', $email, $code);
                                $deleteStmt->execute();
                            }
                            
                            echo 'Your account is now activated! You can now <a class="link" href="profile.php">login</a>!';
                        } else {
                            echo 'Database update error.';
                        }
                    } else {
                        echo 'The account is already activated or doesn\'t exist!';
                    }
                } else {
                    echo 'Database query error.';
                }

                $query = "SELECT activation_code FROM sales_pilot.users WHERE Email = ?";
                if ($stmt = $con->prepare($query)) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $stmt->bind_result($activationStatus);
                    $stmt->fetch();
                    
                    if ($activationStatus == 'activated') {
                        echo '<br>Redirecting to <a class="link" href="user-confirm.html">User Confirmation Page</a>';
                    }
                }
            } else {
                echo 'Invalid activation link.';
            }
            ?>
        </div>
    </div>
</body>
</html>

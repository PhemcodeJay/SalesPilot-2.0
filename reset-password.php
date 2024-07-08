<?php
session_start();
include('config.php');

function resetPassword($email, $newPassword, $connection)
{
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            return "Password reset successful!";
        } else {
            return "Error updating password: " . $stmt->error;
        }

    } catch (Exception $e) {
        return $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"]; // Use lowercase "email" and "new_password"
    $newPassword = $_POST["new_password"];

    $connection = new mysqli($hostname, $username, $password, $database);

    if ($connection->connect_error) {
        exit("Error: " . $connection->connect_error);
    }

    $resultMessage = resetPassword($email, $newPassword, $connection);

    echo $resultMessage;

    $connection->close();
}
// Check if 'Username' is set in the session before using it.
$Username = isset($_SESSION['Username']) ? $_SESSION['Username'] : "";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
    <style>
        /* styles.css */

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background-image: linear-gradient(to right, #108dc7, #ef8e38);
            color: #fff;
            text-align: center;
            padding: 20px;
            height: 100px;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        header img {
            max-width: 150px;
            width: 100%;
            height: auto;
        }

        header h1 {
            font-size: 24px;
            margin: 10px 0;
            color: #e4e6eb;
        }

        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container .dashboard-card {
            position: relative;
            top: -112px;
            left: -132px;
            width: 572px;
            transform: translateX(117px) translateY(-35px);
        }

        /* Input */
        .dashboard-container .dashboard-card form input[type=email] {
            width: 90% !important;
        }

        /* Input */
        .dashboard-container .dashboard-card form input[type=password] {
            width: 94% !important;
        }

        .dashboard-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-card h2 {
            margin-top: 0;
            color: #007bff;
        }

        .dashboard-card label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .dashboard-card input[type="text"],
        .dashboard-card input[type="password"],
        .dashboard-card input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        header img {
            transform: translateX(24px) translateY(-26px);
        }

        /* Heading */
        header h1 {
            transform: translateX(-144px) translateY(38px);
        }

        /* Dashboard card */
        .dashboard-container .dashboard-card {
            transform: translateX(199px) translateY(-43px);
        }

        /* Footer */
        .dashboard-container footer {
            transform: translateX(-495px) translateY(5px);
            width: 442px;
        }

        .button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .dashboard-container .dashboard-card{
 transform:translatex(119px) translatey(-25px);
 min-height:238px;
 height:238px;
}

header h1{
 position:relative;
 left:45px;
}

/* Image */
header img{
 transform:translatex(59px) translatey(-19px);
}

/* Input */
.dashboard-card form input{
 position:relative;
 top:2px;
}

/* Footer */
footer{
 transform:translatex(-3px) translatey(-275px);
}
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        footer p {
            margin: 5px;
        }
    </style>
</head>

<body>
    <header>
        <img class="app-logo" src="http://localhost/WEB/salespilot.png" alt="Sales Pilot Logo">
        <h1><?php echo $Username; ?></h1>
    </header>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <h2>Reset Password</h2>
            <form method="post">
                <label for="email">Email:</label>
                <input type="email" name="email" required><br>
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required><br>
                <input type="submit" value="Reset Password">
            </form>
        </div>
    </div>
</body>
<footer>
    <p>Last Data Update: <span id="currentDateTime"></span></p>
    <p>Contact Us: olphemie@sales-pilot.com</p>
</footer>

</html>

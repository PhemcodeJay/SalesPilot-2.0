<?php
session_start();
require_once 'config.php'; // Include your DB connection file

// Check if username is set in session
if (!isset($_SESSION["username"])) {
    throw new Exception("No username found in session.");
}

$username = htmlspecialchars($_SESSION["username"]);


// Check if action is set to 'activate_trial'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'activate_trial') {
    
    // Step 1: Check if the user already has an active subscription
    $query = "SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $existing_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user already has an active subscription, return an error
    if ($existing_subscription) {
        echo json_encode(['success' => false, 'message' => 'You already have an active subscription']);
        exit();
    }

    // Step 2: Check if the user has already used the free trial
    if ($existing_subscription && $existing_subscription['is_free_trial_used'] == 1) {
        echo json_encode(['success' => false, 'message' => 'You have already used your free trial']);
        exit();
    }

    // Step 3: Calculate the 3-month free trial period
    $start_date = new DateTime();
    $end_date = new DateTime();
    $end_date->add(new DateInterval('P3M')); // Add 3 months to the trial

    // Step 4: Insert the free trial into the subscriptions table
    $query = "INSERT INTO subscriptions (user_id, subscription_plan, start_date, end_date, status, is_free_trial_used) 
              VALUES (?, 'starter', ?, ?, 'active', 1)";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id, $start_date->format('Y-m-d H:i:s'), $end_date->format('Y-m-d H:i:s')]);

    if ($stmt->rowCount() > 0) {
        // Step 5: Insert payment record for the free trial
        $query = "INSERT INTO payments (user_id, payment_method, payment_proof, payment_amount, payment_status, subscription_id) 
                  VALUES (?, 'free', 'N/A', 0.00, 'completed', LAST_INSERT_ID())";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Free trial activated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error recording payment for free trial']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error activating free trial']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>

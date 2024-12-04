<?php
// Include the database configuration file
include 'config.php'; // Path to your config.php file

// Handle PayPal Webhook if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming webhook payload
    $rawPayload = file_get_contents("php://input");
    $payload = json_decode($rawPayload, true);

    // Validate the event
    if (!empty($payload) && isset($payload['event_type'])) {
        // Event types to listen for
        switch ($payload['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                // Subscription activated logic
                $subscriptionId = $payload['resource']['id'];
                $planId = $payload['resource']['plan_id'];
                $userId = getUserIdBySubscription($subscriptionId); // Replace with actual logic to get user ID from your database

                // Determine the plan type based on the plan_id
                $planName = getPlanName($planId);

                if ($planName) {
                    activateSubscription($subscriptionId, $planName, $userId);
                } else {
                    logError("Unknown plan ID: $planId");
                }
                break;

            case 'PAYMENT.SALE.COMPLETED':
                // Payment completed logic
                $saleId = $payload['resource']['id'];
                $amount = $payload['resource']['amount']['total'];
                $userId = getUserIdByPayment($saleId); // Replace with actual logic to get user ID

                // Insert payment information
                recordPayment($saleId, $amount, $userId);
                break;

            // Add additional event types as needed
            default:
                logError("Unhandled event type: " . $payload['event_type']);
                break;
        }

        // Respond with a success status to acknowledge receipt of the webhook
        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Respond with an error status if the payload is invalid
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Function to get the plan name based on plan_id
function getPlanName($planId) {
    $plans = [
        'P-92V01000GH171635WM5HYGRQ' => 'starter',
        'P-6TP94103DT2394623M5HYFKY' => 'business',
        'P-7E210255TM029860GM5HYC4A' => 'enterprise'
    ];
    return $plans[$planId] ?? null;
}

// Function to activate the subscription
function activateSubscription($subscriptionId, $planName, $userId) {
    global $connection; // Use the global connection object

    // Insert subscription data
    $query = "INSERT INTO subscriptions (user_id, subscription_plan, status) VALUES (?, ?, 'active')";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->bindParam(2, $planName, PDO::PARAM_STR);

    if ($stmt->execute()) {
        file_put_contents('webhook_log.txt', "Subscription activated: ID = $subscriptionId, User ID = $userId, Plan = $planName\n", FILE_APPEND);
    } else {
        logError("Subscription insert failed: " . $stmt->errorInfo());
    }
}

// Function to record payment
function recordPayment($saleId, $amount, $userId) {
    global $connection; // Use the global connection object

    // Insert payment data
    $query = "INSERT INTO payments (user_id, payment_method, payment_amount, payment_status, subscription_id) 
              VALUES (?, 'paypal', ?, 'completed', ?)";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->bindParam(2, $amount, PDO::PARAM_STR);
    $stmt->bindParam(3, $subscriptionId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        file_put_contents('webhook_log.txt', "Payment recorded: Sale ID = $saleId, Amount = $amount, User ID = $userId\n", FILE_APPEND);
    } else {
        logError("Payment insert failed: " . $stmt->errorInfo());
    }
}

// Function to log errors
function logError($message) {
    file_put_contents('webhook_log.txt', "Error: $message\n", FILE_APPEND);
}

// Function to get the user ID based on subscription ID (replace with your own logic)
function getUserIdBySubscription($subscriptionId) {
    // Example: Query your database to find the user based on the subscription ID
    return 123; // Replace with actual user ID logic
}

// Function to get the user ID based on sale ID (replace with your own logic)
function getUserIdByPayment($saleId) {
    // Example: Query your database to find the user based on the payment/sale ID
    return 123; // Replace with actual user ID logic
}
?>

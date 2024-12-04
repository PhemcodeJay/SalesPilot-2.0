<?php
// Set your PayPal client ID and secret
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';

// PayPal's webhook verification URL
$paypalWebhookUrl = 'https://api.paypal.com/v1/notifications/verify-webhook-signature';

// Webhook request body
$bodyReceived = file_get_contents('php://input');
$headers = getallheaders();

// PayPal sends a 'paypal-auth-algo' header which contains the signature algorithm used to generate the signature
$authAlgo = $headers['paypal-auth-algo'];
$certUrl = $headers['paypal-cert-url'];
$transmissionId = $headers['paypal-transmission-id'];
$transmissionSig = $headers['paypal-transmission-sig'];
$timestamp = $headers['paypal-transmission-time'];

// Prepare the verification request payload
$verificationData = [
    'auth_algo' => $authAlgo,
    'cert_url' => $certUrl,
    'transmission_id' => $transmissionId,
    'transmission_sig' => $transmissionSig,
    'transmission_time' => $timestamp,
    'webhook_id' => 'YOUR_WEBHOOK_ID',
    'webhook_event' => json_decode($bodyReceived),
];

// Send the verification request to PayPal API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paypalWebhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($verificationData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
]);

$response = curl_exec($ch);
curl_close($ch);

$verificationResponse = json_decode($response, true);

// If PayPal verifies the webhook, process the event
if ($verificationResponse['verification_status'] === 'SUCCESS') {
    $event = json_decode($bodyReceived, true);

    // Check the event type
    if ($event['event_type'] === 'PAYMENT.SALE.COMPLETED') {
        // Handle the payment confirmation (payment has been successfully processed)
        $subscriptionId = $event['resource']['billing_agreement_id']; // Get subscription ID from the event
        $payerId = $event['resource']['payer']['payer_info']['payer_id'];

        // Call your database to activate the subscription
        activateSubscription($subscriptionId, $payerId);
    }

    if ($event['event_type'] === 'BILLING.SUBSCRIPTION.CREATED') {
        // Handle the subscription creation
        $subscriptionId = $event['resource']['id']; // Subscription ID
        $subscriberEmail = $event['resource']['subscriber']['email_address'];

        // Call your database to save the subscription details and activate it
        activateSubscription($subscriptionId, $subscriberEmail);
    }

    // Add additional event types as needed
} else {
    // Verification failed
    error_log('Webhook verification failed');
}

// Function to activate the subscription in your database using PDO
function activateSubscription($subscriptionId, $payerId) {
    // Database connection using PDO
    try {
        $db = new PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the subscription already exists
        $stmt = $db->prepare("SELECT * FROM subscriptions WHERE subscription_id = :subscriptionId");
        $stmt->bindParam(':subscriptionId', $subscriptionId);
        $stmt->execute();

        // If the subscription doesn't exist, insert it
        if ($stmt->rowCount() === 0) {
            $stmt = $db->prepare("INSERT INTO subscriptions (subscription_id, payer_id, status) VALUES (:subscriptionId, :payerId, 'active')");
            $stmt->bindParam(':subscriptionId', $subscriptionId);
            $stmt->bindParam(':payerId', $payerId);
            $stmt->execute();
        } else {
            // Update the existing subscription status
            $stmt = $db->prepare("UPDATE subscriptions SET status = 'active' WHERE subscription_id = :subscriptionId");
            $stmt->bindParam(':subscriptionId', $subscriptionId);
            $stmt->execute();
        }

        // Optionally send an email or do further processing
        // mail($userEmail, "Subscription Activated", "Your subscription has been activated.");
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
    }
}
?>

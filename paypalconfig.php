<?php

// PayPal Configuration - replace with your actual credentials
define('PAYPAL_CLIENT_ID', 'your_client_id_here');
define('PAYPAL_SECRET', 'your_secret_key_here');
define('PAYPAL_SANDBOX', true);  // Set to false for production

// Your database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'salespilot');
define('DB_USER', 'root');
define('DB_PASS', 'dbs13455438');

// Set up PDO connection
try {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include PayPal SDK
require 'vendor/autoload.php';

// Set up PayPal API context
$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        PAYPAL_CLIENT_ID,
        PAYPAL_SECRET
    )
);

// Set the PayPal environment (sandbox for testing or live for production)
if (PAYPAL_SANDBOX) {
    $apiContext->setConfig([
        'mode' => 'sandbox',  // Use 'live' for production
        'http.headers.PayPal-Partner-Attribution-Id' => 'your-partner-id', // Optional, can be removed if not required
    ]);
} else {
    $apiContext->setConfig([
        'mode' => 'live',
        'http.headers.PayPal-Partner-Attribution-Id' => 'your-partner-id', // Optional, can be removed if not required
    ]);
}

// You can now use $apiContext to make PayPal API calls

?>


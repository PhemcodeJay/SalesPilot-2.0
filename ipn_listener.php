<?php
// ipn_listener.php

// Read PayPal's response
$rawPostData = file_get_contents('php://input');
$rawPostArray = explode('&', $rawPostData);
$myPost = array();

foreach ($rawPostArray as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

$ipnMessage = http_build_query($myPost);

// PayPal's IPN verification endpoint
$paypalUrl = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'; // Use 'https://ipnpb.paypal.com/cgi-bin/webscr' for live environment

// Send the data back to PayPal for verification
$ch = curl_init($paypalUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ipnMessage . '&cmd=_notify-validate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// If PayPal's response is VERIFIED, handle the payment confirmation
if (strcmp($response, "VERIFIED") == 0) {
    // Handle the IPN verification success and update the database
    // Example: Update user subscription status in your database
    $user_id = $_POST['custom']; // Custom field set during payment
    // Update subscription status or activate user subscription here
}
?>

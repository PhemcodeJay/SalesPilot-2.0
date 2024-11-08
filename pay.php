<?php
session_start();
require 'config.php'; // Include your database configuration file

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    throw new Exception("No username found in session.");
}

$username = htmlspecialchars($_SESSION["username"]);

// Sample product details (fetch from your database)
$plans = [
    'starter' => [
        'monthly' => 5,  // Monthly price for Starter Plan
    ],
    'growth' => [
        'monthly' => 15, // Monthly price for Growth Plan
    ],
    'enterprise' => [
        'monthly' => 25, // Monthly price for Enterprise Plan
    ],
];

// Default selection
$selected_plan = 'starter';
$selected_cycle = 'monthly';
$total_price = $plans[$selected_plan][$selected_cycle];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected payment method and plan details
    $payment_method = $_POST['payment'];
    $selected_plan = $_POST['plan'];
    $selected_cycle = $_POST['cycle'];
    $total_price = $plans[$selected_plan][$selected_cycle];

    // Process payment based on selected method
    try {
        $paymentData = [
            'amount' => $total_price,
            'method' => $payment_method,
            'description' => "Payment for " . ucfirst($selected_plan) . " Plan",
            'order_id' => uniqid(), // Generate a unique order ID
            'phone_number' => $_POST['phone_number'] ?? null, // For M-Pesa
        ];

        // Call the payment processing function
        processPayment($payment_method, $paymentData);
    } catch (Exception $e) {
        echo 'Payment Error: ' . $e->getMessage();
    }
}

function processPayment($paymentMethod, $paymentData) {
    switch ($paymentMethod) {
        case 'paypal':
            processPaypalPayment($paymentData);
            break;

        case 'binance':
            processBinancePayment($paymentData);
            break;

        case 'mpesa':
            processMpesaPayment($paymentData);
            break;

        case 'bank-transfer':
            processBankTransferPayment($paymentData);
            break;

        default:
            throw new Exception("Unsupported payment method: " . $paymentMethod);
    }
}

function processPaypalPayment($data) {
    // PayPal API credentials and processing logic
    $paypalUrl = 'https://api.paypal.com/v1/payments/payment'; // PayPal API URL
    $ch = curl_init($paypalUrl);

    $postFields = json_encode([
        'intent' => 'sale',
        'redirect_urls' => [
            'return_url' => 'https://salespilot.cybertrendhub.store/success',
            'cancel_url' => 'https://salespilot.cybertrendhub.store/cancel',
        ],
        'payer' => [
            'payment_method' => 'paypal',
        ],
        'transactions' => [[
            'amount' => [
                'total' => $data['amount'],
                'currency' => 'USD',
            ],
            'description' => $data['description'],
        ]],
    ]);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . getPaypalAccessToken(),
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['links'])) {
        foreach ($responseData['links'] as $link) {
            if ($link['rel'] == 'approval_url') {
                header('Location: ' . $link['href']);
                exit;
            }
        }
    } else {
        throw new Exception("PayPal payment error: " . $responseData['message']);
    }
}

function getPaypalAccessToken() {
    $clientId = 'AZYvY1lNRIJ-1uKK0buXQvvblKWefjilgca9HAG6YHTYkfFvriP-OHcrUZsv2RCohiWCl59FyvFUST-W'; // Your PayPal client ID
    $secret = 'EDpaVPowMoKSoA_pyshhfkour_aIIMJC0kSHMjgyaXkxvmq9H4CNVrj-2afCZ_Zxf9wCjb9zBIcLOcez'; // Your PayPal secret
    $url = 'https://api.paypal.com/v1/oauth2/token';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Accept-Language: en_US',
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response, true);
    
    return $responseData['access_token'];
}

function processBinancePayment($data) {
    // Check if required data is available
    if (empty($data['amount']) || empty($data['method'])) {
        throw new Exception("Payment data is incomplete.");
    }

    // Binance Pay API credentials (replace with actual credentials)
    $apiKey = 'oerorywnqozkuillondw6i3agatww7ohql5tqkoiozhjra9fdzxui6xqvssbqgcl';
    $apiSecret = 'anadyqw1l3u4abjd3lu6xkpqf88pd5ik0hnxhrlnrnxgpn8rhjgbvqtk8yrrqaqi';


    // Setup Binance API endpoint (replace with actual endpoint)
    $endpoint = 'https://api.binance.com/v3/payments'; // Example endpoint
    $payload = [
        'amount' => $data['amount'],
        'currency' => 'USD', // Specify the currency
        'method' => $data['method'],
        // Additional parameters can be added here as required by Binance
    ];

    try {
        // Initialize cURL session for API call
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-MBX-APIKEY: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check for API response success
        if ($httpCode === 200) {
            // Process successful response
            $responseData = json_decode($response, true);

            // Check for successful payment status in the response
            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                echo "Binance payment processed successfully for amount: " . $data['amount'] . " USD.";
                
                // Save successful payment to database
                savePayment($data['amount'], $data['method'], 'success');
            } else {
                throw new Exception("Payment failed: " . ($responseData['message'] ?? 'Unknown error'));
            }
        } else {
            throw new Exception("API call failed with HTTP code: " . $httpCode);
        }
    } 
}


function processMpesaPayment($data) {
    $lipaNaMpesaOnlineShortcode = 'YOUR_SHORTCODE'; // Your M-Pesa Shortcode
    $lipaNaMpesaOnlineKey = 'YOUR_MPESA_KEY'; // Your M-Pesa Key
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; // Sandbox URL for testing

    // Create the payment request payload using phone number
    $payload = json_encode([
        'BusinessShortCode' => $lipaNaMpesaOnlineShortcode,
        'Amount' => $data['amount'],
        'PartyA' => $data['phone_number'], // Using phone number
        'PartyB' => $lipaNaMpesaOnlineShortcode,
        'PhoneNumber' => $data['phone_number'],
        'CallBackURL' => 'https://salespilot.cybertrendhub.store/callback',
        'AccountReference' => $data['order_id'],
        'TransactionDesc' => $data['description'],
    ]);

    // Send the payment request to M-Pesa
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . getMpesaAccessToken($lipaNaMpesaOnlineKey),
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Handle the response
    $responseData = json_decode($response, true);
    if ($responseData['ResponseCode'] == '0') {
        // Payment initiated successfully
        echo "M-Pesa payment initiated successfully.";
        // Save payment to database
        savePayment($data['amount'], $data['method'], 'success', [
            'phone_number' => $data['phone_number'],
        ]);
    } else {
        throw new Exception("M-Pesa payment error: " . $responseData['ResponseDescription']);
    }
}

function getMpesaAccessToken($key) {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $clientId = 'YOUR_CLIENT_ID'; // Your M-Pesa Client ID

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $key),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response, true);
    
    return $responseData['access_token'];
}

function processBankTransferPayment($data) {
    // Check if the file was uploaded
    if (isset($_FILES['payment_proof'])) {
        $uploadDir = 'uploads/payment_proofs/'; // Directory to save uploaded files
        $uploadFile = $uploadDir . basename($_FILES['payment_proof']['name']);
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png'])) {
            throw new Exception("Invalid file type. Only PDF, JPG, and PNG files are allowed.");
        }

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadFile)) {
            // Payment proof uploaded successfully
            echo "Payment proof uploaded successfully!<br>";
            // Save payment to database
            savePayment($data['amount'], $data['method'], 'pending', [
                'proof_file' => $uploadFile,
            ]);
        } else {
            throw new Exception("Error uploading payment proof.");
        }
    } // Modal trigger button
    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bankTransferModal">
            Show Bank Transfer Payment Details
          </button>';
    
    // Modal HTML structure
    echo '<div class="modal fade" id="bankTransferModal" tabindex="-1" role="dialog" aria-labelledby="bankTransferModalLabel" aria-hidden="true">';
    echo '  <div class="modal-dialog" role="document">';
    echo '    <div class="modal-content">';
    echo '      <div class="modal-header">';
    echo '        <h5 class="modal-title" id="bankTransferModalLabel">Bank Transfer Payment Details</h5>';
    echo '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    echo '          <span aria-hidden="true">&times;</span>';
    echo '        </button>';
    echo '      </div>';
    echo '      <div class="modal-body">';
    echo '        <p>Bank Name: Stanbic IBTC</p>';
    echo '        <p>Account Number: 1234567890</p>';
    echo '        <p>Account Name: Your Business Name</p>';
    echo '        <p>Upload Payment Proof (PDF/Image):</p>';
    echo '        <form method="post" enctype="multipart/form-data">';
    echo '          <input type="file" name="payment_proof" required>';
    echo '          <button type="submit" class="btn btn-primary mt-2">Upload</button>';
    echo '        </form>';
    echo '      </div>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
    
}

function savePayment($amount, $method, $status, $extraData = []) {
    global $connection; // Get the PDO instance from your config file

    // Prepare the SQL statement
    $sql = "INSERT INTO payments (amount, method, status, created_at, payment_proof) VALUES (:amount, :method, :status, NOW(), :payment_proof)";
    
    // Prepare and execute the statement
    $stmt = $connection->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':method', $method);
    $stmt->bindParam(':status', $status);

    // Bind the payment proof if provided
    $paymentProof = isset($extraData['proof_file']) ? $extraData['proof_file'] : null;
    $stmt->bindParam(':payment_proof', $paymentProof);

    // Execute the SQL statement
    if ($stmt->execute()) {
        echo "Payment recorded successfully!";
    } else {
        echo "Error recording payment.";
    }
}


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Subscriptions</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="https://salespilot.cybertrendhub.store/assets/images/favicon.ico" />
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend-plugin.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/remixicon/fonts/remixicon.css">  </head>
      <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
       
        header {
    background-color: #87CEEB; /* Light blue */
    color: white;
    padding: 10px 20px;
    text-align: center;
    position: relative; /* Needed for absolute positioning of the scrolling element */
    overflow: hidden; /* Hide overflow to create a clean effect */
    height: 100px; /* Adjust header height as needed */
}


.free-trial {
    position: center; /* Position it absolutely within the header */
    white-space: nowrap; /* Prevent text wrapping */
    animation: scroll 10s linear infinite; /* Adjust duration for speed */
}

.free-trial-button {
    background-color: #FFA500;
    color: white; /* White text */
    padding: 10px 15px; /* Padding */
    text-decoration: none; /* No underline */
    border-radius: 5px; /* Rounded corners */
}

@keyframes scroll {
    0% {
        transform: translateX(100%); /* Start off-screen to the right */
    }
    100% {
        transform: translateX(-100%); /* End off-screen to the left */
    }
}

        .pricing {
            padding: 20px;
        }
        .buy-btn {
            background-color: #FFA500; /* Orange */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .buy-btn:hover {
            background-color: #e59400; /* Darker orange */
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #87CEEB; /* Light blue */
            color: white;
        }
        .payment-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }
        .payment-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px;
            padding: 10px;
            text-align: center;
            width: 150px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .payment-option:hover {
            background-color: #f1f1f1;
        }
        .payment-logo {
            width: 100px;
            height: auto;
        }
        .order-summary {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .pay-button {
            background-color: #FFA500; /* Orange */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 0 auto;
        }
        .pay-button:hover {
            background-color: #e59400; /* Darker orange */
        }

        .centered {
            text-align: center;
        }

        .logo {
    font-size: 2.5rem; /* Increase font size */
    font-weight: bold; /* Make the font bold for better visibility */
    color: white; /* Text color */
    text-align: center; /* Center the text */
    animation: float 3s ease-in-out infinite; /* Animation effect */
}

/* Animation for the logo */
@keyframes float {
    0%, 100% {
        transform: translateY(0); /* Original position */
    }
    50% {
        transform: translateY(-10px); /* Move up */
    }
}

    </style>
  
      <body class="  ">
    
    <!-- Wrapper Start -->
    <div class="wrapper">
      
      <div class="iq-sidebar  sidebar-default ">
          <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
              <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                  <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">SalesPilot</h5>
              </a>
              <div class="iq-menu-bt-sidebar ml-0">
                  <i class="las la-bars wrapper-menu"></i>
              </div>
          </div>
          <div class="data-scrollbar" data-scroll="1">
              <nav class="iq-sidebar-menu">
                  <ul id="iq-sidebar-toggle" class="iq-menu">
                      <li class="">
                          <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="svg-icon">                        
                              <svg  class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>
                              </svg>
                              <span class="ml-4">Dashboards</span>
                          </a>
                      </li>
                      <li class=" ">
                          <a href="#product" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash2" width="20" height="20"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                              </svg>
                              <span class="ml-4">Products</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="product" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                              <li class="">
                                  <a href="https://salespilot.cybertrendhub.store/page-list-product.php">
                                      <i class="las la-minus"></i><span>List Product</span>
                                  </a>
                              </li>
                              <li class="">
                                  <a href="https://salespilot.cybertrendhub.store/page-add-product.php">
                                      <i class="las la-minus"></i><span>Add Product</span>
                                  </a>
                              </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#category" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash3" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                              </svg>
                              <span class="ml-4">Categories</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="category" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-category.php">
                                              <i class="las la-minus"></i><span>List Category</span>
                                          </a>
                                  </li>
                                 
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#sale" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash4" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                              </svg>
                              <span class="ml-4">Sale</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="sale" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-sale.php">
                                              <i class="las la-minus"></i><span>List Sale</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-sale.php">
                                              <i class="las la-minus"></i><span>Add Sale</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#purchase" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash5" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                  <line x1="1" y1="10" x2="23" y2="10"></line>
                              </svg>
                              <span class="ml-4">Expenses</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="purchase" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-expense.php">
                                              <i class="las la-minus"></i><span>List Expenses<pan>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-expense.php">
                                              <i class="las la-minus"></i><span>Add Expenses</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#return" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash6" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="14" y1="10" x2="21" y2="3"></line><line x1="3" y1="21" x2="10" y2="14"></line>
                              </svg>
                              <span class="ml-4">Inventory</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="return" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-inventory.php">
                                              <i class="las la-minus"></i><span>List Inventory</span>
                                          </a>
                                  </li>
                              
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#people" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash8" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                              </svg>
                              <span class="ml-4">People</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="active">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-customers.php">
                                              <i class="las la-minus"></i><span>Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-customers.php">
                                              <i class="las la-minus"></i><span>Add Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-staffs.php">
                                              <i class="las la-minus"></i><span>Staff</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-staffs.php">
                                              <i class="las la-minus"></i><span>Add Staffs</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-suppliers.php">
                                              <i class="las la-minus"></i><span>Suppliers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-supplier.php">
                                              <i class="las la-minus"></i><span>Add Suppliers</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                        <a href="#otherpage" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash9" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect>
                            </svg>
                            <span class="ml-4">Analytics</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="otherpage" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/analytics.php">
                                            <i class="las la-minus"></i><span>Charts</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/analytics-report.php">
                                            <i class="las la-minus"></i><span>Reports</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/sales-metrics.php">
                                            <i class="las la-minus"></i><span>Sales Metrics</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/inventory-metrics.php">
                                            <i class="las la-minus"></i><span>Inventory Metrics</span>
                                        </a>
                                </li>
                                
                        </ul>
                    </li>   
                     </ul>
              </nav>
              <div id="sidebar-bottom" class="position-relative sidebar-bottom">
                  <div class="card border-none">
                      <div class="card-body p-0">
                         
                      </div>
                  </div>
              </div>
              <div class="p-3"></div>
          </div>
          </div>      <div class="iq-top-navbar">
          <div class="iq-navbar-custom">
              <nav class="navbar navbar-expand-lg navbar-light p-0">
                  <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                      <i class="ri-menu-line wrapper-menu"></i>
                      <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                          <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal" alt="logo">
                          <h5 class="logo-title ml-3">SalesPilot</h5>
      
                      </a>
                  </div>
                  <div class="iq-search-bar device-search">
                      <form action="#" class="searchbox">
                          <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                          <input type="text" class="text search-input" placeholder="Search here...">
                      </form>
                  </div>
                  <div class="d-flex align-items-center">
                      <button class="navbar-toggler" type="button" data-toggle="collapse"
                          data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                          aria-label="Toggle navigation">
                          <i class="ri-menu-3-line"></i>
                      </button>
                      <div class="collapse navbar-collapse" id="navbarSupportedContent">
                          <ul class="navbar-nav ml-auto navbar-list align-items-center">
                             
                              <li>
                                  <a href="#" class="btn border add-btn shadow-none mx-2 d-none d-md-block"
                                      data-toggle="modal" data-target="#new-order"><i class="las la-plus mr-2"></i>New
                                      Invoice</a>
                              </li>
                              <li class="nav-item nav-icon search-content">
                                  <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown"
                                      aria-haspopup="true" aria-expanded="false">
                                      <i class="ri-search-line"></i>
                                  </a>
                                  <div class="iq-search-bar iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch">
                                      <form action="#" class="searchbox p-2">
                                          <div class="form-group mb-0 position-relative">
                                              <input type="text" class="text search-input font-size-12"
                                                  placeholder="type here to search...">
                                              <a href="#" class="search-link"><i class="las la-search"></i></a>
                                          </div>
                                      </form>
                                  </div>
                              </li>
                              <li class="nav-item nav-icon dropdown">
    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-bell">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="bg-primary "></span>
    </a>
    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
        <div class="card shadow-none m-0">
            <div class="card-body p-0">
                <div class="cust-title p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Notifications</h5>
                        <a class="badge badge-primary badge-card" href="#">
                            <?= count($inventoryNotifications) + count($reportsNotifications) ?>
                        </a>
                    </div>
                </div>
                <div class="px-3 pt-0 pb-0 sub-card">

                    <?php if (!empty($inventoryNotifications)): ?>
                        <?php foreach ($inventoryNotifications as $notification): ?>
                            <a href="#" class="iq-sub-card">
                                <div class="media align-items-center cust-card py-3 border-bottom">
                                    <div>
                                        <img class="avatar-50 rounded-small"
                                            src="<?= htmlspecialchars($notification['image_path']); ?>" 
                                            alt="<?= htmlspecialchars($notification['product_name']); ?>">
                                    </div>
                                    <div class="media-body ml-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><?= htmlspecialchars($notification['product_name']); ?></h6>
                                            <small class="text-dark">
                                                <b>Available: <?= htmlspecialchars($notification['available_stock']); ?></b>
                                            </small>
                                        </div>
                                        <small>Inventory: <?= htmlspecialchars($notification['inventory_qty']); ?>, 
                                        Sales: <?= htmlspecialchars($notification['sales_qty']); ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No inventory notifications available.</p>
                    <?php endif; ?>

                    <?php if (!empty($reportsNotifications)): ?>
                        <?php foreach ($reportsNotifications as $notification): ?>
                            <a href="#" class="iq-sub-card">
                                <div class="media align-items-center cust-card py-3 border-bottom">
                                    <div>
                                        <img class="avatar-50 rounded-small"
                                            src="<?= htmlspecialchars($notification['image_path']); ?>" 
                                            alt="<?= htmlspecialchars($notification['product_name']); ?>">
                                    </div>
                                    <div class="media-body ml-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><?= htmlspecialchars($notification['product_name']); ?></h6>
                                            <small class="text-dark">
                                                <b>Revenue: <?= htmlspecialchars($notification['revenue']); ?></b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No reports notifications available.</p>
                    <?php endif; ?>
                </div>
                <a class="right-ic btn btn-primary btn-block position-relative p-2" href="#" role="button">
                    View All
                </a>
            </div>
        </div>
    </div>
</li>

                              <li class="nav-item nav-icon dropdown caption-content">
                                  <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4"
                                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                  </a>
                                  <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <div class="card shadow-none m-0">
                                          <div class="card-body p-0 text-center">
                                              <div class="media-body profile-detail text-center">
                                                  <img src="https://salespilot.cybertrendhub.store/assets/images/page-img/profile-bg.jpg" alt="profile-bg"
                                                      class="rounded-top img-fluid mb-4">
                                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                              </div>
                                              <div class="p-3">
                                                <h5 class="mb-1"><?php echo $email; ?></h5>
                                                <p class="mb-0">Since <?php echo $date; ?></p>
                                                  <div class="d-flex align-items-center justify-content-center mt-3">
                                                      <a href="https://salespilot.cybertrendhub.store/user-profile-edit.php" class="btn border mr-2">Profile</a>
                                                      <a href="logout.php" class="btn border">Sign Out</a>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
              </nav>
          </div>
      </div>
      <div class="modal fade" id="new-order" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <h4 class="mb-3">New Invoice</h4>
                    <div class="content create-workform bg-body">
                        <div class="pb-3">
                            <label class="mb-2">Name</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Enter Customer Name">
                        </div>
                        <div class="col-lg-12 mt-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-center">
                                <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                <div class="btn btn-outline-primary" id="createButton">Create</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<body>
<header>
    <div class="logo">Pricing Plans</div>
    <div class="free-trial">
        <a href="sign-up.php" class="free-trial-button">Free Trial - 3 Months</a>
    </div>
</header>


    <main>
    <section id="pricing" class="pricing">
        <div class="container section-title" data-aos="fade-up">
            <h2>Sales Pilot</h2>
            <p>Choose the plan that aligns with your business size and ambitions, unlocking the power of comprehensive inventory and sales analytics.</p>
        </div>

        <div class="container" data-aos="zoom-in" data-aos-delay="100">
            <div class="row g-4">
                <?php 
                $plans = [
                    'starter' => ['monthly' => 5],
                    'growth' => ['monthly' => 15],
                    'enterprise' => ['monthly' => 25]
                ];
                $planNames = ['starter' => 'Starter Plan (Small)', 'growth' => 'Growth Plan (Medium)', 'enterprise' => 'Enterprise Plan (Big)'];
                foreach ($planNames as $key => $planName): ?>
                    <div class="col-lg-4">
                        <div class="pricing-item<?= ($key === 'growth') ? ' featured' : '' ?>">
                            <h3><?php echo $planName; ?></h3>
                            <h4><sup>$</sup><?php echo $plans[$key]['monthly']; ?><span> / month</span></h4>
                            <ul>
                                <li><i class="bi bi-check"></i> <span><?php echo ($key === 'starter') ? 'Ideal for startups and small businesses.' : (($key === 'growth') ? 'Designed for growing businesses.' : 'Tailored for large enterprises.'); ?></span></li>
                                <li><i class="bi bi-check"></i> <span><?php echo ($key === 'starter') ? 'Affordable pricing tailored to your budget.' : (($key === 'growth') ? 'Expanded features for enhanced scalability.' : 'Robust features, advanced analytics, and customization options.'); ?></span></li>
                                <li><i class="bi bi-check"></i> <span><?php echo ($key === 'starter') ? 'Essential features for efficient inventory and sales management.' : (($key === 'growth') ? 'Competitive pricing to support your expanding needs.' : 'Scalable solutions to meet the demands of your extensive operations.'); ?></span></li>
                            </ul>
                            <div class="text-center">
                                <button class="buy-btn" onclick="selectPlan('<?php echo $key; ?>')">Buy Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <h1 class="centered">Select Your Payment Method</h1>
    <form method="POST" action="">
        <div class="payment-options">
            <?php 
            $paymentMethods = [
                'paypal' => 'PayPal',
                'binance' => 'Binance Pay',
                'mpesa' => 'M-Pesa',
                'bank-transfer' => 'Nigerian Bank Transfer'
            ];
            foreach ($paymentMethods as $key => $method): ?>
                <div class="payment-option" onclick="selectPaymentMethod('<?php echo $key; ?>')">
                    <img src="https://salespilot.cybertrendhub.store/uploads/images/<?php echo $key; ?>-logo.png" alt="<?php echo $method; ?>" class="payment-logo">
                    <input type="radio" name="payment" value="<?php echo $key; ?>" required style="display: none;"> <?php echo $method; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <p><strong>Selected Plan:</strong> <span id="plan-summary"><?php echo ucfirst($selected_plan); ?></span></p>
            <p><strong>Payment Cycle:</strong> <span id="cycle-summary"><?php echo ucfirst($selected_cycle); ?></span></p>
            <p><strong>Total Amount:</strong> <span id="total-price">$<?php echo $total_price; ?></span></p>
        </div>

        <input type="hidden" name="plan" id="selected-plan" value="<?php echo $selected_plan; ?>">
        <input type="hidden" name="cycle" id="selected-cycle" value="<?php echo $selected_cycle; ?>">
        <button type="submit" class="pay-button">Proceed to Payment</button>
        <button type="button" class="pay-button" onclick="window.location.href='index.html';">Home</button>

    </form>
  
</main>


   <!-- Wrapper End-->
   <footer class="iq-footer">
            <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/privacy-policy.php">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/terms-of-service.php">Terms of Use</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="">SalesPilot</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/table-treeview.js"></script>
    
    <!-- app JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/app.js"></script>
    

    <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    // Function to handle plan selection
    function selectPlan(plan) {
        document.getElementById('selected-plan').value = plan;
        document.getElementById('plan-summary').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
        updateTotalPrice(plan);
    }

    // Function to handle payment method selection
    function selectPaymentMethod(paymentMethod) {
        // Deselect all radio buttons
        const options = document.querySelectorAll('input[name="payment"]');
        options.forEach(option => {
            option.checked = false;
        });

        // Select the chosen payment method
        const selectedOption = document.querySelector(`input[value="${paymentMethod}"]`);
        selectedOption.checked = true;

        // Add a visual highlight to the selected option
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            option.style.backgroundColor = ''; // Reset background color
        });
        const selectedPaymentOption = selectedOption.parentElement;
        selectedPaymentOption.style.backgroundColor = '#87CEEB'; // Light blue for selected option
    }

    // Function to update the total price based on the selected plan
    function updateTotalPrice(selectedPlan) {
        const cycle = document.getElementById('selected-cycle').value;

        // Prices could be dynamically fetched or defined here
        const prices = {
            'starter': { 'monthly': <?php echo $plans['starter']['monthly']; ?> },
            'growth': { 'monthly': <?php echo $plans['growth']['monthly']; ?> },
            'enterprise': { 'monthly': <?php echo $plans['enterprise']['monthly']; ?> },
        };

        const totalPrice = prices[selectedPlan][cycle];
        document.getElementById('total-price').textContent = '$' + totalPrice.toFixed(2);
    }
</script>
<script>
document.getElementById('createButton').addEventListener('click', function() {
    // Optional: Validate input or perform any additional checks here
    
    // Redirect to invoice-form.php
    window.location.href = 'invoice-form.php';
});
</script>
</body>
</html>

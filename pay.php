<?php
session_start();
require 'config.php'; // Include your database configuration file

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
            'return_url' => 'https://yourwebsite.com/success',
            'cancel_url' => 'https://yourwebsite.com/cancel',
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
    $clientId = 'YOUR_PAYPAL_CLIENT_ID'; // Your PayPal client ID
    $secret = 'YOUR_PAYPAL_SECRET'; // Your PayPal secret
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
    $apiKey = 'YOUR_API_KEY';
    $apiSecret = 'YOUR_API_SECRET';

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
    } catch (Exception $e) {
        // Handle errors gracefully
        echo "Error processing Binance payment: " . $e->getMessage();
        // Optionally save payment with status 'failed' or log error
        savePayment($data['amount'], $data['method'], 'failed', ['error_message' => $e->getMessage()]);
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
        'CallBackURL' => 'https://yourwebsite.com/callback',
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    
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
</head>
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


    <footer>
        <p>&copy; 2024 SalesPilot. All rights reserved.</p>
    </footer>

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

</body>
</html>

<?php
session_start();
require 'config.php'; // Include your database configuration file

// Sample product details (you can replace this with actual database queries)
$plans = [
    'starter' => ['monthly' => 5], // Starter Plan monthly price
    'growth' => ['monthly' => 15],  // Growth Plan monthly price
    'enterprise' => ['monthly' => 25] // Enterprise Plan monthly price
];

// Default selection
$selected_plan = 'starter';
$selected_cycle = 'monthly';
$total_price = $plans[$selected_plan][$selected_cycle];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected payment method and plan details
    $payment_method = $_POST['payment'] ?? 'paypal'; // Default to PayPal if no method is selected
    $selected_plan = $_POST['plan'] ?? 'starter';
    $selected_cycle = $_POST['cycle'] ?? 'monthly';
    $total_price = $plans[$selected_plan][$selected_cycle];

    // Prepare payment data
    $paymentData = [
        'amount' => $total_price,
        'method' => $payment_method,
        'description' => "Payment for " . ucfirst($selected_plan) . " Plan",
        'order_id' => uniqid(), // Generate a unique order ID
        'phone_number' => $_POST['phone_number'] ?? null, // Optional phone number for M-Pesa
    ];

    // Call the payment processing function
    try {
        processPayment($payment_method, $paymentData);
    } catch (Exception $e) {
        echo 'Payment Error: ' . $e->getMessage();
    }
}

/**
 * Process payment based on selected method.
 *
 * @param string $paymentMethod
 * @param array $paymentData
 * @throws Exception
 */
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

/**
 * Process PayPal payment.
 *
 * @param array $data
 */
function processPaypalPayment($data) {
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

/**
 * Get PayPal access token.
 *
 * @return string
 */
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

/**
 * Process Binance payment.
 *
 * @param array $data
 */
function processBinancePayment($data) {
    try {
        $endpoint = 'https://api.binance.com/api/v3/order';  // Binance API endpoint for placing an order
        $apiKey = 'YOUR_BINANCE_API_KEY';  // Replace with your actual Binance API Key
        $apiSecret = 'YOUR_BINANCE_API_SECRET';  // Replace with your actual Binance API Secret

        // Define the order parameters
        $payload = [
            'symbol' => 'USDTUSDT',  // Example pair: USDT/USDT, adjust as needed
            'side' => 'BUY',  // For a payment, you could also consider 'SELL'
            'type' => 'MARKET',  // Example: market order (adjust as needed)
            'quantity' => $data['amount'],  // Amount to pay in terms of the cryptocurrency
            'timestamp' => time() * 1000,  // Current time in milliseconds
        ];

        // Generate the signature
        $queryString = http_build_query($payload);  // Query string format
        $signature = hash_hmac('sha256', $queryString, $apiSecret);  // Generate HMAC SHA256 signature

        // Add the signature to the payload
        $payload['signature'] = $signature;

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . http_build_query($payload));  // Append payload as query string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: ' . $apiKey,  // Add API key to request headers
        ]);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $responseData = json_decode($response, true);

            // Check if the response indicates success
            if (isset($responseData['status']) && $responseData['status'] === 'FILLED') {
                savePayment($data['amount'], 'Binance', 'success', $data);
                echo "Binance payment processed successfully.";
            } else {
                throw new Exception("Payment failed: " . ($responseData['msg'] ?? 'Unknown error'));
            }
        } else {
            throw new Exception("API call failed with HTTP code: " . $httpCode);
        }
    } catch (Exception $e) {
        // Handle exceptions gracefully
        echo "Error processing Binance payment: " . $e->getMessage();
        savePayment($data['amount'], 'Binance', 'failed', ['error_message' => $e->getMessage()]);
    }
}


/**
 * Process M-Pesa payment.
 *
 * @param array $data
 */
function processMpesaPayment($data) {
    $lipaNaMpesaOnlineShortcode = 'YOUR_SHORTCODE'; // Your M-Pesa Shortcode
    $lipaNaMpesaOnlineKey = 'YOUR_MPESA_KEY'; // Your M-Pesa Key
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; // M-Pesa sandbox URL

    $payload = json_encode([
        'BusinessShortCode' => $lipaNaMpesaOnlineShortcode,
        'Amount' => $data['amount'],
        'PartyA' => $data['phone_number'],
        'PartyB' => $lipaNaMpesaOnlineShortcode,
        'PhoneNumber' => $data['phone_number'],
        'CallBackURL' => 'https://yourwebsite.com/callback',
        'AccountReference' => $data['order_id'],
        'TransactionDesc' => $data['description'],
    ]);

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

    $responseData = json_decode($response, true);
    if ($responseData['ResponseCode'] == '0') {
        savePayment($data['amount'], $data['method'], 'success', ['phone_number' => $data['phone_number']]);
        echo "M-Pesa payment initiated successfully.";
    } else {
        throw new Exception("M-Pesa payment error: " . $responseData['ResponseDescription']);
    }
}

/**
 * Get M-Pesa access token.
 *
 * @param string $key
 * @return string
 */
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

/**
 * Process bank transfer payment.
 *
 * @param array $data
 */
function processBankTransferPayment($data) {
    // Logic to handle bank transfer payment
    savePayment($data['amount'], 'Bank Transfer', 'pending');
    echo "Bank transfer payment is being processed. Please complete the transfer.";
}

/**
 * Save payment details to the database.
 *
 * @param float $amount
 * @param string $method
 * @param string $status
 * @param array $extraData
 */
function savePayment($amount, $method, $status, $extraData = []) {
    global $connection; // Assuming you have a PDO instance called $connection

    $sql = "INSERT INTO payments (amount, method, status, created_at, payment_proof, phone_number, error_message, order_id, description)
            VALUES (:amount, :method, :status, NOW(), :payment_proof, :phone_number, :error_message, :order_id, :description)";

    $stmt = $connection->prepare($sql);

    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':method', $method);
    $stmt->bindParam(':status', $status);

    $paymentProof = $extraData['payment_proof'] ?? null;
    $stmt->bindParam(':payment_proof', $paymentProof);
    
    $stmt->bindParam(':phone_number', $extraData['phone_number'] ?? null);
    $stmt->bindParam(':error_message', $extraData['error_message'] ?? null);
    $stmt->bindParam(':order_id', $extraData['order_id']);
    $stmt->bindParam(':description', $extraData['description']);

    $stmt->execute();
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
<script>
document.getElementById('createButton').addEventListener('click', function() {
    // Optional: Validate input or perform any additional checks here
    
    // Redirect to invoice-form.php
    window.location.href = 'invoice-form.php';
});
</script>
</body>
</html>

<?php
session_start();
require 'config.php'; // Include your database configuration file

require __DIR__ . '/vendor/autoload.php'; // Include the Composer autoloader



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Define the plans and their corresponding prices for each cycle
$plans = [
    'starter' => ['monthly' => 5],
    'growth' => ['monthly' => 15],
    'enterprise' => ['monthly' => 25]
];

// Set default values for selected plan, cycle, and total price
$selected_plan = 'starter';
$selected_cycle = 'monthly';
$total_price = $plans[$selected_plan][$selected_cycle];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected payment method, plan, cycle, and other data
    $payment_method = $_POST['payment'] ?? 'paypal';
    $selected_plan = $_POST['plan'] ?? 'starter';
    $selected_cycle = $_POST['cycle'] ?? 'monthly';
    $total_price = $plans[$selected_plan][$selected_cycle];

    // Prepare payment data
    $paymentData = [
        'amount' => $total_price,
        'method' => $payment_method,
        'description' => "Payment for " . ucfirst($selected_plan) . " Plan",
        'order_id' => uniqid(),
        'phone_number' => $_POST['phone_number'] ?? null
    ];

    // Handle payment based on the selected method
    try {
        if ($payment_method === 'bank-transfer') {
            // If Bank Transfer is selected, trigger modal on frontend
            echo '<script>document.addEventListener("DOMContentLoaded", function() { showBankTransferModal(); });</script>';
        } else {
            // For other payment methods, process the payment
            processPayment($payment_method, $paymentData);
        }
    } catch (Exception $e) {
        echo 'Payment Error: ' . $e->getMessage();
    }
}

/**
 * Process payment based on selected method.
 *
 * @param string $paymentMethod The payment method (e.g., PayPal, Binance)
 * @param array $paymentData Payment data including amount, method, etc.
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
            // Handled by modal
            break;
        default:
            throw new Exception("Unsupported payment method: " . $paymentMethod);
    }
}

/**
 * Process PayPal payment.
 *
 * @param array $data Payment data
 */
function processPaypalPayment($data) {
    $paypalUrl = 'https://api.paypal.com/v1/payments/payment';
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
 * @return string PayPal access token
 */
function getPaypalAccessToken() {
    $clientId = 'AZYvY1lNRIJ-1uKK0buXQvvblKWefjilgca9HAG6YHTYkfFvriP-OHcrUZsv2RCohiWCl59FyvFUST-W';
    $secret = 'EDpaVPowMoKSoA_pyshhfkour_aIIMJC0kSHMjgyaXkxvmq9H4CNVrj-2afCZ_Zxf9wCjb9zBIcLOcez';
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
 * @param array $data Payment data
 */
function processBinancePayment($data) {
    try {
        $endpoint = 'https://api.binance.com/api/v3/order';
        $apiKey = 'oerorywnqozkuillondw6i3agatww7ohql5tqkoiozhjra9fdzxui6xqvssbqgcl';
        $apiSecret = 'anadyqw1l3u4abjd3lu6xkpqf88pd5ik0hnxhrlnrnxgpn8rhjgbvqtk8yrrqaqi';

        $payload = [
            'symbol' => 'USDTUSDT',
            'side' => 'BUY',
            'type' => 'MARKET',
            'quantity' => $data['amount'],
            'timestamp' => time() * 1000,
        ];

        $queryString = http_build_query($payload);
        $signature = hash_hmac('sha256', $queryString, $apiSecret);
        $payload['signature'] = $signature;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: ' . $apiKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
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
        echo "Error processing Binance payment: " . $e->getMessage();
        savePayment($data['amount'], 'Binance', 'failed', ['error_message' => $e->getMessage()]);
    }
}

/**
 * Process M-Pesa payment in live mode.
 *
 * @param array $data Payment data
 */
function processMpesaPayment($data) {
    // Use live URL and credentials
    $liveUrl = "https://api.safaricom.co.ke/mpesa_url"; // Replace with actual live endpoint
    $liveCredentials = [
        'consumer_key' => 'YOUR_LIVE_CONSUMER_KEY',
        'consumer_secret' => 'YOUR_LIVE_CONSUMER_SECRET',
    ];

    // Set up the live environment
    $response = initiateMpesaRequest($liveUrl, $liveCredentials, $data);
    
    // Process response and save payment record
    if ($response['status'] === 'success') {
        savePayment($data['amount'], 'M-Pesa (Live)', 'success', $data);
    } else {
        savePayment($data['amount'], 'M-Pesa (Live)', 'failure', $data);
    }
}

/**
 * Helper function to initiate M-Pesa request in live mode.
 */
function initiateMpesaRequest($url, $credentials, $data) {
    // Implement the actual API request logic here
    // Example: Use cURL to send the request to the live M-Pesa API

    // Example mock response for successful transaction
    return [
        'status' => 'success', // or 'failure' based on API response
        'transaction_id' => 'LIVE123456789'
    ];
}


/**
 * Save payment details to the database.
 *
 * @param float $amount Payment amount
 * @param string $method Payment method used (e.g., PayPal, Binance)
 * @param string $status Payment status (e.g., 'successful', 'failed')
 * @param array $extraData Additional payment information (e.g., order_id, description)
 */
function savePayment($amount, $method, $status, $extraData = []) {
    global $connection;

    $sql = "INSERT INTO payments (amount, method, status, created_at, payment_proof, phone_number, error_message, order_id, description)
            VALUES (:amount, :method, :status, NOW(), :payment_proof, :phone_number, :error_message, :order_id, :description)";

    $stmt = $connection->prepare($sql);

    $paymentProof = $extraData['payment_proof'] ?? null;
    $phoneNumber = $extraData['phone_number'] ?? null;
    $errorMessage = $extraData['error_message'] ?? null;
    $orderId = $extraData['order_id'] ?? null;
    $description = $extraData['description'] ?? null;

    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':method', $method);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':payment_proof', $paymentProof);
    $stmt->bindParam(':phone_number', $phoneNumber);
    $stmt->bindParam(':error_message', $errorMessage);
    $stmt->bindParam(':order_id', $orderId);
    $stmt->bindParam(':description', $description);

    $stmt->execute();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $formData = $_POST;
    $adminEmail = $_POST['adminEmail']; // Admin email address

    // You can add other form fields here as needed
    $orderId = $formData['order_id']; 
    $amount = $formData['amount']; // The amount paid, or the data you'd like to send in the email
    
    // Create email body
    $subject = "Payment Proof Submitted for Order #$orderId";
    $message = "
        <html>
        <head>
            <title>Payment Proof for Order #$orderId</title>
        </head>
        <body>
            <p>A payment proof has been submitted for order #$orderId.</p>
            <p><strong>Amount Paid:</strong> $$amount</p>
            <p><strong>Payment Proof:</strong> <a href='path/to/payment-proof/" . $formData['payment_proof'] . "'>View Proof</a></p>
        </body>
        </html>
    ";

    // Set content-type header for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // Additional headers
    $headers .= "From: webmaster@cybertrendhub.store" . "\r\n";

    // Send the email
    if (mail($adminEmail, $subject, $message, $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Error sending email.";
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
            background-color: #f4f6f9;
        }
        
        /* Header Styling */
        header {
            background-color: lightgoldenrodyellow; /* Bright Blue */
            color: blue;
            padding: 30px;
            text-align: center;
        }
        
        header .logo {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header-banner {
            font-size: 1.25rem;
            animation: scroll 15s linear infinite;
            white-space: nowrap;
        }

        
        
        @keyframes scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        
        /* Section Styling */
        .pricing {
            padding: 40px 0;
        }

        .pricing .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .pricing-item {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .pricing-item:hover {
            transform: translateY(-10px);
        }

        .pricing-item h3 {
            margin-top: 10px;
            font-size: 1.5rem;
        }
        
        .pricing-item h4 {
            font-size: 1.25rem;
            margin-top: 10px;
        }

        .pricing-item ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }

        .pricing-item ul li {
            margin: 10px 0;
            color: #555;
        }

        .buy-btn {
            background-color: #28a745; /* Green */
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buy-btn:hover {
            background-color: blue;
        }

        .buy-btn.selected {
            background-color: blue; /* Keep the color red when selected */
        }
            

        .buy-btn:active {
            background-color: blue; /* Color stays on click */
        }


        .payment-options {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .payment-option {
            border: 1px solid #ddd;
            padding: 15px;
            width: 160px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .payment-option:hover {
            background-color: #f0f0f0;
        }

        .payment-logo {
            max-width: 100px;
            height: auto;
        }

        .order-summary {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .pay-button {
            background-color: #007bff; /* Blue */
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            border: none;
            font-size: 1.1rem;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .pay-button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            margin-top: 50px;
        }

        footer p {
            color: #6c757d;
        }
        
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Sales Pilot</div>
    <div class="header-banner">
        <a href="sign-up.php" class="free-trial-button">Free Trial - 3 Months</a>
        <span> | </span>
        <a href="subscription.php" class="free-trial-button">Pay with Paypal and Get Extra Month</a>
    </div>
</header>

<main>
    <section class="pricing">
        <div class="container section-title">
            <h2>Choose Your Plan</h2>
            <p>Select a pricing plan that aligns with your business goals and grow with our powerful sales and inventory analytics tool.</p>
        </div>

        <div class="container">
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
                        <div class="pricing-item">
                            <h3><?php echo $planName; ?></h3>
                            <h4><sup>$</sup><?php echo $plans[$key]['monthly']; ?><span> / month</span></h4>
                            <ul>
                                <li><i class="bi bi-check"></i> <?php echo ($key === 'starter') ? 'Ideal for startups.' : (($key === 'growth') ? 'Designed for growing businesses.' : 'Tailored for enterprises.'); ?></li>
                                <li><i class="bi bi-check"></i> <?php echo ($key === 'starter') ? 'Affordable pricing for small businesses.' : (($key === 'growth') ? 'Features to support scalability.' : 'Advanced features for large-scale operations.'); ?></li>
                                <li><i class="bi bi-check"></i> <?php echo ($key === 'starter') ? 'Basic features for efficient sales.' : (($key === 'growth') ? 'Enhanced features for business growth.' : 'Scalable solutions for complex needs.'); ?></li>
                            </ul>
                            <div>
                                <button class="buy-btn" onclick="selectPlan('<?php echo $key; ?>')">Select Plan</button>
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
<!-- Bank Transfer Modal -->
<div class="modal fade" id="bankTransferModal" tabindex="-1" role="dialog" aria-labelledby="bankTransferModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bankTransferModalLabel">Bank Transfer Instructions</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6>Please follow the instructions below to complete your bank transfer:</h6>
        <ul>
          <li><strong>Bank Name:</strong> STANBIC IBTC NIGERIA</li>
          <li><strong>Account Name:</strong> OLUWAFEMI JEGEDE</li>
          <li><strong>Account Number:</strong> 0010414317</li>
          <li><strong>Bill Amount:</strong> <span id="bankAmountDisplay"></span></li>
          <li><strong>Exchange Rate:</strong> <span id="Naira Equivalent">Loading...</span></li>
          <li><strong>Total (NGN):</strong> <span id="totalAmountDisplay"></span></li>
        </ul>
        <h6>Once you have completed the transfer, please upload your payment proof:</h6>
        <form id="paymentProofForm" action="submit_payment_proof.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="paymentProof">Upload Payment Proof</label>
            <input type="file" class="form-control" id="paymentProof" name="payment_proof" required>
          </div>
          <input type="hidden" name="order_id" id="order_id" value="">
          <button type="submit" class="btn btn-primary">Submit Payment Proof</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Triggering Bank Transfer Modal -->
<script>
  // Function to show the Bank Transfer modal
  function showBankTransferModal() {
    // Set the total amount for the bank transfer in the modal
    var amount = <?php echo json_encode($total_price); ?>;
    document.getElementById("bankAmountDisplay").textContent = "$" + amount;

    // Set order_id if available
    var orderId = "<?php echo uniqid(); ?>";
    document.getElementById("order_id").value = orderId;

    // Show the modal
    $('#bankTransferModal').modal('show');
  }

  // Listen for when the form is submitted
  document.getElementById("paymentProofForm").addEventListener("submit", function(e) {
    e.preventDefault(); // Prevent default form submission

    var formData = new FormData(this);

    // AJAX request to submit the payment proof
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "pay.php", true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        // Handle success, maybe notify the user and close the modal
        alert("Payment proof submitted successfully!");
        $('#bankTransferModal').modal('hide');
        
        // Call the email function to send proof to admin
        sendEmailToAdmin(formData);
      } else {
        alert("Error submitting payment proof. Please try again.");
      }
    };
    xhr.send(formData);
  });

  // Function to send payment proof email to the admin
  function sendEmailToAdmin(formData) {
    // Create an AJAX request to send the email to admin
    var xhrEmail = new XMLHttpRequest();
    xhrEmail.open("POST", "pay.php", true);
    xhrEmail.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Prepare the form data for sending the email (you can add more form data if needed)
    var emailData = new URLSearchParams(formData).toString();
    emailData += "&adminEmail=admin@cybertrendhub.store"; // Set the admin's email address

    // Handle email sending response
    xhrEmail.onload = function() {
      if (xhrEmail.status === 200) {
        console.log("Email sent to admin successfully!");
      } else {
        console.log("Error sending email to admin.");
      }
    };

    // Send the email request
    xhrEmail.send(emailData);
  }
</script>



<!-- Footer -->
<footer>
    <p>&copy; 2024 Sales Pilot. All rights reserved.</p>
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
        async function getNairaEquivalent() {
            try {
                // Fetch the exchange rate from Exchangerate-API (you can replace this with another API if needed)
                const apiKey = 'cc688057dc86274ff7958e5e';  // Replace with your Exchangerate-API key
                const response = await fetch(`https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`);
                const data = await response.json();

                if (data && data.conversion_rates && data.conversion_rates.NGN) {
    const exchangeRate = data.conversion_rates.NGN;
    const amountInUSD = 1;  // You can replace this with the dynamic amount if needed
    const amountInNaira = (amountInUSD * exchangeRate).toFixed(2);  // Calculate Naira equivalent

    // Format the amount with commas for thousands
    const formattedAmountInNaira = parseFloat(amountInNaira).toLocaleString();

    document.getElementById('Naira Equivalent').textContent = `${formattedAmountInNaira} NGN`;
} else {
    document.getElementById('Naira Equivalent').textContent = "Exchange rate unavailable.";
}

            } catch (error) {
                console.error('Error fetching exchange rate:', error);
                document.getElementById('Naira Equivalent').textContent = "Error fetching exchange rate.";
            }
        }

        // Trigger the exchange rate conversion on page load
        document.addEventListener('DOMContentLoaded', getNairaEquivalent);
        
       
    </script>
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
        options.forEach(option => option.checked = false);

        // Select the chosen payment method
        const selectedOption = document.querySelector(`input[value="${paymentMethod}"]`);
        selectedOption.checked = true;

        // Add a visual highlight to the selected option
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => option.style.backgroundColor = ''); // Reset background color
        selectedOption.parentElement.style.backgroundColor = '#87CEEB'; // Light blue for selected option
    }

    // Function to update the total price based on the selected plan and cycle
    function updateTotalPrice(selectedPlan) {
        const cycle = document.getElementById('selected-cycle').value;

        // Prices could be dynamically fetched or predefined
        const prices = {
            'starter': { 'monthly': <?php echo $plans['starter']['monthly']; ?> },
            'growth': { 'monthly': <?php echo $plans['growth']['monthly']; ?> },
            'enterprise': { 'monthly': <?php echo $plans['enterprise']['monthly']; ?> },
        };

        const totalPrice = prices[selectedPlan][cycle];
        document.getElementById('total-price').textContent = '$' + totalPrice.toFixed(2);

        // Store the total price to use later in the exchange rate conversion
        window.selectedPlanPrice = totalPrice;
    }

    // Function to fetch the Naira equivalent exchange rate and calculate the total in NGN
    async function getNairaEquivalent() {
        try {
            const apiKey = 'cc688057dc86274ff7958e5e'; // Replace with your Exchangerate-API key
            const response = await fetch(`https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`);
            const data = await response.json();

            if (data && data.conversion_rates && data.conversion_rates.NGN) {
                const exchangeRate = data.conversion_rates.NGN;
                const formattedExchangeRate = parseFloat(exchangeRate).toLocaleString();

                // Update exchange rate display
                document.getElementById('Naira Equivalent').textContent = `${formattedExchangeRate} NGN`;

                // Calculate total in NGN based on the selected plan's price
                if (window.selectedPlanPrice) {
                    const amountInNaira = (window.selectedPlanPrice * exchangeRate).toFixed(2);
                    const formattedAmountInNaira = parseFloat(amountInNaira).toLocaleString();
                    document.getElementById('totalAmountDisplay').textContent = `${formattedAmountInNaira} NGN`;
                }

            } else {
                document.getElementById('Naira Equivalent').textContent = "Exchange rate unavailable.";
                document.getElementById('totalAmountDisplay').textContent = "Error calculating total.";
            }

        } catch (error) {
            console.error('Error fetching exchange rate:', error);
            document.getElementById('Naira Equivalent').textContent = "Error fetching exchange rate.";
            document.getElementById('totalAmountDisplay').textContent = "Error fetching exchange rate.";
        }
    }

    // Trigger the exchange rate conversion and default plan selection on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Default to 'starter' plan
        selectPlan('starter');
        getNairaEquivalent();  // Fetch exchange rate on page load
    });
</script>

<!-- HTML structure for displaying the details -->
<ul>
    <li><strong>Bill Amount:</strong> <span id="bankAmountDisplay">$0.00</span></li>
    <li><strong>Exchange Rate:</strong> <span id="Naira Equivalent">Loading...</span></li>
    <li><strong>Total (NGN):</strong> <span id="totalAmountDisplay">Loading...</span></li>
</ul>

</body>
</html>

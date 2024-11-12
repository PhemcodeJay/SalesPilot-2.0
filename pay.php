<?php 

session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);



// Include database connection
include('config.php');
require 'vendor/autoload.php';
require 'access_level.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: loginpage.php");
    exit;
}

$username = htmlspecialchars($_SESSION["username"]);


// Simulated exchange rates
$exchangeRates = [
    'USD' => 1,
    'KES' => 100,
    'NGN' => 450,
];

// Fetch exchange rates
function getExchangeRates($baseCurrency = 'USD') {
    $apiKey = 'cc688057dc86274ff7958e5e'; // Replace with your API key
    $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$baseCurrency}";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['result'] === 'success' ? $data['conversion_rates'] : null;
}

$conversionRates = getExchangeRates('USD') ?: $exchangeRates;

// Base prices and details in USD for each plan
$basePricingPlans = [
    'starter' => [
        'amount' => 5,
        'currency' => 'USD',
        'name' => 'Starter Plan',
        'details' => [
            'description' => 'Perfect for individuals or small startups just getting started.',
            'features' => [
                'Inventory Management',
                'Sales Management',
                'Invoices & Expenses',
                'Analytics & Reports',
                '24/7 Customer Service Support'
            ]
        ],
    ],
    'medium' => [
        'amount' => 15,
        'currency' => 'USD',
        'name' => 'Buisness Plan',
        'details' => [
            'description' => 'Great for small to medium-sized businesses looking to grow.',
            'features' => [
                'Inventory Management',
                'Sales Management',
                'Invoices & Expenses',
                'Analytics & Reports',
                'Customers, Staffs, Suppliers - Records',
                '24/7 Customer Service Support'
                
            ]
        ],
    ],
    'enterprise' => [
        'amount' => 25,
        'currency' => 'USD',
        'name' => 'Enterprise Plan',
        'details' => [
            'description' => 'Comprehensive solution for large businesses with advanced needs.',
            'features' => [
                'Inventory Management',
                'Sales Management',
                'Invoices & Expenses',
                'Analytics & Reports',
                'Customers, Staffs, Suppliers - Records',
                'Custom Integrations and Dedicated Support'
            ]
        ],
    ],
];

// Calculate prices in KES and NGN for each plan
$pricingPlans = [];
foreach ($basePricingPlans as $key => $plan) {
    $pricingPlans[$key] = [
        'name' => $plan['name'],
        'amount_USD' => $plan['amount'],
        'amount_KES' => round($plan['amount'] * $conversionRates['KES'], 2),
        'amount_NGN' => round($plan['amount'] * $conversionRates['NGN'], 2),
        'details' => $plan['details'],
    ];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesPilot - Pricing Plans</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fa; color: #333; }
        .header { text-align: center; margin: 30px 0; }
        .header img { max-width: 100px; }
        .pricing-plan { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: 0.3s; }
        .pricing-plan:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
        .pricing-plan h3 { color: #007bff; font-weight: bold; }
        .pricing-plan .price { font-size: 24px; margin: 10px 0; }
        .pricing-plan ul { list-style: none; padding: 0; }
        .pricing-plan ul li { padding: 5px 0; }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-warning { background-color: #ff9800; border: none; }
        .btn-success { background-color: #28a745; border: none; }
        .btn-info { background-color: #17a2b8; border: none; }
        .trial-container {
            text-align: center;
            margin-top: 50px;
        }
        .trial-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .trial-button:hover {
            background-color: #45a049;
        }
        .response-message {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="https://salespilot.cybertrendhub.store/logonew1.jpg" alt="Logo">
        <h1>Sales Pilot - Price and Plans</h1>
        <p>Select a plan that suits your needs and get started today!</p>
    </div>

    <div class="trial-container">
    <h2>Start Your Free 3-Month Trial!</h2>
    <button class="trial-button" onclick="activateTrial()">Activate Free Trial</button>
    <div id="responseMessage" class="response-message"></div>
    </div>

    <div class="row">
        <?php foreach ($pricingPlans as $planKey => $plan): ?>
            <div class="col-md-4 mb-4">
                <div class="pricing-plan text-center">
                    <h3><?= $plan['name'] ?></h3>
                    <p class="price">USD: $<?= number_format($plan['amount_USD'], 2) ?></p>
                    <p><strong>KES:</strong> <?= number_format($plan['amount_KES'], 2) ?></p>
                    <p><strong>NGN:</strong> <?= number_format($plan['amount_NGN'], 2) ?></p>

                    <p><?= $plan['details']['description'] ?></p>
                    <ul>
                        <?php foreach ($plan['details']['features'] as $feature): ?>
                            <li><?= $feature ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- PayPal Button -->
                    <form action="https://www.paypal.com/ncp/payment/B9NZE4X5V6ET6" method="post" target="_top">
                        <button class="btn btn-primary pp-B9NZE4X5V6ET6" type="submit">PayPal (USD)</button>
                    </form>

                    <!-- Payment Buttons for other options -->
                    <button class="btn btn-warning" data-toggle="modal" data-target="#binanceModal" 
                            data-currency="USD" data-amount="<?= $plan['amount_USD'] ?>" 
                            data-payment="Binance Pay" data-info="Pay with Binance">
                        Binance Pay (USDT)
                    </button>
                    <button class="btn btn-success" data-toggle="modal" data-target="#mpesaModal" 
                            data-currency="KES" data-amount="<?= $plan['amount_KES'] ?>" 
                            data-payment="M-Pesa" data-info="Pay via M-Pesa">
                        M-Pesa (KES)
                    </button>
                    <button class="btn btn-info" data-toggle="modal" data-target="#ngnModal" 
                            data-currency="NGN" data-amount="<?= $plan['amount_NGN'] ?>" 
                            data-payment="Bank Transfer (NGN)" data-info="Bank Transfer (NGN)">
                        Bank Transfer (NGN)
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Payment Modals -->
<div class="modal fade" id="binanceModal" tabindex="-1" aria-labelledby="binanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="binanceModalLabel">Binance Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="payment-info"></p>
                <p><strong>Amount:</strong> <span id="payment-amount"></span> <span id="payment-currency"></span></p>
                <p><strong>BINANCE PAY ID:</strong> 128 320 436 </p>
                <p><strong>BINANCE USERNAME:</strong> Phemcode</p>
                <p><strong>Email:</strong> sales@cybertrendhub.store</p>
                
                <!-- Payment Proof Upload Section -->
                <form id="paymentProofForm" action="upload-payment-proof.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="payment-proof">Upload Payment Proof</label>
                        <input type="file" class="form-control" id="payment-proof" name="payment-proof" accept="image/*, .pdf" required>
                        <small class="form-text text-muted">Upload a screenshot or PDF of your payment proof.</small>
                    </div>
                    <input type="hidden" name="payment-method" value="binance">
                    <input type="hidden" name="amount" id="hidden-amount">
                    <input type="hidden" name="currency" id="hidden-currency">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="paymentProofForm" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- M-Pesa Modal -->
<div class="modal fade" id="mpesaModal" tabindex="-1" aria-labelledby="mpesaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mpesaModalLabel">M-Pesa Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="payment-info"></p>
                <p><strong>Amount:</strong> <span id="payment-amount"></span> <span id="payment-currency"></span></p>
                <script>
                // Example amount in a variable, replace this with your actual value
                const amount = 1234567.89;
                const currency = "KES";

                // Format the amount with commas and set the values in the HTML
                document.getElementById("payment-amount").textContent = amount.toLocaleString();
                document.getElementById("payment-currency").textContent = currency;
                </script>
                <p><strong>Mpesa name:</strong> OLUWAFEMI JEGEDE</p>
                <p><strong>Mpesa Number:</strong> +254 111 826 872</p>
                <p><strong>Email:</strong>sales@cybertrendhub.store</p>

                <!-- Payment Proof Upload Section -->
                <form id="mpesaPaymentProofForm" action="upload-payment-proof.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="mpesa-payment-proof">Upload Payment Proof</label>
                        <input type="file" class="form-control" id="mpesa-payment-proof" name="payment-proof" accept="image/*, .pdf" required>
                        <small class="form-text text-muted">Upload a screenshot or PDF of your payment proof.</small>
                    </div>
                    <input type="hidden" name="payment-method" value="mpesa">
                    <input type="hidden" name="amount" id="mpesa-hidden-amount">
                    <input type="hidden" name="currency" id="mpesa-hidden-currency">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="mpesaPaymentProofForm" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- NGN Bank Transfer Modal -->
<div class="modal fade" id="ngnModal" tabindex="-1" aria-labelledby="ngnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ngnModalLabel">Bank Transfer (NGN) Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="payment-info"></p>
                <p><strong>Amount:</strong> <span id="payment-amount"></span> <span id="payment-currency"></span></p>
                <script>
                // Example amount in a variable, replace this with your actual value
                const amount1 = 1234567.89;
                const currency1 = "NGN";

                // Format the amount with commas and set the values in the HTML
                document.getElementById("payment-amount").textContent = amount.toLocaleString();
                document.getElementById("payment-currency").textContent = currency;
                </script>
                <p><strong>Bank Name:</strong> STANBIC IBTC Nigeria</p>
                <p><strong>Bank Account Number:</strong> 0141980198</p>
                <p><strong>Bank Account Name:</strong> JEGEDE OLUWAFEMI ADEGBOYE</p>
                <p><strong>Phone Number:</strong> +2348131365814</p>
                <p><strong>Email:</strong> sales@cybertrendhub.store</p>

                <!-- Payment Proof Upload Section -->
                <form id="ngnPaymentProofForm" action="upload-payment-proof.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="ngn-payment-proof">Upload Payment Proof</label>
                        <input type="file" class="form-control" id="ngn-payment-proof" name="payment-proof" accept="image/*, .pdf" required>
                        <small class="form-text text-muted">Upload a screenshot or PDF of your payment proof.</small>
                    </div>
                    <input type="hidden" name="payment-method" value="bank-transfer-ngn">
                    <input type="hidden" name="amount" id="ngn-hidden-amount">
                    <input type="hidden" name="currency" id="ngn-hidden-currency">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="ngnPaymentProofForm" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('#binanceModal, #mpesaModal, #ngnModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var currency = button.data('currency');
        var amount = button.data('amount');
        var paymentMethod = button.data('payment');
        var info = button.data('info');
        var modal = $(this);
        modal.find('.modal-title').text(paymentMethod);
        modal.find('.modal-body #payment-info').text(info);
        modal.find('.modal-body #payment-amount').text(amount);
        modal.find('.modal-body #payment-currency').text(currency);
    });
</script>
<script>
    function activateTrial() {
        // Show loading message while waiting for response
        document.getElementById("responseMessage").innerHTML = "Activating your free trial...";

        // Send a request to activate the free trial
        fetch('activate_trial.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'action=activate_trial'  // Send the action parameter
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("responseMessage").innerHTML = "Your free trial has been activated successfully!";
            } else {
                document.getElementById("responseMessage").innerHTML = "Error activating trial: " + data.message;
            }
        })
        .catch(error => {
            document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.message;
        });
    }
</script>

</body>
</html>

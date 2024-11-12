<?php 
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
        'amount' => 10,
        'currency' => 'USD',
        'name' => 'Starter Plan',
        'details' => [
            'description' => 'Perfect for individuals or small startups just getting started.',
            'features' => [
                '1 GB Storage',
                'Basic Support',
                'Access to Starter Resources'
            ]
        ],
    ],
    'medium' => [
        'amount' => 30,
        'currency' => 'USD',
        'name' => 'Medium Plan',
        'details' => [
            'description' => 'Great for small to medium-sized businesses looking to grow.',
            'features' => [
                '10 GB Storage',
                'Priority Support',
                'Access to All Resources',
                'Team Collaboration Tools'
            ]
        ],
    ],
    'enterprise' => [
        'amount' => 100,
        'currency' => 'USD',
        'name' => 'Enterprise Plan',
        'details' => [
            'description' => 'Comprehensive solution for large businesses with advanced needs.',
            'features' => [
                'Unlimited Storage',
                '24/7 Dedicated Support',
                'Access to Premium Resources',
                'Advanced Security Features',
                'Custom Integrations'
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
    <title>Modern Pricing Plans</title>
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
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="path_to_logo.png" alt="Logo">
        <h1>Choose the Best Plan for Your Business</h1>
        <p>Select a plan that suits your needs and get started today!</p>
    </div>

    <div class="row">
        <?php foreach ($pricingPlans as $planKey => $plan): ?>
            <div class="col-md-4 mb-4">
                <div class="pricing-plan text-center">
                    <h3><?= $plan['name'] ?></h3>
                    <p class="price">USD: $<?= $plan['amount_USD'] ?></p>
                    <p><strong>KES:</strong> <?= $plan['amount_KES'] ?></p>
                    <p><strong>NGN:</strong> <?= $plan['amount_NGN'] ?></p>
                    <p><?= $plan['details']['description'] ?></p>
                    <ul>
                        <?php foreach ($plan['details']['features'] as $feature): ?>
                            <li><?= $feature ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Payment Buttons -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#paymentModal" 
                            data-currency="USD" data-amount="<?= $plan['amount_USD'] ?>" 
                            data-payment="PayPal" data-info="Pay with PayPal">
                        Pay with PayPal (USD)
                    </button>
                    <button class="btn btn-warning" data-toggle="modal" data-target="#paymentModal" 
                            data-currency="USD" data-amount="<?= $plan['amount_USD'] ?>" 
                            data-payment="Binance Pay" data-info="Pay with Binance">
                        Binance Pay (USD)
                    </button>
                    <button class="btn btn-success" data-toggle="modal" data-target="#paymentModal" 
                            data-currency="KES" data-amount="<?= $plan['amount_KES'] ?>" 
                            data-payment="M-Pesa" data-info="Pay via M-Pesa">
                        M-Pesa (KES)
                    </button>
                    <button class="btn btn-info" data-toggle="modal" data-target="#paymentModal" 
                            data-currency="NGN" data-amount="<?= $plan['amount_NGN'] ?>" 
                            data-payment="Nigerian Bank" data-info="Bank Transfer (NGN)">
                        Bank Transfer (NGN)
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="payment-info"></p>
                <p><strong>Amount:</strong> <span id="payment-amount"></span> <span id="payment-currency"></span></p>
                <p><strong>Account Name:</strong> Business Name</p>
                <p><strong>Account Number / Phone:</strong> +123456789</p>
                <p><strong>Email:</strong> business@example.com</p>

                <!-- Payment Proof Upload for non-PayPal Options -->
                <div id="proof-upload-section" style="display: none;">
                    <label for="payment-proof">Upload Payment Proof:</label>
                    <input type="file" id="payment-proof" class="form-control" accept="image/*, application/pdf">
                    <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('#paymentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var amount = button.data('amount');
        var currency = button.data('currency');
        var payment = button.data('payment');
        var info = button.data('info');
        
        var modal = $(this);
        modal.find('#payment-info').text(info);
        modal.find('#payment-amount').text(amount);
        modal.find('#payment-currency').text(currency);
        
        if (payment !== "PayPal") {
            $('#proof-upload-section').show();
        } else {
            $('#proof-upload-section').hide();
        }
    });
</script>

</body>
</html>

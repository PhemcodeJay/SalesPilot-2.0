<?php
session_start();

// Sample product details (this should be fetched from your database)
$plans = [
    'premium' => [
        'monthly' => 1000,  // Monthly price for Premium
        'yearly' => 10000,  // Yearly price for Premium
    ],
    'enterprise' => [
        'monthly' => 2000,  // Monthly price for Enterprise
        'yearly' => 20000,  // Yearly price for Enterprise
    ],
];

// Default selection
$selected_plan = 'premium';
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
    // You need to integrate with the actual payment gateway API here
    switch ($payment_method) {
        case 'paypal':
            // PayPal payment processing logic
            break;
        case 'binance':
            // Binance Pay payment processing logic
            break;
        case 'bybit':
            // Bybit Pay payment processing logic
            break;
        case 'okx':
            // OKX Pay payment processing logic
            break;
        case 'mpesa':
            // M-Pesa payment processing logic
            break;
        case 'bank-transfer':
            // Nigerian Bank Transfer payment logic
            break;
        default:
            echo "Invalid payment method selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        main {
            padding: 20px;
        }
        h1 {
            text-align: center;
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
            background-color: #4CAF50;
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
            background-color: #45a049;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Your Logo</div>
    </header>

    <main>
        <h1>Select Your Plan and Payment Method</h1>
        <form method="POST" action="">
            <div>
                <label for="plan">Select Plan:</label>
                <select name="plan" id="plan" onchange="updatePrice()">
                    <option value="premium" <?php echo ($selected_plan == 'premium') ? 'selected' : ''; ?>>Premium</option>
                    <option value="enterprise" <?php echo ($selected_plan == 'enterprise') ? 'selected' : ''; ?>>Enterprise</option>
                </select>
            </div>

            <div>
                <label for="cycle">Billing Cycle:</label>
                <select name="cycle" id="cycle" onchange="updatePrice()">
                    <option value="monthly" <?php echo ($selected_cycle == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                    <option value="yearly" <?php echo ($selected_cycle == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                </select>
            </div>

            <div class="payment-options">
                <div class="payment-option">
                    <img src="paypal-logo.png" alt="PayPal" class="payment-logo">
                    <input type="radio" name="payment" value="paypal" required> PayPal
                </div>
                <div class="payment-option">
                    <img src="binance-pay-logo.png" alt="Binance Pay" class="payment-logo">
                    <input type="radio" name="payment" value="binance"> Binance Pay
                </div>
                <div class="payment-option">
                    <img src="bybit-pay-logo.png" alt="Bybit Pay" class="payment-logo">
                    <input type="radio" name="payment" value="bybit"> Bybit Pay
                </div>
                <div class="payment-option">
                    <img src="okx-pay-logo.png" alt="OKX Pay" class="payment-logo">
                    <input type="radio" name="payment" value="okx"> OKX Pay
                </div>
                <div class="payment-option">
                    <img src="mpesa-logo.png" alt="M-Pesa" class="payment-logo">
                    <input type="radio" name="payment" value="mpesa"> M-Pesa
                </div>
                <div class="payment-option">
                    <img src="bank-transfer-logo.png" alt="Nigerian Bank Transfer" class="payment-logo">
                    <input type="radio" name="payment" value="bank-transfer"> Nigerian Bank Transfer
                </div>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <p>Plan: <strong><?php echo ucfirst($selected_plan); ?></strong></p>
                <p>Billing Cycle: <strong><?php echo ucfirst($selected_cycle); ?></strong></p>
                <p>Total Amount: <strong>₦<?php echo number_format($total_price, 2); ?></strong></p>
            </div>

            <button type="submit" class="pay-button">Pay Now</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Your Company. All rights reserved.</p>
    </footer>

    <script>
        function updatePrice() {
            const plan = document.getElementById("plan").value;
            const cycle = document.getElementById("cycle").value;
            const totalPriceElement = document.querySelector(".order-summary strong");
            
            // Example prices for updating
            const prices = {
                premium: { monthly: 1000, yearly: 10000 },
                enterprise: { monthly: 2000, yearly: 20000 },
            };

            const total_price = prices[plan][cycle];
            totalPriceElement.textContent = "₦" + total_price.toFixed(2);
        }
    </script>
</body>
</html>

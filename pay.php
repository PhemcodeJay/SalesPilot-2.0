<?php
session_start();

// Sample product details (this should be fetched from your database)
$plans = [
    'starter' => [
        'monthly' => 9, // Monthly price for Starter Plan
    ],
    'growth' => [
        'monthly' => 29, // Monthly price for Growth Plan
    ],
    'enterprise' => [
        'monthly' => 49, // Monthly price for Enterprise Plan
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
    </style>
</head>
<body>
    <header>
        <div class="logo">Your Logo</div>
    </header>

    <main>
        <section id="pricing" class="pricing">
            <div class="container section-title" data-aos="fade-up">
                <h2>SalesPilot Pricing Plans</h2>
                <p>Choose the SalesPilot plan that aligns with your business size and ambitions, unlocking the power of comprehensive inventory and sales analytics.</p>
            </div>

            <div class="container" data-aos="zoom-in" data-aos-delay="100">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="pricing-item">
                            <h3>Starter Plan (Small)</h3>
                            <h4><sup>$</sup><?php echo $plans['starter']['monthly']; ?><span> / month</span></h4>
                            <ul>
                                <li><i class="bi bi-check"></i> <span>Ideal for startups and small businesses.</span></li>
                                <li><i class="bi bi-check"></i> <span>Affordable pricing tailored to your budget.</span></li>
                                <li><i class="bi bi-check"></i> <span>Essential features for efficient inventory and sales management.</span></li>
                            </ul>
                            <div class="text-center"><button class="buy-btn" onclick="selectPlan('starter')">Buy Now</button></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pricing-item featured">
                            <h3>Growth Plan (Medium)</h3>
                            <h4><sup>$</sup><?php echo $plans['growth']['monthly']; ?><span> / month</span></h4>
                            <ul>
                                <li><i class="bi bi-check"></i> <span>Designed for growing businesses.</span></li>
                                <li><i class="bi bi-check"></i> <span>Expanded features for enhanced scalability.</span></li>
                                <li><i class="bi bi-check"></i> <span>Competitive pricing to support your expanding needs.</span></li>
                            </ul>
                            <div class="text-center"><button class="buy-btn" onclick="selectPlan('growth')">Buy Now</button></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pricing-item">
                            <h3>Enterprise Plan (Big)</h3>
                            <h4><sup>$</sup><?php echo $plans['enterprise']['monthly']; ?><span> / month</span></h4>
                            <ul>
                                <li><i class="bi bi-check"></i> <span>Tailored for large enterprises.</span></li>
                                <li><i class="bi bi-check"></i> <span>Robust features, advanced analytics, and customization options.</span></li>
                                <li><i class="bi bi-check"></i> <span>Scalable solutions to meet the demands of your extensive operations.</span></li>
                            </ul>
                            <div class="text-center"><button class="buy-btn" onclick="selectPlan('enterprise')">Buy Now</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <h1 class="centered">Select Your Payment Method</h1>
        <form method="POST" action="">
            <div class="payment-options">
                <div class="payment-option" onclick="selectPaymentMethod('paypal')">
                    <img src="uploads/images/paypal-logo.png" alt="PayPal" class="payment-logo">
                    <input type="radio" name="payment" value="paypal" required style="display: none;"> PayPal
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('binance')">
                    <img src="uploads/images/binance-pay-logo.png" alt="Binance Pay" class="payment-logo">
                    <input type="radio" name="payment" value="binance" style="display: none;"> Binance Pay
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('bybit')">
                    <img src="uploads/images/bybit-pay-logo.png" alt="Bybit Pay" class="payment-logo">
                    <input type="radio" name="payment" value="bybit" style="display: none;"> Bybit Pay
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('okx')">
                    <img src="uploads/images/okx-pay-logo.png" alt="OKX Pay" class="payment-logo">
                    <input type="radio" name="payment" value="okx" style="display: none;"> OKX Pay
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('mpesa')">
                    <img src="uploads/images/mpesa-logo.png" alt="M-Pesa" class="payment-logo">
                    <input type="radio" name="payment" value="mpesa" style="display: none;"> Safaricom M-Pesa
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('bank-transfer')">
                    <img src="uploads/images/naira.png" alt="Bank Transfer" class="payment-logo">
                    <input type="radio" name="payment" value="bank-transfer" style="display: none;"> Nigerian Bank Transfer
                </div>
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
        </form>
    </main>

    <footer>
        <p>&copy; 2024 SalesPilot. All rights reserved.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to handle plan selection
        function selectPlan(plan) {
            document.getElementById('selected-plan').value = plan;
            document.getElementById('plan-summary').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
            updateTotalPrice();
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
            const selectedPaymentOption = document.querySelector(`input[value="${paymentMethod}"]`).parentElement;
            selectedPaymentOption.style.backgroundColor = '#87CEEB'; // Light blue for selected option
        }

        // Function to update the total price based on the selected plan
        function updateTotalPrice() {
            const plan = document.getElementById('selected-plan').value;
            const cycle = document.getElementById('selected-cycle').value;

            const prices = {
                'starter': { 'monthly': 0 },
                'growth': { 'monthly': 29 },
                'enterprise': { 'monthly': 49 },
            };

            const totalPrice = prices[plan][cycle];
            document.getElementById('total-price').textContent = '$' + totalPrice;
        }
    </script>
</body>
</html>

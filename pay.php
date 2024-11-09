

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

<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);
include('config.php'); // Includes database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process payment form submission
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $status = 'Pending'; // Default status

    
    try {
        $pdo = new PDO($dsn, $username, $password, $options);

        // Prepare SQL query
        $stmt = $pdo->prepare("INSERT INTO payments (amount, method, status) VALUES (?, ?, ?)");
        $stmt->execute([$amount, $method, $status]);

        $paymentId = $pdo->lastInsertId();

        // Process payment based on method
        switch ($method) {
            case 'PayPal':
                // Integrate with PayPal API
                echo "PayPal payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'Bitcoin':
                // Integrate with Bitcoin API
                echo "Bitcoin payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'USDT':
                // Integrate with USDT API
                $network = $_POST['usdtNetwork'];
                echo "USDT payment initiated on $network network. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'Matic':
                // Integrate with Matic (Polygon) API
                echo "Matic payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'TRON':
                // Integrate with TRON API
                echo "TRON payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'Binance Pay':
                // Integrate with Binance Pay API
                echo "Binance Pay payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'Bybit Pay':
                // Integrate with Bybit Pay API
                echo "Bybit Pay payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            case 'OKX Pay':
                // Integrate with OKX Pay API
                echo "OKX Pay payment initiated. Your payment ID is: " . htmlspecialchars($paymentId);
                break;
            default:
                echo "Invalid payment method.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
} else {
    // Display payment form
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
        label {
            display: block;
            margin: 0.5rem 0 0.2rem;
            color: #555;
        }
        input, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodSelect = document.getElementById('method');
            const paymentDetails = document.getElementById('paymentDetails');

            methodSelect.addEventListener('change', function() {
                let html = '';
                switch (methodSelect.value) {
                    case 'PayPal':
                        html = '<div class="form-group">' +
                               '<label for="paypalEmail">PayPal Email:</label>' +
                               '<input type="email" id="paypalEmail" name="paypalEmail">' +
                               '</div>';
                        break;
                    case 'Bitcoin':
                        html = '<div class="form-group">' +
                               '<label for="bitcoinAddress">Bitcoin Address:</label>' +
                               '<input type="text" id="bitcoinAddress" name="bitcoinAddress">' +
                               '</div>';
                        break;
                    case 'USDT':
                        html = '<div class="form-group">' +
                               '<label for="usdtNetwork">USDT Network:</label>' +
                               '<select id="usdtNetwork" name="usdtNetwork" required>' +
                               '<option value="ERC20">ERC20</option>' +
                               '<option value="TRC20">TRC20</option>' +
                               '<option value="BEP20">BEP20</option>' +
                               '</select>' +
                               '</div>' +
                               '<div class="form-group">' +
                               '<label for="usdtAddress">USDT Address:</label>' +
                               '<input type="text" id="usdtAddress" name="usdtAddress">' +
                               '</div>';
                        break;
                    case 'Matic':
                        html = '<div class="form-group">' +
                               '<label for="maticAddress">Matic Address:</label>' +
                               '<input type="text" id="maticAddress" name="maticAddress">' +
                               '</div>';
                        break;
                    case 'TRON':
                        html = '<div class="form-group">' +
                               '<label for="tronAddress">TRON Address:</label>' +
                               '<input type="text" id="tronAddress" name="tronAddress">' +
                               '</div>';
                        break;
                    case 'Binance Pay':
                        html = '<div class="form-group">' +
                               '<label for="binancePayEmail">Binance Pay Email:</label>' +
                               '<input type="email" id="binancePayEmail" name="binancePayEmail">' +
                               '</div>';
                        break;
                    case 'Bybit Pay':
                        html = '<div class="form-group">' +
                               '<label for="bybitPayEmail">Bybit Pay Email:</label>' +
                               '<input type="email" id="bybitPayEmail" name="bybitPayEmail">' +
                               '</div>';
                        break;
                    case 'OKX Pay':
                        html = '<div class="form-group">' +
                               '<label for="okxPayEmail">OKX Pay Email:</label>' +
                               '<input type="email" id="okxPayEmail" name="okxPayEmail">' +
                               '</div>';
                        break;
                }
                paymentDetails.innerHTML = html;
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Payment Page</h1>
        <form id="paymentForm" method="post" action="">
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="method">Payment Method:</label>
                <select id="method" name="method" required>
                    <option value="PayPal">PayPal</option>
                    <option value="Bitcoin">Bitcoin</option>
                    <option value="USDT">USDT</option>
                    <option value="Matic">Matic (Polygon)</option>
                    <option value="TRON">TRON</option>
                    <option value="Binance Pay">Binance Pay</option>
                    <option value="Bybit Pay">Bybit Pay</option>
                    <option value="OKX Pay">OKX Pay</option>
                </select>
            </div>
            
            <div id="paymentDetails"></div>

            <button type="submit">Pay Now</button>
        </form>
    </div>
</body>
</html>
<?php
}
?>

<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

require 'vendor/autoload.php'; // Include OpenAI PHP SDK
include('config.php'); // Includes database connection

use Orhanerday\OpenAi\OpenAi;

$open_ai_key = 'your_openai_api_key';
$openAi = new OpenAi($open_ai_key);

// Check if username is set in session
if (!isset($_SESSION["username"])) {
    exit("Error: No username found in session.");
}

$username = htmlspecialchars($_SESSION["username"]);

try {
    // Retrieve user information from the users table
    $user_query = "SELECT username, email, date FROM users WHERE username = :username";
    $stmt = $connection->prepare($user_query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_info) {
        exit("Error: User not found.");
    }

    // Retrieve user email and registration date
    $email = htmlspecialchars($user_info['email']);
    $date = htmlspecialchars($user_info['date']);

    // Fetch data from the reports table
    $reportsQuery = $connection->query("SELECT * FROM reports");
    $reports = $reportsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data from the revenue_by_product table
    $revenueByProductQuery = $connection->query("SELECT * FROM revenue_by_product");
    $revenueByProduct = $revenueByProductQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data from the sales_analytics table
    $salesAnalyticsQuery = $connection->query("SELECT * FROM sales_analytics");
    $salesAnalytics = $salesAnalyticsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for OpenAI analysis
    $salesData = [
        'weekly_sales' => $salesAnalytics,
        'top_products' => $revenueByProduct,
        'inventory_metrics' => $reports,
    ];

    $salesDataJson = json_encode($salesData);

    // Construct the prompt for OpenAI
    $prompt = "
Analyze the following sales data and provide insights:
$salesDataJson
- Identify trends in sales and inventory.
- Suggest actions to improve performance.
- Highlight any anomalies or concerns.
    ";

    // Request analysis from OpenAI
    $response = $openAi->completion([
        'model' => 'gpt-4',
        'prompt' => $prompt,
        'temperature' => 0.7,
        'max_tokens' => 1000,
        'top_p' => 1.0,
        'frequency_penalty' => 0.0,
        'presence_penalty' => 0.0,
    ]);

    $responseData = json_decode($response, true);
    $insights = $responseData['choices'][0]['text'] ?? 'No insights found.';

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Display user information and AI insights
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Email: <?php echo $email; ?></p>
    <p>Registration Date: <?php echo $date; ?></p>

    <h2>AI Analysis</h2>
    <div><?php echo nl2br(htmlspecialchars($insights)); ?></div>

    <h2>Reports</h2>
    <pre><?php echo htmlspecialchars(json_encode($reports, JSON_PRETTY_PRINT)); ?></pre>

    <h2>Revenue by Product</h2>
    <pre><?php echo htmlspecialchars(json_encode($revenueByProduct, JSON_PRETTY_PRINT)); ?></pre>

    <h2>Sales Analytics</h2>
    <pre><?php echo htmlspecialchars(json_encode($salesAnalytics, JSON_PRETTY_PRINT)); ?></pre>
</body>
</html>

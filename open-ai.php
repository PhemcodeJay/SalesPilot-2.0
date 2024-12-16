<?php
session_start([
    'cookie_lifetime' => 86400,  // 24 hours
    'cookie_secure'   => true,   // Ensures cookies are sent over secure channels
    'cookie_httponly' => true,   // Prevents JavaScript access to session cookies
    'use_strict_mode' => true,   // Ensures a new session ID is generated if the session is hijacked
    'sid_length'      => 48,     // Sets session ID length for enhanced security
]);

require 'vendor/autoload.php'; // Include OpenAI PHP SDK
include('config.php'); // Includes the database connection

use Orhanerday\OpenAi\OpenAi;

$open_ai_key = 'your_openai_api_key'; // Replace with your OpenAI API key
$openAi = new OpenAi($open_ai_key);

// Check if the username is set in the session
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

    // Convert the data to JSON for the OpenAI prompt
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
        'model' => 'gpt-4',           // Specify the model to use
        'prompt' => $prompt,          // Pass the constructed prompt
        'temperature' => 0.7,         // Set the randomness of the response
        'max_tokens' => 1000,         // Limit the length of the response
        'top_p' => 1.0,               // Use nucleus sampling
        'frequency_penalty' => 0.0,   // Avoid repeating tokens
        'presence_penalty' => 0.0,    // Avoid discussing irrelevant topics
    ]);

    $responseData = json_decode($response, true);
    $insights = $responseData['choices'][0]['text'] ?? 'No insights found.'; // Handle if no insights are found

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage()); // Handle database connection errors
} catch (Exception $e) {
    die("Error: " . $e->getMessage()); // Handle other exceptions
}

// Display user information and AI insights
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI Insights</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Registration Date: <?php echo htmlspecialchars($date); ?></p>

    <h2>AI Insights</h2>
    <div><?php echo nl2br(htmlspecialchars($insights)); ?></div> <!-- Display insights from OpenAI -->
</body>
</html>

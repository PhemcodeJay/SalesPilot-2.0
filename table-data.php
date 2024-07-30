<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

include('config.php'); // Includes database connection

// Check if username is set in session
if (!isset($_SESSION["username"])) {
    exit("Error: No username found in session.");
}

$username = htmlspecialchars($_SESSION["username"]);

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

// Calculate metrics
$total_sales_query = "
    SELECT SUM(sales_qty * sales_price) AS total_sales,
           SUM(sales_qty) AS total_quantity,
           SUM(sales_qty * (sales_price - cost_price)) AS total_profit,
           SUM(sales_qty * cost_price) AS total_expenses
    FROM sales
    INNER JOIN products ON sales.product_id = products.product_id";
$stmt = $connection->query($total_sales_query);
$sales_data = $stmt->fetch(PDO::FETCH_ASSOC);

$total_sales = $sales_data['total_sales'];
$total_quantity = $sales_data['total_quantity'];
$total_profit = $sales_data['total_profit'];
$total_expenses = $sales_data['total_expenses'];
$net_profit = $total_profit; // Assuming net profit is the same as total profit for simplicity

$most_sold_product_query = "
    SELECT product_id, SUM(sales_qty) AS total_sold
    FROM sales
    GROUP BY product_id
    ORDER BY total_sold DESC
    LIMIT 1";
$stmt = $connection->query($most_sold_product_query);
$most_sold_product = $stmt->fetch(PDO::FETCH_ASSOC);
$most_sold_product_id = $most_sold_product['product_id'];

$available_stock_query = "
    SELECT product_id, (inventory_qty - IFNULL(SUM(sales_qty), 0)) AS available_stock
    FROM products
    LEFT JOIN sales ON products.product_id = sales.product_id
    GROUP BY product_id";
$stmt = $connection->query($available_stock_query);
$available_stock_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$revenue = $total_sales; // Assuming revenue is the same as total sales
$profit_margin = ($total_profit / $revenue) * 100;

$revenue_by_product_query = "
    SELECT product_id, SUM(sales_qty * sales_price) AS revenue
    FROM sales
    GROUP BY product_id";
$stmt = $connection->query($revenue_by_product_query);
$revenue_by_product_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Year-over-year growth and cost of selling calculations are complex and require historical data, so these are simplified
$year_over_year_growth = 0.00; // Placeholder value
$cost_of_selling = $total_expenses; // Assuming cost of selling is the same as total expenses

// Insert calculated metrics into sales_analytics table
$insert_query = "
    INSERT INTO sales_analytics (total_sales, total_quantity, total_profit, total_expenses, net_profit, most_sold_product_id, available_stock, revenue, profit_margin, revenue_by_product, year_over_year_growth, cost_of_selling, date)
    VALUES (:total_sales, :total_quantity, :total_profit, :total_expenses, :net_profit, :most_sold_product_id, :available_stock, :revenue, :profit_margin, :revenue_by_product, :year_over_year_growth, :cost_of_selling, NOW())";
$stmt = $connection->prepare($insert_query);
$stmt->execute([
    ':total_sales' => $total_sales,
    ':total_quantity' => $total_quantity,
    ':total_profit' => $total_profit,
    ':total_expenses' => $total_expenses,
    ':net_profit' => $net_profit,
    ':most_sold_product_id' => $most_sold_product_id,
    ':available_stock' => json_encode($available_stock_data), // Assuming JSON encoding for multiple rows
    ':revenue' => $revenue,
    ':profit_margin' => $profit_margin,
    ':revenue_by_product' => json_encode($revenue_by_product_data), // Assuming JSON encoding for multiple rows
    ':year_over_year_growth' => $year_over_year_growth,
    ':cost_of_selling' => $cost_of_selling
]);

echo "Sales analytics data has been inserted successfully.";
?>

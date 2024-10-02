<?php
header('Content-Type: application/json');
require 'config.php'; // Include your database connection script

try {
    // Retrieve the time range from the request
    $range = $_GET['range'] ?? 'yearly';
    $startDate = '';
    $endDate = '';

    // Define the date range based on the selected period
    switch ($range) {
        case 'weekly':
            $startDate = date('Y-m-d', strtotime('this week Monday'));
            $endDate = date('Y-m-d', strtotime('this week Sunday'));
            break;
        case 'monthly':
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            break;
        case 'yearly':
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            break;
        default:
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            break;
    }

    // Fetch sales quantity data for Apex Basic Chart
    $salesQuery = $connection->prepare("
        SELECT DATE(sale_date) AS date, SUM(sales_qty) AS total_sales 
        FROM sales 
        WHERE DATE(sale_date) BETWEEN :startDate AND :endDate 
        GROUP BY DATE(sale_date)");
    $salesQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $salesData = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch sell-through rate and inventory turnover rate for Apex Line Area Chart
    $metricsQuery = $connection->prepare("
        SELECT DATE(report_date) AS date, 
               AVG(sell_through_rate) AS avg_sell_through_rate, 
               AVG(inventory_turnover_rate) AS avg_inventory_turnover_rate 
        FROM reports 
        WHERE DATE(report_date) BETWEEN :startDate AND :endDate 
        GROUP BY DATE(report_date)");
    $metricsQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $metricsData = $metricsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch revenue by product for Apex 3D Pie Chart
    $revenueByProductQuery = $connection->prepare("
        SELECT report_date, revenue_by_product 
        FROM reports 
        WHERE DATE(report_date) BETWEEN :startDate AND :endDate");
    $revenueByProductQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $revenueByProductData = $revenueByProductQuery->fetchAll(PDO::FETCH_ASSOC);

    // Decode the revenue_by_product JSON data and aggregate it
$revenueByProduct = [];
foreach ($revenueByProductData as $report) {
    // Decode JSON, ensuring we get an associative array
    $products = json_decode($report['revenue_by_product'], true);

    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error: ' . json_last_error_msg());
        continue;
    }

    // Ensure $products is an array before proceeding
    if (is_array($products)) {
        foreach ($products as $product => $revenue) {
            // Ensure the product name is a valid string and revenue is numeric
            if (is_string($product) && is_numeric($revenue)) {
                if (!isset($revenueByProduct[$product])) {
                    $revenueByProduct[$product] = 0;
                }
                // Aggregate revenue, ensuring it is cast to a float for numeric accuracy
                $revenueByProduct[$product] += (float)$revenue;
            }
        }
    }
}

// Sort the products by revenue and get the top 5 products
arsort($revenueByProduct);
$top5Products = array_slice($revenueByProduct, 0, 5, true);


    // Sort the products by revenue and get the top 5 products
    arsort($revenueByProduct);
    $top5Products = array_slice($revenueByProduct, 0, 5, true);

    // Fetch revenue, total cost, and additional expenses for Apex 3-Column Chart
    $revenueQuery = $connection->prepare("
        SELECT DATE(sale_date) AS date, SUM(sales_qty * price) AS revenue 
        FROM sales 
        JOIN products ON sales.product_id = products.id 
        WHERE DATE(sale_date) BETWEEN :startDate AND :endDate 
        GROUP BY DATE(sale_date)");
    $revenueQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $revenueData = $revenueQuery->fetchAll(PDO::FETCH_ASSOC);

    $totalCostQuery = $connection->prepare("
        SELECT DATE(sale_date) AS date, SUM(sales_qty * cost) AS total_cost 
        FROM sales 
        JOIN products ON sales.product_id = products.id 
        WHERE DATE(sale_date) BETWEEN :startDate AND :endDate 
        GROUP BY DATE(sale_date)");
    $totalCostQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $totalCostData = $totalCostQuery->fetchAll(PDO::FETCH_ASSOC);

    $expenseQuery = $connection->prepare("
        SELECT DATE(expense_date) AS date, SUM(amount) AS total_expenses 
        FROM expenses 
        WHERE DATE(expense_date) BETWEEN :startDate AND :endDate 
        GROUP BY DATE(expense_date)");
    $expenseQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
    $expenseData = $expenseQuery->fetchAll(PDO::FETCH_ASSOC);

    // Combine revenue, total cost, and additional expenses for Apex 3-Column Chart
    $combinedData = [];
    foreach ($revenueData as $data) {
        $date = $data['date'];
        $revenue = isset($data['revenue']) ? (float)$data['revenue'] : 0;

        // Find matching total cost data
        $totalCost = 0;
        foreach ($totalCostData as $cost) {
            if ($cost['date'] === $date) {
                $totalCost = isset($cost['total_cost']) ? (float)$cost['total_cost'] : 0;
                break;
            }
        }

        // Find matching expense data
        $expenses = 0;
        foreach ($expenseData as $expense) {
            if ($expense['date'] === $date) {
                $expenses = isset($expense['total_expenses']) ? (float)$expense['total_expenses'] : 0;
                break;
            }
        }

        // Calculate total expenses and profit
        $totalExpenses = $totalCost + $expenses;
        $profit = $revenue - $totalExpenses;

        // Add to combined data
        $combinedData[] = [
            'date' => $date,
            'revenue' => number_format($revenue, 2),
            'total_expenses' => number_format($totalExpenses, 2),
            'profit' => number_format($profit, 2)
        ];
    }

    // Prepare final data for each chart
    $response = [
        'apex-basic' => $salesData,
        'apex-line-area' => $metricsData,
        'am-3dpie-chart' => $top5Products,
        'apex-column' => $combinedData
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Handle any database-related exceptions
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to retrieve data.']);
    exit;
}
?>

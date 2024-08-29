<?php
header('Content-Type: application/json');
require 'config.php'; // Include your database connection script

// Retrieve the time range from the request
$range = $_GET['range'] ?? 'monthly';
$startDate = '';
$endDate = '';

// Define the date range based on the selected period
switch ($range) {
    case 'weekly':
        $startDate = date('Y-m-d', strtotime('last week Monday'));
        $endDate = date('Y-m-d', strtotime('last week Sunday'));
        break;
    case 'monthly':
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        break;
    case 'yearly':
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-31');
        break;
}

// Fetch sales data for Bar Chart
$salesQuery = $connection->prepare("SELECT DATE(sale_date) AS date, SUM(sales_qty) AS total_sales 
                                    FROM sales 
                                    WHERE sale_date BETWEEN :startDate AND :endDate 
                                    GROUP BY DATE(sale_date)");
$salesQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$salesData = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch revenue data for Candlestick and Area Charts
$revenueQuery = $connection->prepare("SELECT DATE(sale_date) AS date, 
                                       SUM(sales_qty * price) AS revenue 
                                       FROM sales 
                                       JOIN products ON sales.product_id = products.id 
                                       WHERE sale_date BETWEEN :startDate AND :endDate 
                                       GROUP BY DATE(sale_date)");
$revenueQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$revenueData = $revenueQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch total cost and expense data for Area Chart
$totalCostQuery = $connection->prepare("SELECT DATE(sale_date) AS date, 
                                        SUM(sales_qty * cost) AS total_cost 
                                        FROM sales 
                                        JOIN products ON sales.product_id = products.id 
                                        WHERE sale_date BETWEEN :startDate AND :endDate 
                                        GROUP BY DATE(sale_date)");
$totalCostQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$totalCostData = $totalCostQuery->fetchAll(PDO::FETCH_ASSOC);

$expenseQuery = $connection->prepare("SELECT DATE(expense_date) AS date, 
                                      SUM(amount) AS total_expenses 
                                      FROM expenses 
                                      WHERE expense_date BETWEEN :startDate AND :endDate 
                                      GROUP BY DATE(expense_date)");
$expenseQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$expenseData = $expenseQuery->fetchAll(PDO::FETCH_ASSOC);

// Combine revenue, total cost, and expenses for Area Chart
$combinedRevenueExpense = [];
foreach ($revenueData as $data) {
    $date = $data['date'];
    $revenue = $data['revenue'];
    
    // Find matching total cost data
    $totalCost = 0;
    foreach ($totalCostData as $cost) {
        if ($cost['date'] === $date) {
            $totalCost = $cost['total_cost'];
            break;
        }
    }
    
    // Find matching expense data
    $expenses = 0;
    foreach ($expenseData as $expense) {
        if ($expense['date'] === $date) {
            $expenses = $expense['total_expenses'];
            break;
        }
    }
    
    $totalExpenses = $totalCost + $expenses;
    $profit = $revenue - $totalExpenses;

    $combinedRevenueExpense[] = [
        'date' => $date,
        'total_revenue' => $revenue,
        'total_expenses' => $totalExpenses,
        'profit' => $profit
    ];
}

// Fetch sell-through rate and inventory turnover rate for Histogram Chart
$metricsQuery = $connection->prepare("SELECT DATE(report_date) AS date, 
                                      AVG(sell_through_rate) AS avg_sell_through_rate, 
                                      AVG(inventory_turnover_rate) AS avg_inventory_turnover_rate 
                                      FROM reports 
                                      WHERE report_date BETWEEN :startDate AND :endDate 
                                      GROUP BY DATE(report_date)");
$metricsQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$metricsData = $metricsQuery->fetchAll(PDO::FETCH_ASSOC);

// Prepare final data
$response = [
    'barData' => $salesData,
    'areaData' => $combinedRevenueExpense,
    'histogramData' => $metricsData,
    'candlestickData' => $revenueData
];

echo json_encode($response);
?>

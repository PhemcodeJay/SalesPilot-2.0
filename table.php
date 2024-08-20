<?php
header('Content-Type: text/html');
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

// Fetch sales data for Bar Chart and convert to table format
$salesQuery = $connection->prepare("SELECT DATE(sale_date) AS date, SUM(sales_qty) AS total_sales 
                                    FROM sales 
                                    WHERE sale_date BETWEEN :startDate AND :endDate 
                                    GROUP BY DATE(sale_date)");
$salesQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$salesData = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch revenue and profit data for Candlestick and Area Charts and convert to table format
$revenueProfitQuery = $connection->prepare("SELECT DATE(sale_date) AS date, 
                                            SUM(sales_qty * price) AS revenue, 
                                            SUM(sales_qty * (price - cost)) AS profit 
                                            FROM sales 
                                            JOIN products ON sales.product_id = products.id 
                                            WHERE sale_date BETWEEN :startDate AND :endDate 
                                            GROUP BY DATE(sale_date)");
$revenueProfitQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$revenueProfitData = $revenueProfitQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch expense and inventory data for Area Chart and convert to table format
$expenseQuery = $connection->prepare("SELECT DATE(expense_date) AS date, 
                                      SUM(amount) AS total_expenses 
                                      FROM expenses 
                                      WHERE expense_date BETWEEN :startDate AND :endDate 
                                      GROUP BY DATE(expense_date)");
$expenseQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$expenseData = $expenseQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch sell-through rate and inventory turnover rate from reports and convert to table format
$metricsQuery = $connection->prepare("SELECT DATE(report_date) AS date, 
                                      AVG(sell_through_rate) AS avg_sell_through_rate, 
                                      AVG(inventory_turnover_rate) AS avg_inventory_turnover_rate 
                                      FROM reports 
                                      WHERE report_date BETWEEN :startDate AND :endDate 
                                      GROUP BY DATE(report_date)");
$metricsQuery->execute(['startDate' => $startDate, 'endDate' => $endDate]);
$metricsData = $metricsQuery->fetchAll(PDO::FETCH_ASSOC);

// Function to generate HTML table
function generateTable($data, $columns) {
    echo '<table border="1">';
    echo '<tr>';
    foreach ($columns as $column) {
        echo "<th>{$column}</th>";
    }
    echo '</tr>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($columns as $column) {
            echo "<td>{$row[$column]}</td>";
        }
        echo '</tr>';
    }
    echo '</table>';
}

// Output tables
echo '<h2>Sales Data</h2>';
generateTable($salesData, ['date', 'total_sales']);

echo '<h2>Revenue and Profit Data</h2>';
generateTable($revenueProfitData, ['date', 'revenue', 'profit']);

echo '<h2>Expense Data</h2>';
generateTable($expenseData, ['date', 'total_expenses']);

echo '<h2>Metrics Data</h2>';
generateTable($metricsData, ['date', 'avg_sell_through_rate', 'avg_inventory_turnover_rate']);
?>

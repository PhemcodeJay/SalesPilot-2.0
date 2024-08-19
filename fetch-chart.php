<?php
header('Content-Type: application/json');

include('config.php'); // Includes database connection

try {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=salespilot', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the period parameter from the request
    $period = isset($_GET['period']) ? $_GET['period'] : 'month';

    // Prepare SQL query based on the selected period
    switch ($period) {
        case 'year':
            $date_format = "%Y";
            break;
        case 'month':
            $date_format = "%Y-%m";
            break;
        case 'week':
            $date_format = "%Y-%u"; // Week number of the year
            break;
        default:
            $date_format = "%Y-%m-%d"; // Default to daily
            break;
    }

    // SQL query to get total revenue by date
    $stmt = $pdo->prepare("
        SELECT
            DATE_FORMAT(report_date, :date_format) AS date,
            SUM(revenue) AS total_revenue
        FROM reports
        GROUP BY DATE_FORMAT(report_date, :date_format)
        ORDER BY DATE_FORMAT(report_date, :date_format) ASC
    ");

    // Bind the date format parameter
    $stmt->bindParam(':date_format', $date_format, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the data
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for chart
    $chartData = array_map(function ($row) {
        return [
            'date' => $row['date'],
            'total_revenue' => (float) $row['total_revenue']
        ];
    }, $data);

    // Output data as JSON
    echo json_encode($chartData);

} catch (PDOException $e) {
    // Handle errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>

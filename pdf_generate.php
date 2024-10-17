<?php
require 'vendor/autoload.php'; // Include Composer autoload (for TCPDF)
require 'config.php'; // Include your database connection script
require('path/to/fpdf.php'); // Adjust the path to your FPDF file

// Database connection
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'salespilot';

$dsn = "mysql:host=$hostname;dbname=$database;charset=utf8";
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

function generatePDF($title, $data, $filename) {
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Set font for the PDF
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    
    // Set font for data
    $pdf->SetFont('Arial', '', 12);
    
    foreach ($data as $label => $value) {
        $pdf->Cell(40, 10, $label . ':', 0, 0);
        $pdf->Cell(0, 10, $value, 0, 1);
    }

    // Output the PDF as a download
    $pdf->Output('D', $filename);
}

if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :customer_id");
    $stmt->execute(['customer_id' => $customer_id]);
    $customer = $stmt->fetch();

    if ($customer) {
        generatePDF('Customer Information', [
            'Name' => $customer['customer_name'],
            'Email' => $customer['customer_email'],
            'Phone' => $customer['customer_phone'],
            'Location' => $customer['customer_location']
        ], 'customer_' . $customer_id . '.pdf');
    } else {
        echo "Customer not found.";
    }
} elseif (isset($_GET['expense_id'])) {
    $expense_id = $_GET['expense_id'];
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = :expense_id");
    $stmt->execute(['expense_id' => $expense_id]);
    $expense = $stmt->fetch();

    if ($expense) {
        generatePDF('Expense Information', [
            'Description' => $expense['description'],
            'Amount' => '$' . number_format($expense['amount'], 2),
            'Date' => $expense['expense_date'],
            'Created by' => $expense['created_by']
        ], 'expense_' . $expense_id . '.pdf');
    } else {
        echo "Expense not found.";
    }
} elseif (isset($_GET['inventory_id'])) {
    $inventory_id = $_GET['inventory_id'];
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = :inventory_id");
    $stmt->execute(['inventory_id' => $inventory_id]);
    $inventory = $stmt->fetch();

    if ($inventory) {
        generatePDF('Inventory Information', [
            'Product Name' => $inventory['product_name'],
            'Product ID' => $inventory['product_id'],
            'Sales Quantity' => $inventory['sales_qty'],
            'Stock Quantity' => $inventory['stock_qty'],
            'Supply Quantity' => $inventory['supply_qty'],
            'Available Stock' => $inventory['available_stock'],
            'Inventory Quantity' => $inventory['inventory_qty'],
            'Last Updated' => $inventory['last_updated']
        ], 'inventory_' . $inventory_id . '.pdf');
    } else {
        echo "Inventory record not found.";
    }
} elseif (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = :invoice_id");
    $stmt->execute(['invoice_id' => $invoice_id]);
    $invoice = $stmt->fetch();

    if ($invoice) {
        generatePDF('Invoice Details', [
            'Invoice Number' => $invoice['invoice_number'],
            'Customer Name' => $invoice['customer_name'],
            'Order Date' => $invoice['order_date'],
            'Due Date' => $invoice['due_date'],
            'Subtotal' => '$' . number_format($invoice['subtotal'], 2),
            'Discount' => '$' . number_format($invoice['discount'], 2),
            'Total Amount' => '$' . number_format($invoice['total_amount'], 2)
        ], 'invoice_' . $invoice_id . '.pdf');
    } else {
        echo "Invoice not found.";
    }
} else {
    echo "No valid ID provided for generation.";
}

// Check if a specific product is requested
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch the specific product record from the database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Create instance of FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set font for the PDF
        $pdf->SetFont('Arial', 'B', 16);

        // Add a title
        $pdf->Cell(0, 10, 'Product Details', 0, 1, 'C');

        // Set font for product data
        $pdf->SetFont('Arial', '', 12);

        // Add product details
        $pdf->Cell(40, 10, 'Product Name:', 0, 0);
        $pdf->Cell(0, 10, $product['product_name'], 0, 1); // Adjusted key to 'product_name'

        $pdf->Cell(40, 10, 'Description:', 0, 0);
        $pdf->MultiCell(0, 10, $product['description']);

        $pdf->Cell(40, 10, 'Price:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($product['price'], 2), 0, 1);

        $pdf->Cell(40, 10, 'Cost:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($product['cost'], 2), 0, 1);

        $pdf->Cell(40, 10, 'Stock Quantity:', 0, 0);
        $pdf->Cell(0, 10, $product['stock_qty'], 0, 1);

        $pdf->Cell(40, 10, 'Supply Quantity:', 0, 0);
        $pdf->Cell(0, 10, $product['supply_qty'], 0, 1);

        $pdf->Cell(40, 10, 'Inventory Quantity:', 0, 0);
        $pdf->Cell(0, 10, $product['inventory_qty'], 0, 1);

        $pdf->Cell(40, 10, 'Profit:', 0, 0);
        $pdf->Cell(0, 10, '$' . number_format($product['profit'], 2), 0, 1);

        // Output the PDF as a download
        $pdf->Output('D', 'product_' . $product_id . '.pdf');
    } else {
        echo "Product record not found.";
    }
} else {
    // Generate a PDF for all products if no specific `product_id` is provided
    $stmt = $pdo->query("SELECT * FROM products");

    if ($stmt->rowCount() > 0) {
        // Create instance of FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set font for the PDF
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Product Report', 0, 1, 'C');

        // Set table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'ID', 1);
        $pdf->Cell(60, 10, 'Product Name', 1);
        $pdf->Cell(30, 10, 'Price', 1);
        $pdf->Cell(30, 10, 'Cost', 1);
        $pdf->Cell(30, 10, 'Stock Qty', 1);
        $pdf->Ln();

        // Set font for table content
        $pdf->SetFont('Arial', '', 12);

        // Loop through each product record and add to the PDF
        while ($product = $stmt->fetch()) {
            $pdf->Cell(30, 10, $product['id'], 1);
            $pdf->Cell(60, 10, $product['product_name'], 1); // Adjusted key to 'product_name'
            $pdf->Cell(30, 10, '$' . number_format($product['price'], 2), 1);
            $pdf->Cell(30, 10, '$' . number_format($product['cost'], 2), 1);
            $pdf->Cell(30, 10, $product['stock_qty'], 1);
            $pdf->Ln();
        }

        // Output the PDF as a download
        $pdf->Output('D', 'product_report.pdf');
    } else {
        echo "No products found.";
    }
}
?>



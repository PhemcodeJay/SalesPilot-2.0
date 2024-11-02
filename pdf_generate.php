<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

require 'config.php'; // Database connection
require 'vendor/autoload.php';
require 'fpdf/fpdf.php';

// Sanitize and validate input parameters
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags($input));
}

function validateId($id) {
    return filter_var($id, FILTER_VALIDATE_INT);
}

// PDF generation function
function generatePDF($title, $data, $filename) {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10); // Add a line break after the title

    // Main invoice data
    $pdf->SetFont('Arial', '', 12);
    foreach ($data as $label => $value) {
        if (is_array($value)) {
            // If the value is an array (for invoice items)
            $pdf->Cell(40, 10, "$label:", 0, 0);
            $pdf->Ln(); // Move to the next line
            // Format each item in the items array
            foreach ($value as $item) {
                if (is_array($item)) {
                    // Print item details
                    $pdf->Cell(40, 10, 'Item Name: ' . htmlspecialchars($item['item_name']), 0, 1);
                    $pdf->Cell(40, 10, 'Quantity: ' . intval($item['qty']), 0, 1);
                    $pdf->Cell(40, 10, 'Price: $' . number_format(floatval($item['price']), 2), 0, 1);
                    $pdf->Cell(40, 10, 'Total: $' . number_format(floatval($item['total']), 2), 0, 1);
                    $pdf->Ln(5); // Add space between items
                }
            }
        } else {
            // Print the invoice details
            $pdf->Cell(40, 10, "$label:", 0, 0);
            $pdf->Cell(0, 10, htmlspecialchars($value), 0, 1);
        }
    }

    // Output the PDF
    $pdf->Output('D', $filename);
}

// Fetch data from specified table
function fetchData($table, $idColumn, $id) {
    global $connection;
    if (!validateId($id)) return false;
    
    $stmt = $connection->prepare("SELECT * FROM $table WHERE $idColumn = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to generate an invoice PDF
function generateInvoicePDF($invoice, $invoiceItems, $invoiceId) {
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Invoice Details', 0, 1, 'C');
    $pdf->Ln(10);

    // Invoice details
$pdf->SetFont('Arial', '', 12);

// Invoice ID
$pdf->Cell(40, 10, 'Invoice ID:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['invoice_id']), 0, 1);

// Invoice Number
$pdf->Cell(40, 10, 'Invoice Number:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['invoice_number']), 0, 1);

// Customer Name
$pdf->Cell(40, 10, 'Customer Name:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['customer_name']), 0, 1);

// Invoice Description
$pdf->Cell(40, 10, 'Description:', 0, 0);
$pdf->MultiCell(0, 10, htmlspecialchars($invoice['invoice_description']), 0, 1);

// Order Date
$pdf->Cell(40, 10, 'Order Date:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['order_date']), 0, 1);

// Delivery Address
$pdf->Cell(40, 10, 'Delivery Address:', 0, 0);
$pdf->MultiCell(0, 10, htmlspecialchars($invoice['delivery_address']), 0, 1);

// Mode of Payment
$pdf->Cell(40, 10, 'Mode of Payment:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['mode_of_payment']), 0, 1);

// Due Date
$pdf->Cell(40, 10, 'Due Date:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($invoice['due_date']), 0, 1);

// Subtotal
$pdf->Cell(40, 10, 'Subtotal:', 0, 0);
$pdf->Cell(0, 10, '$' . number_format(floatval($invoice['subtotal']), 2), 0, 1);

// Total Amount
$pdf->Cell(40, 10, 'Total Amount:', 0, 0);
$pdf->Cell(0, 10, '$' . number_format(floatval($invoice['total_amount']), 2), 0, 1);

$pdf->Ln(10); // Add a line break before items


    // Invoice items header
    $pdf->Cell(40, 10, 'Item Name', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    // Loop through each item and add to PDF
    foreach ($invoiceItems as $item) {
        $pdf->Cell(40, 10, htmlspecialchars($item['item_name']), 1);
        $pdf->Cell(30, 10, intval($item['qty']), 1);
        $pdf->Cell(30, 10, '$' . number_format(floatval($item['price']), 2), 1);
        $pdf->Cell(30, 10, '$' . number_format(floatval($item['total']), 2), 1);
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', "invoice_$invoiceId.pdf");
}


// Handle PDF generation based on type
function handlePDFGeneration($type, $id) {
    $data = [];
    switch ($type) {
        case 'customer':
            $customer = fetchData('customers', 'customer_id', $id);
            if ($customer) {
                $data = [
                    'Name' => htmlspecialchars($customer['customer_name']),
                    'Email' => htmlspecialchars($customer['customer_email'] ?? 'N/A'),
                    'Phone' => htmlspecialchars($customer['customer_phone'] ?? 'N/A'),
                    'Location' => htmlspecialchars($customer['customer_location'] ?? 'N/A'),
                ];
                generatePDF('Customer Information', $data, "customer_$id.pdf");
            } else {
                displayError('Customer not found.');
            }
            break;

        case 'expense':
            $expense = fetchData('expenses', 'expense_id', $id);
            if ($expense) {
                $data = [
                    'Description' => htmlspecialchars($expense['description']),
                    'Amount' => '$' . number_format(floatval($expense['amount']), 2),
                    'Date' => htmlspecialchars($expense['expense_date']),
                    'Created by' => htmlspecialchars($expense['created_by']),
                ];
                generatePDF('Expense Information', $data, "expense_$id.pdf");
            } else {
                displayError('Expense not found.');
            }
            break;

        case 'invoice':
            // Check if invoice_id is provided
            if (validateId($id)) {
                // Fetch invoice details
                $invoiceQuery = "SELECT * FROM invoices WHERE invoice_id = :id";
                $stmt = $GLOBALS['connection']->prepare($invoiceQuery);
                $stmt->execute(['id' => $id]);
                $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

                // Fetch associated invoice items
                $itemsQuery = "SELECT * FROM invoice_items WHERE invoice_id = :id";
                $itemStmt = $GLOBALS['connection']->prepare($itemsQuery);
                $itemStmt->execute(['id' => $id]);
                $invoiceItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

                // Check if invoice exists
                if ($invoice) {
                    generateInvoicePDF($invoice, $invoiceItems, $id);
                } else {
                    displayError('Invoice not found.');
                }
            } else {
                displayError('Invalid invoice ID.');
            }
            break;

        case 'product':
            $product = fetchData('products', 'id', $id);
            if ($product) {
                $data = [
                    'Product Name' => htmlspecialchars($product['name']),
                    'Description' => htmlspecialchars($product['description']),
                    'Price' => '$' . number_format(floatval($product['price']), 2),
                    'Cost' => '$' . number_format(floatval($product['cost']), 2),
                    'Stock Quantity' => intval($product['stock_qty']),
                    'Supply Quantity' => intval($product['supply_qty']),
                    'Inventory Quantity' => intval($product['inventory_qty']),
                    'Profit' => '$' . number_format(floatval($product['profit']), 2),
                ];
                generatePDF('Product Details', $data, "product_$id.pdf");
            } else {
                displayError('Product not found.');
            }
            break;

        case 'staff':
            $staff = fetchData('staffs', 'staff_id', $id);
            if ($staff) {
                $data = [
                    'Staff Name' => htmlspecialchars($staff['staff_name']),
                    'Staff Email' => htmlspecialchars($staff['staff_email'] ?? 'N/A'),
                    'Position' => htmlspecialchars($staff['position'] ?? 'N/A'),
                    'Phone' => htmlspecialchars($staff['staff_phone'] ?? 'N/A'),
                ];
                generatePDF('Staff Details', $data, "staff_$id.pdf");
            } else {
                displayError('Staff record not found.');
            }
            break;

        case 'sales':
            $sales = fetchData('sales', 'sales_id', $id);
            if ($sales) {
                $data = [
                    'Sales ID' => intval($sales['sales_id']),
                    'Product' => htmlspecialchars($sales['name']),
                    'Payment Status' => htmlspecialchars($sales['payment_status']),
                    'Staff ID' => intval($sales['staff_id']),
                    'Sales Quantity' => intval($sales['sales_qty']),
                    'Sales Date' => htmlspecialchars($sales['sale_date']),
                    'Total Amount' => '$' . number_format(floatval($sales['total_price']), 2),
                ];
                generatePDF('Sales Information', $data, "sales_$id.pdf");
            } else {
                displayError('Sales record not found.');
            }
            break;

        default:
            http_response_code(400);
            echo "Invalid type for PDF generation.";
    }
}

// Error display function
function displayError($message) {
    http_response_code(404);
    echo $message;
    exit;
}

// Process GET request
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        // Sanitize input to prevent injection or XSS
        $sanitizedValue = sanitizeInput($value);

        // Check if the key ends with '_id' to identify relevant parameters
        if (strpos($key, '_id') !== false) {
            // Determine the type (e.g., 'invoice', 'report') by removing '_id' suffix
            $type = str_replace('_id', '', $key);
            
            try {
                // Call the PDF generation function based on the type and sanitized value
                handlePDFGeneration($type, $sanitizedValue);
                exit; // Exit after handling to prevent further output
            } catch (Exception $e) {
                // Log and handle exceptions gracefully
                error_log("PDF Generation Error: " . $e->getMessage());
                echo "An error occurred while generating the PDF. Please try again.";
                exit; // Ensure no further output
            }
        }
    }
} else {
    // Default action if no specific ID is provided, ensuring no prior output
    try {
        generateProductReport();
    } catch (Exception $e) {
        // Log and handle any exceptions in report generation
        error_log("Report Generation Error: " . $e->getMessage());
        echo "An error occurred while generating the product report.";
    }
}

// Function to generate product report PDF
function generateProductReport() {
    global $connection;

    $stmt = $connection->prepare("SELECT * FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products) {
        displayError('No products found for report generation.');
    }

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Product Report', 0, 1, 'C');
    $pdf->Ln(10); // Add line break

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'ID', 1);
    $pdf->Cell(80, 10, 'Name', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Stock', 1);
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('Arial', '', 12);
    foreach ($products as $product) {
        $pdf->Cell(30, 10, $product['id'], 1);
        $pdf->Cell(80, 10, htmlspecialchars($product['name']), 1);
        $pdf->Cell(30, 10, '$' . number_format(floatval($product['price']), 2), 1);
        $pdf->Cell(30, 10, intval($product['stock_qty']), 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'product_report.pdf'); // Download the report
}
?>

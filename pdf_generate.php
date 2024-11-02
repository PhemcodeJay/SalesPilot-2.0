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
    
    // Data
    $pdf->SetFont('Arial', '', 12);
    foreach ($data as $label => $value) {
        if (is_array($value)) {
            // If the value is an array, we need to format it differently
            $pdf->Cell(40, 10, "$label:", 0, 0);
            foreach ($value as $item) {
                if (is_array($item)) {
                    // If item is an array (for invoice items)
                    $pdf->Ln(); // Move to the next line for item
                    foreach ($item as $itemLabel => $itemValue) {
                        $pdf->Cell(40, 10, "$itemLabel: $itemValue", 0, 1);
                    }
                } else {
                    // If item is a string
                    $pdf->Ln(); // Move to the next line for item
                    $pdf->Cell(40, 10, $item, 0, 1);
                }
            }
        } else {
            $pdf->Cell(40, 10, "$label:", 0, 0);
            $pdf->Cell(0, 10, $value, 0, 1);
        }
    }
    
    $pdf->Output('D', $filename);
}

// Fetch data from specified table
function fetchData($table, $idColumn, $id) {
    global $connection;
    if (!validateId($id)) return false;
    
    $stmt = $connection->prepare("SELECT * FROM $table WHERE $idColumn = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// Handle PDF generation based on type
function handlePDFGeneration($type, $id) {
    $data = [];
    switch ($type) {
        case 'customer':
            $customer = fetchData('customers', 'customer_id', $id);
            if ($customer) {
                $data = [
                    'Name' => $customer['customer_name'],
                    'Email' => $customer['customer_email'] ?? 'N/A',
                    'Phone' => $customer['customer_phone'] ?? 'N/A',
                    'Location' => $customer['customer_location'] ?? 'N/A',
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
                    'Description' => $expense['description'],
                    'Amount' => '$' . number_format($expense['amount'], 2),
                    'Date' => $expense['expense_date'],
                    'Created by' => $expense['created_by'],
                ];
                generatePDF('Expense Information', $data, "expense_$id.pdf");
            } else {
                displayError('Expense not found.');
            }
            break;

        case 'inventory':
            $inventory = fetchData('inventory', 'inventory_id', $id);
            if ($inventory) {
                $data = [
                    'Product Name' => $inventory['product_name'],
                    'Product ID' => $inventory['product_id'],
                    'Sales Quantity' => $inventory['sales_qty'],
                    'Stock Quantity' => $inventory['stock_qty'],
                    'Supply Quantity' => $inventory['supply_qty'],
                    'Available Stock' => $inventory['available_stock'],
                    'Inventory Quantity' => $inventory['inventory_qty'],
                    'Last Updated' => $inventory['last_updated'],
                ];
                generatePDF('Inventory Information', $data, "inventory_$id.pdf");
            } else {
                displayError('Inventory record not found.');
            }
            break;

            case 'invoice':
                // Retrieve the invoice ID from the URL parameters
                $invoice_id = $_GET['invoice_id'] ?? null;
            
                if ($invoice_id) {
                    // Fetch the invoice details
                    $invoice = fetchData('invoices', 'invoice_id', $id);
            
                    // Check if the invoice was found
                    if ($invoice) {
                        // Fetch the items associated with the invoice
                        $invoiceItems = fetchData('invoice_items', 'invoice_id', $id);
            
                        // Validate the type of $invoiceItems to ensure it's an array
                        if (!is_array($invoiceItems)) {
                            error_log('Invoice items fetch returned unexpected type: ' . gettype($invoiceItems));
                            $invoiceItems = []; // Set to empty array if the data type is incorrect
                        }
            
                        // Prepare the main invoice data for the PDF
                        $data = [
                            'Invoice Number' => $invoice['invoice_number'],
                            'Customer Name' => $invoice['customer_name'],
                            'Order Date' => $invoice['order_date'],
                            'Due Date' => $invoice['due_date'],
                            'Subtotal' => '$' . number_format($invoice['subtotal'], 2),
                            'Discount' => '$' . number_format($invoice['discount'], 2),
                            'Total Amount' => '$' . number_format($invoice['total_amount'], 2),
                        ];
            
                        // Prepare the items data for the PDF, if available
                        if (!empty($invoiceItems)) {
                            $itemsData = [];
                            foreach ($invoiceItems as $item) {
                                if (is_array($item)) { // Ensure each item is an array
                                    $itemsData[] = [
                                        'Item Name' => $item['item_name'],
                                        'Quantity' => $item['quantity'],
                                        'Price' => '$' . number_format($item['price'], 2),
                                        'Total' => '$' . number_format($item['quantity'] * $item['price'], 2),
                                    ];
                                } else {
                                    error_log('Unexpected item type: ' . gettype($item));
                                }
                            }
                            $data['Items'] = $itemsData;
                        } else {
                            $data['Items'] = 'No items found for this invoice.';
                        }
            
                        // Generate the PDF using the gathered invoice details and items
                        generatePDF('Invoice Details', $data, "invoice_$id.pdf");
                    } else {
                        displayError('Invoice not found.');
                    }
                } else {
                    displayError('Invoice ID not provided.');
                }
                break;
            
            

        case 'product':
            $product = fetchData('products', 'id', $id);
            if ($product) {
                $data = [
                    'Product Name' => $product['name'],
                    'Description' => $product['description'],
                    'Price' => '$' . number_format($product['price'], 2),
                    'Cost' => '$' . number_format($product['cost'], 2),
                    'Stock Quantity' => $product['stock_qty'],
                    'Supply Quantity' => $product['supply_qty'],
                    'Inventory Quantity' => $product['inventory_qty'],
                    'Profit' => '$' . number_format($product['profit'], 2),
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
                    'Staff Name' => $staff['staff_name'],
                    'Staff Email' => $staff['staff_email'] ?? 'N/A',
                    'Position' => $staff['position'] ?? 'N/A',
                    'Phone' => $staff['staff_phone'] ?? 'N/A',
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
                    'Sales ID' => $sales['sales_id'],
                    'Product' => $sales['name'],
                    'Payment Status' => $sales['payment_status'],
                    'Staff ID' => $sales['staff_id'],
                    'Sales Quantity' => $sales['sales_qty'],
                    'Sales Date' => $sales['sale_date'],
                    'Total Amount' => '$' . number_format($sales['total_price'], 2),
                ];
                generatePDF('Sales Information', $data, "sales_$id.pdf");
            } else {
                displayError('Sales record not found.');
            }
            break;

        case 'supplier':
            $supplier = fetchData('suppliers', 'supplier_id', $id);
            if ($supplier) {
                $data = [
                    'Supplier Name' => $supplier['supplier_name'],
                    'Supplier Email' => $supplier['supplier_email'] ?? 'N/A',
                    'Phone' => $supplier['supplier_phone'] ?? 'N/A',
                    'Address' => $supplier['supplier_location'] ?? 'N/A',
                ];
                generatePDF('Supplier Information', $data, "supplier_$id.pdf");
            } else {
                displayError('Supplier record not found.');
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

// Generate product report PDF
function generateProductReport() {
    global $connection;

    $stmt = $connection->query("SELECT * FROM products");
    if ($stmt->rowCount() > 0) {
        $pdf = new FPDF();
        $pdf->AddPage();

        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Product Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $header = ['ID', 'Product Name', 'Price', 'Cost', 'Stock Qty'];
        $widths = [30, 60, 30, 30, 30];
        
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 10, $col, 1);
        }
        $pdf->Ln();

        // Table Content
        $pdf->SetFont('Arial', '', 12);
        while ($product = $stmt->fetch()) {
            $pdf->Cell(30, 10, $product['id'], 1);
            $pdf->Cell(60, 10, $product['name'], 1);
            $pdf->Cell(30, 10, '$' . number_format($product['price'], 2), 1);
            $pdf->Cell(30, 10, '$' . number_format($product['cost'], 2), 1);
            $pdf->Cell(30, 10, $product['stock_qty'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'product_report.pdf');
    } else {
        displayError('No product data found.');
    }
}
?>

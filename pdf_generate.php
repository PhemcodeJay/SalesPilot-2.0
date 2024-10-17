<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

include('config.php'); // Database connection
require 'vendor/autoload.php';
require('fpdf/fpdf.php');

function generatePDF($title, $data, $filename) {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set title font
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');

    // Set data font
    $pdf->SetFont('Arial', '', 12);
    foreach ($data as $label => $value) {
        $pdf->Cell(40, 10, $label . ':', 0, 0);
        $pdf->Cell(0, 10, $value, 0, 1);
    }

    // Output the PDF as a download
    $pdf->Output('D', $filename);
}

function fetchData($table, $idColumn, $id) {
    global $connection; // Access the PDO instance
    $stmt = $connection->prepare("SELECT * FROM $table WHERE $idColumn = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function handlePDFGeneration($type, $id) {
    $data = [];
    switch ($type) {
        case 'customer':
            $customer = fetchData('customers', 'customer_id', $id);
            if ($customer) {
                $data = [
                    'Name' => $customer['customer_name'],
                    'Email' => $customer['customer_email'],
                    'Phone' => $customer['customer_phone'],
                    'Location' => $customer['customer_location'],
                ];
                generatePDF('Customer Information', $data, 'customer_' . $id . '.pdf');
            } else {
                echo "Customer not found.";
            }
            break;

        case 'expense':
            $expense = fetchData('expenses', 'id', $id);
            if ($expense) {
                $data = [
                    'Description' => $expense['description'],
                    'Amount' => '$' . number_format($expense['amount'], 2),
                    'Date' => $expense['expense_date'],
                    'Created by' => $expense['created_by'],
                ];
                generatePDF('Expense Information', $data, 'expense_' . $id . '.pdf');
            } else {
                echo "Expense not found.";
            }
            break;

        case 'inventory':
            $inventory = fetchData('inventory', 'id', $id);
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
                generatePDF('Inventory Information', $data, 'inventory_' . $id . '.pdf');
            } else {
                echo "Inventory record not found.";
            }
            break;

        case 'invoice':
            $invoice = fetchData('invoices', 'id', $id);
            if ($invoice) {
                $data = [
                    'Invoice Number' => $invoice['invoice_number'],
                    'Customer Name' => $invoice['customer_name'],
                    'Order Date' => $invoice['order_date'],
                    'Due Date' => $invoice['due_date'],
                    'Subtotal' => '$' . number_format($invoice['subtotal'], 2),
                    'Discount' => '$' . number_format($invoice['discount'], 2),
                    'Total Amount' => '$' . number_format($invoice['total_amount'], 2),
                ];
                generatePDF('Invoice Details', $data, 'invoice_' . $id . '.pdf');
            } else {
                echo "Invoice not found.";
            }
            break;

        case 'product':
            $product = fetchData('products', 'id', $id);
            if ($product) {
                $data = [
                    'Product Name' => $product['product_name'],
                    'Description' => $product['description'],
                    'Price' => '$' . number_format($product['price'], 2),
                    'Cost' => '$' . number_format($product['cost'], 2),
                    'Stock Quantity' => $product['stock_qty'],
                    'Supply Quantity' => $product['supply_qty'],
                    'Inventory Quantity' => $product['inventory_qty'],
                    'Profit' => '$' . number_format($product['profit'], 2),
                ];
                generatePDF('Product Details', $data, 'product_' . $id . '.pdf');
            } else {
                echo "Product record not found.";
            }
            break;

        case 'supplier':
            $supplier = fetchData('suppliers', 'supplier_id', $id);
            if ($supplier) {
                $data = [
                    'Supplier Name' => $supplier['supplier_name'],
                    'Email' => $supplier['supplier_email'],
                    'Phone' => $supplier['supplier_phone'],
                    'Location' => $supplier['supplier_location'],
                ];
                generatePDF('Supplier Information', $data, 'supplier_' . $id . '.pdf');
            } else {
                echo "Supplier not found.";
            }
            break;

        case 'sales':
            $sales = fetchData('sales', 'id', $id);
            if ($sales) {
                $data = [
                    'Sales ID' => $sales['id'],
                    'Customer ID' => $sales['customer_id'],
                    'Staff ID' => $sales['staff_id'],
                    'Product ID' => $sales['product_id'],
                    'Sales Quantity' => $sales['sales_qty'],
                    'Sales Date' => $sales['sales_date'],
                    'Total Amount' => '$' . number_format($sales['total_amount'], 2),
                ];
                generatePDF('Sales Information', $data, 'sales_' . $id . '.pdf');
            } else {
                echo "Sales record not found.";
            }
            break;

        default:
            echo "Invalid type provided for PDF generation.";
    }
}

if (isset($_GET['customer_id'])) {
    handlePDFGeneration('customer', $_GET['customer_id']);
} elseif (isset($_GET['expense_id'])) {
    handlePDFGeneration('expense', $_GET['expense_id']);
} elseif (isset($_GET['inventory_id'])) {
    handlePDFGeneration('inventory', $_GET['inventory_id']);
} elseif (isset($_GET['invoice_id'])) {
    handlePDFGeneration('invoice', $_GET['invoice_id']);
} elseif (isset($_GET['product_id'])) {
    handlePDFGeneration('product', $_GET['product_id']);
} elseif (isset($_GET['supplier_id'])) {
    handlePDFGeneration('supplier', $_GET['supplier_id']);
} elseif (isset($_GET['sales_id'])) {
    handlePDFGeneration('sales', $_GET['sales_id']);
} else {
    echo "No valid ID provided for generation.";
}

// Function to generate product report PDF
function generateProductReport() {
    global $connection;

    $stmt = $connection->query("SELECT * FROM products");
    if ($stmt->rowCount() > 0) {
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Product Report', 0, 1, 'C');
        $pdf->Ln(10);

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
            $pdf->Cell(60, 10, $product['name'], 1);
            $pdf->Cell(30, 10, '$' . number_format($product['price'], 2), 1);
            $pdf->Cell(30, 10, '$' . number_format($product['cost'], 2), 1);
            $pdf->Cell(30, 10, $product['stock_qty'], 1);
            $pdf->Ln();
        }

        // Output the PDF as a download
        $pdf->Output('D', 'pdf_generate.pdf');
    } else {
        echo "No products found.";
    }
}

if (!isset($_GET['product_id'])) {
    generateProductReport();
}
?>

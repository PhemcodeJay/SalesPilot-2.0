<?php
require 'vendor/autoload.php'; // Include Composer autoload (for TCPDF)
require 'config.php'; // Include your database connection script

$invoice_id = $_GET['invoice_id'] ?? null;

if ($invoice_id === null) {
    die('Invoice ID is required.');
}

// Fetch invoice details and items
$query = "
    SELECT 
        i.invoice_number, i.customer_name, i.invoice_description, i.order_date, 
        i.order_status, i.order_id, i.billing_address, i.shipping_address, 
        i.bank, i.account_no, i.due_date, i.subtotal, i.discount, i.total_amount, 
        i.notes, 
        ii.item_name, ii.quantity, ii.price, ii.total
    FROM invoices i
    LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
    WHERE i.id = :invoice_id
";

$stmt = $connection->prepare($query);
$stmt->execute(['invoice_id' => $invoice_id]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$invoice_details = [];
$invoice_items = [];

foreach ($data as $row) {
    if (empty($invoice_details)) {
        $invoice_details = array(
            'invoice_number' => $row['invoice_number'],
            'customer_name' => $row['customer_name'],
            'invoice_description' => $row['invoice_description'],
            'order_date' => $row['order_date'],
            'order_status' => $row['order_status'],
            'order_id' => $row['order_id'],
            'billing_address' => $row['billing_address'],
            'shipping_address' => $row['shipping_address'],
            'bank' => $row['bank'],
            'account_no' => $row['account_no'],
            'due_date' => $row['due_date'],
            'subtotal' => $row['subtotal'],
            'discount' => $row['discount'],
            'total_amount' => $row['total_amount'],
            'notes' => $row['notes'],
        );
    }

    if ($row['item_name']) {
        $invoice_items[] = array(
            'item_name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'total' => $row['total']
        );
    }
}

$tcpdf = new \TCPDF();
$tcpdf->AddPage();

// Add content to PDF
$html = '
    <h1>Invoice Details</h1>
    <p>Invoice Number: ' . htmlspecialchars($invoice_details['invoice_number']) . '</p>
    <p>Customer Name: ' . htmlspecialchars($invoice_details['customer_name']) . '</p>
    <p>Description: ' . htmlspecialchars($invoice_details['invoice_description']) . '</p>
    <p>Order Date: ' . htmlspecialchars($invoice_details['order_date']) . '</p>
    <p>Order Status: ' . htmlspecialchars($invoice_details['order_status']) . '</p>
    <p>Order ID: ' . htmlspecialchars($invoice_details['order_id']) . '</p>
    <p>Billing Address: ' . nl2br(htmlspecialchars($invoice_details['billing_address'])) . '</p>
    <p>Shipping Address: ' . nl2br(htmlspecialchars($invoice_details['shipping_address'])) . '</p>
    <p>Bank: ' . htmlspecialchars($invoice_details['bank']) . '</p>
    <p>Account No: ' . htmlspecialchars($invoice_details['account_no']) . '</p>
    <p>Due Date: ' . htmlspecialchars($invoice_details['due_date']) . '</p>
    <p>Subtotal: $' . number_format($invoice_details['subtotal'], 2) . '</p>
    <p>Discount: ' . htmlspecialchars($invoice_details['discount']) . '%</p>
    <p>Total Amount: $' . number_format($invoice_details['total_amount'], 2) . '</p>
    <p>Notes: ' . htmlspecialchars($invoice_details['notes']) . '</p>
    <h2>Order Summary</h2>
    <table border="1" cellpadding="4">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

$index = 1;
foreach ($invoice_items as $item) {
    $html .= '
        <tr>
            <td>' . $index++ . '</td>
            <td>' . htmlspecialchars($item['item_name']) . '</td>
            <td>' . htmlspecialchars($item['quantity']) . '</td>
            <td>$' . number_format($item['price'], 2) . '</td>
            <td>$' . number_format($item['total'], 2) . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>';

$tcpdf->writeHTML($html);
$tcpdf->Output('invoice_' . $invoice_id . '.pdf', 'D'); // 'D' forces download
?>

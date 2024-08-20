<?php
require 'config.php'; // Include your database connection script

// Capture form data
$invoice_number = $_POST['invoice_number'];
$customer_name = $_POST['customer_name'];
$invoice_description = $_POST['invoice_description'];
$order_date = $_POST['order_date'];
$order_status = $_POST['order_status'];
$order_id = $_POST['order_id'];
$billing_address = $_POST['billing_address'];
$shipping_address = $_POST['shipping_address'];
$bank = $_POST['bank'];
$account_no = $_POST['account_no'];
$due_date = $_POST['due_date'];
$subtotal = $_POST['subtotal'];
$discount = $_POST['discount'];
$total_amount = $_POST['total_amount'];
$notes = $_POST['notes'];

// Begin a transaction
$pdo->beginTransaction();

try {
    // Prepare the insert statement
    $stmt = $pdo->prepare("
        INSERT INTO invoices (
            invoice_number, customer_name, invoice_description, order_date, 
            order_status, order_id, billing_address, shipping_address, 
            bank, account_no, due_date, subtotal, discount, total_amount, notes, 
            item_name, quantity, price, total
        ) VALUES (
            :invoice_number, :customer_name, :invoice_description, :order_date, 
            :order_status, :order_id, :billing_address, :shipping_address, 
            :bank, :account_no, :due_date, :subtotal, :discount, :total_amount, :notes,
            :item_name, :quantity, :price, :total
        )
    ");

    // Extract item data
    $item_names = $_POST['item_name'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];
    $totals = $_POST['total'];

    // Insert each invoice item
    foreach ($item_names as $index => $item_name) {
        $stmt->execute([
            ':invoice_number' => $invoice_number,
            ':customer_name' => $customer_name,
            ':invoice_description' => $invoice_description,
            ':order_date' => $order_date,
            ':order_status' => $order_status,
            ':order_id' => $order_id,
            ':billing_address' => $billing_address,
            ':shipping_address' => $shipping_address,
            ':bank' => $bank,
            ':account_no' => $account_no,
            ':due_date' => $due_date,
            ':subtotal' => $subtotal,
            ':discount' => $discount,
            ':total_amount' => $total_amount,
            ':notes' => $notes,
            ':item_name' => $item_name,
            ':quantity' => $quantities[$index],
            ':price' => $prices[$index],
            ':total' => $totals[$index]
        ]);
    }

    // Commit the transaction
    $pdo->commit();

    echo "Invoice has been successfully saved!";
} catch (Exception $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    echo "Failed to save invoice: " . $e->getMessage();
}
?>

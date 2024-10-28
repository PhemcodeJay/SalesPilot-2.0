<?php
// Include the database connection
require 'config.php'; // Ensure this file contains your PDO connection settings

// Initialize variables
$message = '';
$invoiceId = '';
$invoiceNumber = '';
$customerName = '';
$orderDate = '';
$dueDate = '';
$subtotal = '';
$discount = '';
$totalAmount = '';
$notes = '';
$invoiceItems = [];

// Check if an invoice ID is provided to fetch existing data
if (isset($_GET['invoice_id'])) {
    $invoiceId = $_GET['invoice_id'];

    // Fetch invoice details
    $invoiceQuery = "SELECT * FROM invoices WHERE invoice_id = ?";
    $stmt = $connection->prepare($invoiceQuery);
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($invoice) {
        // Populate the variables with fetched data
        $invoiceNumber = $invoice['invoice_number'];
        $customerName = $invoice['customer_name'];
        $orderDate = $invoice['order_date'];
        $dueDate = $invoice['due_date'];
        $subtotal = $invoice['subtotal'];
        $discount = $invoice['discount'];
        $totalAmount = $invoice['total_amount'];
        $notes = $invoice['notes'];

        // Fetch associated invoice items
        $itemsQuery = "SELECT * FROM invoice_items WHERE invoice_id = ?";
        $itemStmt = $connection->prepare($itemsQuery);
        $itemStmt->execute([$invoiceId]);
        $invoiceItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the invoice ID from the POST data
    $invoiceId = $_POST['invoice_id'];
    $invoiceNumber = $_POST['invoice_number'];
    $customerName = $_POST['customer_name'];
    $orderDate = $_POST['order_date'];
    $dueDate = $_POST['due_date'];
    $subtotal = $_POST['subtotal'];
    $discount = $_POST['discount'];
    $totalAmount = $_POST['total_amount'];
    $notes = $_POST['notes'];

    // Prepare the SQL statement to update the invoice details
    $updateInvoiceQuery = "UPDATE invoices 
                            SET invoice_number = ?, 
                                customer_name = ?, 
                                order_date = ?, 
                                due_date = ?, 
                                subtotal = ?, 
                                discount = ?, 
                                total_amount = ?, 
                                notes = ? 
                            WHERE invoice_id = ?";
    
    try {
        $stmt = $connection->prepare($updateInvoiceQuery);
        $stmt->execute([$invoiceNumber, $customerName, $orderDate, $dueDate, $subtotal, $discount, $totalAmount, $notes, $invoiceId]);

        // Update items or insert new ones
        if (isset($_POST['items'])) {
            $items = $_POST['items']; // This should be an array of items
            
            foreach ($items as $item) {
                $itemId = $item['id']; // Unique identifier for the item
                $itemName = $item['item_name'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total = $quantity * $price; // Calculate total for the item

                if ($itemId) {
                    // Update existing item
                    $updateItemQuery = "UPDATE invoice_items 
                                        SET item_name = ?, qty = ?, price = ?, total = ? 
                                        WHERE invoice_items_id = ?";
                    
                    $itemStmt = $connection->prepare($updateItemQuery);
                    $itemStmt->execute([$itemName, $quantity, $price, $total, $itemId]);
                } else {
                    // Insert new item
                    $insertItemQuery = "INSERT INTO invoice_items (invoice_id, item_name, qty, price, total) 
                                        VALUES (?, ?, ?, ?, ?)";
                    
                    $itemStmt = $connection->prepare($insertItemQuery);
                    $itemStmt->execute([$invoiceId, $itemName, $quantity, $price, $total]);
                }
            }
        }

        // Success message
        $message = "Invoice and items updated successfully.";
    } catch (PDOException $e) {
        // Handle any errors during the update
        $message = "Error updating invoice: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        header {
            background-color: #343a40;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin: 20px 0;
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
        }
        .item-row {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        @media (max-width: 600px) {
            form {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Update Invoice</h1>
</header>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" id="invoiceForm">
    <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoiceId); ?>">
    
    <label for="invoice_number">Invoice Number:</label>
    <input type="text" name="invoice_number" id="invoice_number" required value="<?php echo htmlspecialchars($invoiceNumber); ?>">

    <label for="customer_name">Customer Name:</label>
    <input type="text" name="customer_name" id="customer_name" required value="<?php echo htmlspecialchars($customerName); ?>">

    <label for="order_date">Order Date:</label>
    <input type="date" name="order_date" id="order_date" required value="<?php echo htmlspecialchars($orderDate); ?>">

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" id="due_date" required value="<?php echo htmlspecialchars($dueDate); ?>">

    <label for="subtotal">Subtotal:</label>
    <input type="number" name="subtotal" id="subtotal" step="0.01" required value="<?php echo htmlspecialchars($subtotal); ?>">

    <label for="discount">Discount:</label>
    <input type="number" name="discount" id="discount" step="0.01" value="<?php echo htmlspecialchars($discount); ?>">

    <label for="total_amount">Total Amount:</label>
    <input type="number" name="total_amount" id="total_amount" step="0.01" required value="<?php echo htmlspecialchars($totalAmount); ?>">

    <label for="notes">Notes:</label>
    <textarea name="notes" id="notes" rows="4"><?php echo htmlspecialchars($notes); ?></textarea>

    <h3>Invoice Items</h3>
    <div id="item-container">
        <?php foreach ($invoiceItems as $index => $item): ?>
            <div class="item-row">
                <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo $item['invoice_items_id']; ?>">
                <label for="item_name">Item Name:</label>
                <input type="text" name="items[<?php echo $index; ?>][item_name]" required value="<?php echo htmlspecialchars($item['item_name']); ?>">
                
                <label for="quantity">Quantity:</label>
                <input type="number" name="items[<?php echo $index; ?>][quantity]" required value="<?php echo htmlspecialchars($item['qty']); ?>">
                
                <label for="price">Price:</label>
                <input type="number" name="items[<?php echo $index; ?>][price]" required step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-item">Add Item</button>
    <br><br>
    <button type="submit">Update Invoice</button>
</form>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Your Company Name. All Rights Reserved.</p>
</footer>

<script>
    document.getElementById('add-item').addEventListener('click', function() {
        const itemContainer = document.getElementById('item-container');
        const itemCount = itemContainer.children.length;

        const newItemRow = document.createElement('div');
        newItemRow.classList.add('item-row');
        newItemRow.innerHTML = `
            <input type="hidden" name="items[${itemCount}][id]" value="">
            <label for="item_name">Item Name:</label>
            <input type="text" name="items[${itemCount}][item_name]" required placeholder="Item Name">
            
            <label for="quantity">Quantity:</label>
            <input type="number" name="items[${itemCount}][quantity]" required placeholder="Quantity">
            
            <label for="price">Price:</label>
            <input type="number" name="items[${itemCount}][price]" required step="0.01" placeholder="Price">
        `;
        itemContainer.appendChild(newItemRow);
    });
</script>

</body>
</html>

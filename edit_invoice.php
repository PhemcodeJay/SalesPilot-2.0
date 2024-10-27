<?php
// Include database connection
include 'config.php'; // Ensure this points to your actual database connection file

// Initialize variables
$invoice_id = null;
$invoices = [];

// Check if an invoice ID is provided for viewing/editing
if (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];

    try {
        // Fetch invoice details
        $invoice_query = "SELECT * FROM invoices WHERE invoice_id = :invoice_id";
        $stmt = $connection->prepare($invoice_query);
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching invoice: " . $e->getMessage();
    }
}

// Fetch all invoices for the list
try {
    $invoices_query = "SELECT * FROM invoices";
    $stmt = $connection->query($invoices_query);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching invoices: " . $e->getMessage();
}

// Handle edit and delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'edit' && isset($_POST['invoice_id'])) {
            // Logic to handle editing an invoice
            $updated_invoice_number = $_POST['invoice_number'];
            $updated_customer_name = $_POST['customer_name'];
            $updated_order_date = $_POST['order_date'];
            $invoice_id = $_POST['invoice_id']; // Ensure we have the invoice ID to update

            try {
                $update_query = "UPDATE invoices SET invoice_number = :invoice_number, customer_name = :customer_name, order_date = :order_date WHERE invoice_id = :invoice_id";
                $update_stmt = $connection->prepare($update_query);
                $update_stmt->bindParam(':invoice_number', $updated_invoice_number);
                $update_stmt->bindParam(':customer_name', $updated_customer_name);
                $update_stmt->bindParam(':order_date', $updated_order_date);
                $update_stmt->bindParam(':invoice_id', $invoice_id);
                $update_stmt->execute();

                // Redirect or show a success message
                header("Location: your_page.php"); // Change this to your actual page
                exit;
            } catch (PDOException $e) {
                echo "Error updating invoice: " . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['invoice_id'])) {
            // Logic to handle deleting an invoice
            try {
                $delete_query = "DELETE FROM invoices WHERE invoice_id = :invoice_id";
                $delete_stmt = $connection->prepare($delete_query);
                $delete_stmt->bindParam(':invoice_id', $_POST['invoice_id']);
                $delete_stmt->execute();

                // Redirect or show a success message
                header("Location: your_page.php"); // Change this to your actual page
                exit;
            } catch (PDOException $e) {
                echo "Error deleting invoice: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management System</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header */
        .card-header {
            background-color: #007bff; /* Bootstrap primary */
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }

        .card-title {
            margin: 0;
        }

        /* Invoice Table */
        .table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            text-align: left;
        }

        .table th {
            background-color: #f8f9fa; /* Light gray */
            font-weight: bold;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Light gray */
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue */
        }

        .action-btn {
            background-color: #6c757d; /* Bootstrap secondary */
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #5a6268; /* Darker gray */
        }

        /* Form Styles */
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }

            .btn, .action-btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="content-page">  
    <div class="container-fluid">
        <div class="container">
            <?php if (isset($invoice_id) && $invoice): ?>
                <!-- Invoice Details View -->
                <div class="container-fluid">
                    <div class="row">                  
                        <div class="col-lg-12">
                            <div class="card card-block card-stretch card-height print rounded">
                                <div class="card-header d-flex justify-content-between bg-primary header-invoice">
                                    <div class="iq-header-title">
                                        <h4 class="card-title mb-0">Invoice#<?php echo htmlspecialchars($invoice['invoice_number']); ?></h4>
                                    </div>
                                    <div class="invoice-btn">
                                        <button type="button" class="btn btn-primary-dark">
                                            <a href="pdf_generate.php?invoice_id=<?php echo urlencode($invoice_id); ?>" class="text-white">
                                                <i class="las la-file-download"></i> PDF
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice_id); ?>" />
                                <div class="row">
                                    <div class="col-sm-12">                                  
                                        <img src="http://localhost/project/assets/images/logo.png" class="logo-invoice img-fluid mb-3">
                                        <h5 class="mb-0">Hello, <input type="text" class="form-control d-inline-block w-auto" value="<?php echo htmlspecialchars($invoice['customer_name']); ?>" name="customer_name" /></h5>
                                        <p><textarea class="form-control" name="invoice_description"><?php echo htmlspecialchars($invoice['invoice_description']); ?></textarea></p>
                                        <input type="text" class="form-control" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" placeholder="Invoice Number" required />
                                        <input type="date" class="form-control" name="order_date" value="<?php echo htmlspecialchars($invoice['order_date']); ?>" required />
                                    </div>
                                </div>
                                <button type="submit" name="action" value="edit" class="btn btn-primary">Update Invoice</button>
                            </form>
                            <form method="POST" action="">
                                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice_id); ?>" />
                                <button type="submit" name="action" value="delete" class="action-btn">Delete Invoice</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Invoice List -->
                <h2>Invoice List</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['order_date']); ?></td>
                                <td>
                                    <a href="?invoice_id=<?php echo urlencode($invoice['invoice_id']); ?>" class="action-btn">Edit</a>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" />
                                        <button type="submit" name="action" value="delete" class="action-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Invoice Management System. All rights reserved.</p>
</div>
</body>
</html>

<?php


include('config.php'); // Database connection
require 'vendor/autoload.php';
require('fpdf/fpdf.php');




// Handle form actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? null;
        $customer_id = $_POST['customer_id'] ?? null;

        if (isset($_POST['edit'])) {
            // Handle edit action
            // Process edit (e.g., redirect to edit form or update customer details)
        } elseif (isset($_POST['delete'])) {
            // Handle delete action
            $delete_query = "DELETE FROM customers WHERE customer_id = :customer_id";
            $stmt = $connection->prepare($delete_query);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF']); // Reload page
            exit;
        } elseif (isset($_POST['save_pdf'])) {
            // Handle save as PDF action
            // Generate and save the PDF
            require('fpdf/fpdf.php'); // Include your PDF library

            if ($customer_id) {
                $query = "SELECT * FROM customers WHERE customer_id = :customer_id";
                $stmt = $connection->prepare($query);
                $stmt->bindParam(':customer_id', $customer_id);
                $stmt->execute();
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($customer) {
                    $pdf = new FPDF();
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 16);
                    $pdf->Cell(40, 10, 'Customer Details');
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->Cell(40, 10, 'Name: ' . $customer['customer_name']);
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Email: ' . $customer['customer_email']);
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Phone: ' . $customer['customer_phone']);
                    $pdf->Ln();
                    $pdf->Cell(40, 10, 'Location: ' . $customer['customer_location']);

                    // Output the PDF
                    $pdf->Output('D', 'customer_' . $customer_id . '.pdf');
                } else {
                    echo 'Customer not found.';
                }
            } else {
                echo 'No customer ID provided.';
            }
            exit;
        }
    }
?>

<script>
$(document).ready(function() {
    $('.editable').on('click', function() {
        var $this = $(this);
        var currentText = $this.text();
        var input = $('<input>', {
            type: 'text',
            value: currentText,
            class: 'form-control form-control-sm'
        });
        $this.html(input);
        input.focus();
        input.on('blur', function() {
            var newText = $(this).val();
            $this.html(newText);
        });
        input.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $(this).blur();
            }
        });
    });

    $('.save-btn').on('click', function() {
        var $row = $(this).closest('tr');
        var customerId = $(this).data('customer-id');
        var customerName = $row.find('[data-field="customer_name"]').text();
        var customerEmail = $row.find('[data-field="customer_email"]').text();
        var customerPhone = $row.find('[data-field="customer_phone"]').text();
        var customerLocation = $row.find('[data-field="customer_location"]').text();

        $.post('update_customer.php', {
            customer_id: customerId,
            customer_name: customerName,
            customer_email: customerEmail,
            customer_phone: customerPhone,
            customer_location: customerLocation,
            action: 'update'
        }, function(response) {
            alert('Customer updated successfully!');
        }).fail(function() {
            alert('Error updating customer.');
        });
    });

    $('.delete-btn').on('click', function() {
        if (confirm('Are you sure you want to delete this customer?')) {
            var customerId = $(this).data('customer-id');
            $.post('update_customer.php', {
                customer_id: customerId,
                action: 'delete'
            }, function(response) {
                alert('Customer deleted successfully!');
                location.reload(); // Refresh the page to reflect changes
            }).fail(function() {
                alert('Error deleting customer.');
            });
        }
    });

    $('.save-pdf-btn').on('click', function() {
        var customerId = $(this).data('customer-id');
        window.location.href = 'generate_pdf.php?customer_id=' + customerId;
    });
});
</script>
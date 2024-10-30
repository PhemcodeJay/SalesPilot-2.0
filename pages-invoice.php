<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

require 'config.php'; // Include your database connection script
require 'vendor/autoload.php';
require 'fpdf/fpdf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $invoice_id = $_POST['invoice_id'] ?? null;

    // Common invoice query for fetching invoice details
    $invoice_query = "SELECT * FROM invoices WHERE id = :invoice_id";
    $stmt = $connection->prepare($invoice_query);
    $stmt->bindParam(':invoice_id', $invoice_id);

    if ($action === 'view' && $invoice_id) {
        $stmt->execute();
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            echo json_encode([
                'success' => true,
                'invoice_number' => $invoice['invoice_number'],
                'customer_name' => $invoice['customer_name'],
                'order_date' => $invoice['order_date'],
                'total_amount' => $invoice['total_amount'],
                'items' => fetchInvoiceItems($invoice_id) // Fetch items for modal view
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invoice not found.']);
        }
    } elseif ($action === 'generate_pdf' && $invoice_id) {
        generateInvoicePDF($invoice_id);
        exit; // Exit after generating PDF
    } elseif ($action === 'delete' && $invoice_id) {
        $delete_query = "DELETE FROM invoices WHERE id = :invoice_id";
        $stmt = $connection->prepare($delete_query);
        $stmt->bindParam(':invoice_id', $invoice_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Invoice deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete invoice.']);
        }
    } elseif ($action === 'edit' && $invoice_id) {
        // Redirect to edit invoice page
        header("Location: edit-invoice.php?invoice_id=" . urlencode($invoice_id));
        exit;
    }
}

// Function to fetch invoice items
function fetchInvoiceItems($invoice_id) {
    global $connection; // Your PDO instance
    $items_query = "SELECT item_name, quantity, price, total FROM invoice_items WHERE invoice_id = :invoice_id";
    $stmt = $connection->prepare($items_query);
    $stmt->bindParam(':invoice_id', $invoice_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all items for the given invoice
}

// Function to generate PDF
function generateInvoicePDF($invoice_id) {
    global $connection;
    
    // Fetch invoice details
    $invoice_query = "SELECT * FROM invoices WHERE id = :invoice_id";
    $stmt = $connection->prepare($invoice_query);
    $stmt->bindParam(':invoice_id', $invoice_id);
    $stmt->execute();
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        echo json_encode(['success' => false, 'message' => 'Invoice not found.']);
        return;
    }

    // Fetch invoice items
    $items = fetchInvoiceItems($invoice_id);

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

    // Add invoice details
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Invoice Number: ' . $invoice['invoice_number'], 0, 1);
    $pdf->Cell(0, 10, 'Customer Name: ' . $invoice['customer_name'], 0, 1);
    $pdf->Cell(0, 10, 'Order Date: ' . $invoice['order_date'], 0, 1);
    $pdf->Cell(0, 10, 'Total Amount: ' . number_format($invoice['total_amount'], 2), 0, 1);
    $pdf->Ln(10); // Add a line break

    // Add items header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, 'Item Name', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    // Add items to PDF
    $pdf->SetFont('Arial', '', 12);
    foreach ($items as $item) {
        $pdf->Cell(80, 10, $item['item_name'], 1);
        $pdf->Cell(30, 10, $item['quantity'], 1);
        $pdf->Cell(30, 10, number_format($item['price'], 2), 1);
        $pdf->Cell(30, 10, number_format($item['total'], 2), 1);
        $pdf->Ln();
    }

    // Output PDF to browser
    $pdf->Output('I', 'invoice_' . $invoice['invoice_number'] . '.pdf');
}

// Fetch user information and invoices
try {
    if (!isset($_SESSION["username"])) {
        throw new Exception("No username found in session.");
    }

    $username = htmlspecialchars($_SESSION["username"]);

    // Retrieve user information
    $user_query = "SELECT username, email, date FROM users WHERE username = :username";
    $stmt = $connection->prepare($user_query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_info) {
        throw new Exception("User not found.");
    }

    $email = htmlspecialchars($user_info['email']);
    $date = htmlspecialchars($user_info['date']);

    // Retrieve invoices
    $invoices_query = "SELECT id AS invoice_id, invoice_number, customer_name, order_date FROM invoices";
    $stmt = $connection->prepare($invoices_query);
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}

// Fetch inventory notifications with product images
try {
    $inventoryQuery = $connection->prepare("SELECT i.product_name, i.available_stock, i.inventory_qty, i.sales_qty, p.image_path
        FROM inventory i
        JOIN products p ON i.product_id = p.id
        WHERE i.available_stock < :low_stock OR i.available_stock > :high_stock
        ORDER BY i.last_updated DESC");
    $inventoryQuery->execute([
        ':low_stock' => 10,
        ':high_stock' => 1000,
    ]);
    $inventoryNotifications = $inventoryQuery->fetchAll();

    // Fetch reports notifications
    $reportsQuery = $connection->prepare("SELECT JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_name')) AS product_name, 
               JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.total_sales')) AS total_sales 
        FROM reports");
    $reportsQuery->execute();
    $reportsNotifications = $reportsQuery->fetchAll();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}

// Additional code for rendering or processing notifications
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Invoice</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="http://localhost/project/assets/images/favicon.ico" />
      <link rel="stylesheet" href="http://localhost/project/assets/css/backend-plugin.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="http://localhost/project/assets/vendor/remixicon/fonts/remixicon.css">  </head>
  <body class="  ">
    <!-- loader Start -->
    <div id="loading">
          <div id="loading-center">
          </div>
    </div>
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">
      
      <div class="iq-sidebar  sidebar-default ">
          <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
              <a href="http://localhost/project/dashboard.php" class="header-logo">
                  <img src="http://localhost/project/assets/images/logo.png" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">SalesPilot</h5>
              </a>
              <div class="iq-menu-bt-sidebar ml-0">
                  <i class="las la-bars wrapper-menu"></i>
              </div>
          </div>
          <div class="data-scrollbar" data-scroll="1">
              <nav class="iq-sidebar-menu">
                  <ul id="iq-sidebar-toggle" class="iq-menu">
                      <li class="">
                          <a href="http://localhost/project/dashboard.php" class="svg-icon">                        
                              <svg  class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>
                              </svg>
                              <span class="ml-4">Dashboards</span>
                          </a>
                      </li>
                      <li class=" ">
                          <a href="#product" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash2" width="20" height="20"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                              </svg>
                              <span class="ml-4">Products</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="product" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                              <li class="">
                                  <a href="http://localhost/project/page-list-product.php">
                                      <i class="las la-minus"></i><span>List Product</span>
                                  </a>
                              </li>
                              <li class="">
                                  <a href="http://localhost/project/page-add-product.php">
                                      <i class="las la-minus"></i><span>Add Product</span>
                                  </a>
                              </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#category" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash3" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                              </svg>
                              <span class="ml-4">Categories</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="category" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="http://localhost/project/page-list-category.php">
                                              <i class="las la-minus"></i><span>List Category</span>
                                          </a>
                                  </li>
                             
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#sale" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash4" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                              </svg>
                              <span class="ml-4">Sale</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="sale" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="http://localhost/project/page-list-sale.php">
                                              <i class="las la-minus"></i><span>List Sale</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-add-sale.php">
                                              <i class="las la-minus"></i><span>Add Sale</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#purchase" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash5" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                  <line x1="1" y1="10" x2="23" y2="10"></line>
                              </svg>
                              <span class="ml-4">Expenses</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="purchase" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="http://localhost/project/page-list-expense.php">
                                              <i class="las la-minus"></i><span>List Expenses</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-add-expense.php">
                                              <i class="las la-minus"></i><span>Add Expenses</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#return" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash6" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="14" y1="10" x2="21" y2="3"></line><line x1="3" y1="21" x2="10" y2="14"></line>
                              </svg>
                              <span class="ml-4">Inventory</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="return" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="http://localhost/project/page-list-inventory.php">
                                              <i class="las la-minus"></i><span>List Inventory</span>
                                          </a>
                                  </li>
                              
                          </ul>
                      </li>
                      <li class=" ">
                          <a href="#people" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash8" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                              </svg>
                              <span class="ml-4">People</span>
                              <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                              </svg>
                          </a>
                          <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                  <li class="">
                                          <a href="http://localhost/project/page-list-customers.php">
                                              <i class="las la-minus"></i><span>Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-add-customers.php">
                                              <i class="las la-minus"></i><span>Add Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-list-staffs.php">
                                              <i class="las la-minus"></i><span>Staffs</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-add-staffs.php">
                                              <i class="las la-minus"></i><span>Add Staffs</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-list-suppliers.php">
                                              <i class="las la-minus"></i><span>Suppliers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="http://localhost/project/page-add-supplier.php">
                                              <i class="las la-minus"></i><span>Add Suppliers</span>
                                          </a>
                                  </li>
                          </ul>
                      </li>
                      <li class=" ">
                        <a href="#otherpage" class="collapsed" data-toggle="collapse" aria-expanded="false">
                              <svg class="svg-icon" id="p-dash9" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="9"></rect><rect x="14" y="7" width="3" height="5"></rect>
                            </svg>
                            <span class="ml-4">Analytics</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="otherpage" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="">
                                        <a href="http://localhost/project/analytics.php">
                                            <i class="las la-minus"></i><span>Charts</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="http://localhost/project/analytics-report.php">
                                            <i class="las la-minus"></i><span>Reports</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="http://localhost/project/sales-metrics.php">
                                            <i class="las la-minus"></i><span>Sales Metrics</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="http://localhost/project/inventory-metrics.php">
                                            <i class="las la-minus"></i><span>Inventory Metrics</span>
                                        </a>
                                </li>
                                
                        </ul>
                    </li>   
                      
                          </ul>
                      </li>
                  </ul>
              </nav>
              <div id="sidebar-bottom" class="position-relative sidebar-bottom">
                  <div class="card border-none">
                      <div class="card-body p-0">
                          
                      </div>
                  </div>
              </div>
              <div class="p-3"></div>
          </div>
          </div>      <div class="iq-top-navbar">
          <div class="iq-navbar-custom">
              <nav class="navbar navbar-expand-lg navbar-light p-0">
                  <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                      <i class="ri-menu-line wrapper-menu"></i>
                      <a href="http://localhost/project/dashboard.php" class="header-logo">
                          <img src="http://localhost/project/assets/images/logo.png" class="img-fluid rounded-normal" alt="logo">
                          <h5 class="logo-title ml-3">SalesPilot</h5>
      
                      </a>
                  </div>
                  <div class="iq-search-bar device-search">
                      <form action="#" class="searchbox">
                          <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                          <input type="text" class="text search-input" placeholder="Search here...">
                      </form>
                  </div>
                  <div class="d-flex align-items-center">
                      <button class="navbar-toggler" type="button" data-toggle="collapse"
                          data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                          aria-label="Toggle navigation">
                          <i class="ri-menu-3-line"></i>
                      </button>
                      <div class="collapse navbar-collapse" id="navbarSupportedContent">
                          <ul class="navbar-nav ml-auto navbar-list align-items-center">
                              <li class="nav-item nav-icon dropdown">
                                  
                              </li>
                              <li>
                                  <a href="#" class="btn border add-btn shadow-none mx-2 d-none d-md-block"
                                      data-toggle="modal" data-target="#new-order"><i class="las la-plus mr-2"></i>New
                                      Invoice</a>
                              </li>
                              <li class="nav-item nav-icon search-content">
                                  <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown"
                                      aria-haspopup="true" aria-expanded="false">
                                      <i class="ri-search-line"></i>
                                  </a>
                                  <div class="iq-search-bar iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch">
                                      <form action="#" class="searchbox p-2">
                                          <div class="form-group mb-0 position-relative">
                                              <input type="text" class="text search-input font-size-12"
                                                  placeholder="type here to search...">
                                              <a href="#" class="search-link"><i class="las la-search"></i></a>
                                          </div>
                                      </form>
                                  </div>
                              </li>
                              <li class="nav-item nav-icon dropdown">
    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-bell">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="bg-primary "></span>
    </a>
    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
        <div class="card shadow-none m-0">
            <div class="card-body p-0">
                <div class="cust-title p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Notifications</h5>
                        <a class="badge badge-primary badge-card" href="#">
                            <?= count($inventoryNotifications) + count($reportsNotifications) ?>
                        </a>
                    </div>
                </div>
                <div class="px-3 pt-0 pb-0 sub-card">

                    <?php if (!empty($inventoryNotifications)): ?>
                        <?php foreach ($inventoryNotifications as $notification): ?>
                            <a href="#" class="iq-sub-card">
                                <div class="media align-items-center cust-card py-3 border-bottom">
                                    <div>
                                        <img class="avatar-50 rounded-small"
                                            src="<?= htmlspecialchars($notification['image_path']); ?>" 
                                            alt="<?= htmlspecialchars($notification['product_name']); ?>">
                                    </div>
                                    <div class="media-body ml-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><?= htmlspecialchars($notification['product_name']); ?></h6>
                                            <small class="text-dark">
                                                <b>Available: <?= htmlspecialchars($notification['available_stock']); ?></b>
                                            </small>
                                        </div>
                                        <small>Inventory: <?= htmlspecialchars($notification['inventory_qty']); ?>, 
                                        Sales: <?= htmlspecialchars($notification['sales_qty']); ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No inventory notifications available.</p>
                    <?php endif; ?>

                    <?php if (!empty($reportsNotifications)): ?>
                        <?php foreach ($reportsNotifications as $notification): ?>
                            <a href="#" class="iq-sub-card">
                                <div class="media align-items-center cust-card py-3 border-bottom">
                                    <div>
                                        <img class="avatar-50 rounded-small"
                                            src="<?= htmlspecialchars($notification['image_path']); ?>" 
                                            alt="<?= htmlspecialchars($notification['product_name']); ?>">
                                    </div>
                                    <div class="media-body ml-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><?= htmlspecialchars($notification['product_name']); ?></h6>
                                            <small class="text-dark">
                                                <b>Revenue: <?= htmlspecialchars($notification['revenue']); ?></b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No reports notifications available.</p>
                    <?php endif; ?>
                </div>
                <a class="right-ic btn btn-primary btn-block position-relative p-2" href="#" role="button">
                    View All
                </a>
            </div>
        </div>
    </div>
</li>

                              <li class="nav-item nav-icon dropdown caption-content">
                                  <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4"
                                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <img src="http://localhost/project/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                  </a>
                                  <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <div class="card shadow-none m-0">
                                          <div class="card-body p-0 text-center">
                                              <div class="media-body profile-detail text-center">
                                                  <img src="http://localhost/project/assets/images/page-img/profile-bg.jpg" alt="profile-bg"
                                                      class="rounded-top img-fluid mb-4">
                                                      <img src="http://localhost/project/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">
                                              </div>
                                              <div class="p-3">
                                                <h5 class="mb-1"><?php echo $email; ?></h5>
                                                <p class="mb-0">Since <?php echo $date; ?></p>
                                                  <div class="d-flex align-items-center justify-content-center mt-3">
                                                      <a href="http://localhost/project/user-profile-edit.php" class="btn border mr-2">Profile</a>
                                                      <a href="logout.php" class="btn border">Sign Out</a>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
              </nav>
          </div>
      </div>
      <div class="modal fade" id="new-order" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <h4 class="mb-3">New Invoice</h4>
                    <div class="content create-workform bg-body">
                        <div class="pb-3">
                            <label class="mb-2">Name</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Enter Customer Name">
                        </div>
                        <div class="col-lg-12 mt-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-center">
                                <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                <div class="btn btn-outline-primary" id="createButton">Create</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-page">
    <div class="container-fluid">
        <div class="container">
            <?php if (isset($invoice_id)): ?>
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
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <img src="http://localhost/project/assets/images/logo.png" class="logo-invoice img-fluid mb-3" alt="Logo">
                                            <h5 class="mb-0">Hello, <?php echo htmlspecialchars($invoice['customer_name']); ?></h5>
                                            <p><?php echo htmlspecialchars($invoice['invoice_description']); ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive-sm">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Invoice Number</th>
                                                            <th>Customer Name</th>
                                                            <th>Order Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($invoices as $invoice): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($invoice['invoice_id']); ?></td>
                                                                <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                                                <td class="editable" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>"><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($invoice['order_date']); ?></td>
                                                                <td>
                                                                    <div class="d-flex align-items-center list-action">
                                                                        <button class="action-btn badge badge-info mr-2" data-action="view" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="View">
                                                                            <i class="ri-eye-line mr-0"></i>
                                                                        </button>
                                                                        <button class="action-btn badge bg-info mr-2" data-action="save-pdf" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Save as PDF">
                                                                            <i class="ri-download-line mr-0"></i>
                                                                        </button>
                                                                        <button class="action-btn badge bg-success mr-2" data-action="edit" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Edit">
                                                                            <i class="ri-pencil-line mr-0"></i>
                                                                        </button>
                                                                        <button class="action-btn badge bg-warning mr-2" data-action="delete" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this invoice?');">
                                                                            <i class="ri-delete-bin-line mr-0"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h5 class="mb-3">Order Summary</h5>
                                            <div class="table-responsive-sm">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" scope="col">#</th>
                                                            <th scope="col">Item</th>
                                                            <th class="text-center" scope="col">Quantity</th>
                                                            <th class="text-center" scope="col">Price</th>
                                                            <th class="text-center" scope="col">Totals</th>
                                                            <th class="text-center" scope="col">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $index = 1; 
                                                        $items = []; // Initialize items array

                                                        // Fetch invoice items if invoice_id is set
                                                        if (isset($invoice_id)) {
                                                            try {
                                                                $items_query = "SELECT item_name, quantity, price, total FROM invoice_items WHERE invoice_id = :invoice_id"; 
                                                                $stmt = $connection->prepare($items_query);
                                                                $stmt->bindParam(':invoice_id', $invoice_id);
                                                                $stmt->execute();

                                                                // Fetch all items related to the invoice
                                                                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                            } catch (PDOException $e) {
                                                                echo "Error fetching items: " . $e->getMessage();
                                                            }
                                                        }

                                                        foreach ($items as $item): ?>
                                                            <tr>
                                                                <th class="text-center" scope="row"><?php echo $index++; ?></th>
                                                                <td>
                                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['item_name']); ?></h6>
                                                                </td>
                                                                <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                                <td class="text-center">$<?php echo number_format($item['price'], 2); ?></td>
                                                                <td class="text-center"><b>$<?php echo number_format($item['total'], 2); ?></b></td>
                                                                <td class="text-center">
                                                                    <button class="action-btn badge badge-info mr-2" data-action="edit" data-item-name="<?php echo htmlspecialchars($item['item_name']); ?>" title="Edit">
                                                                        <i class="ri-pencil-line mr-0"></i>
                                                                    </button>
                                                                    <button class="action-btn badge bg-warning" data-action="delete" data-item-name="<?php echo htmlspecialchars($item['item_name']); ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                                                        <i class="ri-delete-bin-line mr-0"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>                              
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- List Invoices -->
                <h1>Invoice List</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invoice['invoice_id']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                <td class="editable" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>"><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['order_date']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <button class="action-btn badge badge-info mr-2" data-action="view" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="View">
                                            <i class="ri-eye-line mr-0"></i>
                                        </button>
                                        <button class="action-btn badge bg-info mr-2" data-action="save-pdf" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Save as PDF">
                                            <i class="ri-download-line mr-0"></i>
                                        </button>
                                        <button class="action-btn badge bg-success mr-2" data-action="edit" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Edit">
                                            <i class="ri-pencil-line mr-0"></i>
                                        </button>
                                        <button class="action-btn badge bg-warning mr-2" data-action="delete" data-invoice-id="<?php echo htmlspecialchars($invoice['invoice_id']); ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this invoice?');">
                                            <i class="ri-delete-bin-line mr-0"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

    <!-- Wrapper End-->
     <!-- Modal for displaying invoice details -->
<div id="invoiceModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="invoiceModalContent" class="modal-body">
                <!-- Invoice details will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Footer-->
    <footer class="iq-footer">
            <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="http://localhost/project/privacy-policy.php">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="http://localhost/project/terms-of-service.php">Terms of Use</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="http://localhost/project/dashboard.php" class="">SalesPilot</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <script src="http://localhost/project/assets/js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="http://localhost/project/assets/js/table-treeview.js"></script>
    
    <!-- app JavaScript -->
    <script src="http://localhost/project/assets/js/app.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
document.getElementById('createButton').addEventListener('click', function() {
    // Optional: Validate input or perform any additional checks here
    
    // Redirect to invoice-form.php
    window.location.href = 'invoice-form.php';
});
</script>
<script>
   document.addEventListener('DOMContentLoaded', () => {
    // Action buttons (view, save as PDF, edit, delete)
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            const invoiceId = this.getAttribute('data-invoice-id');

            if (action === 'view') {
                fetch('pages-invoice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'view',
                        invoice_id: invoiceId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('invoiceModalContent').innerHTML = `
                            <h3>Invoice Details</h3>
                            <p><strong>Invoice Number:</strong> ${data.invoice_number}</p>
                            <p><strong>Customer Name:</strong> ${data.customer_name}</p>
                            <p><strong>Order Date:</strong> ${data.order_date}</p>
                            <p><strong>Total Amount:</strong> ${data.total_amount}</p>
                        `;
                        $('#invoiceModal').modal('show');
                    } else {
                        alert('Invoice not found.');
                    }
                })
                .catch(error => console.error('Error:', error));

            } else if (action === 'save-pdf') {
                // Redirect to PDF generation
                window.location.href = `pdf_generate.php?invoice_id=${invoiceId}`;

            } else if (action === 'edit') {
                // Redirect to the editable invoice page
                window.location.href = `edit_invoice.php?invoice_id=${invoiceId}`;

            } else if (action === 'delete' && confirm('Are you sure you want to delete this invoice?')) {
                fetch('pages-invoice.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'delete', invoice_id: invoiceId })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // Reload the page to reflect changes
                })
                .catch(() => alert('Error deleting invoice.'));
            }
        });
    });

    // Editable cells for inline editing (if needed)
    document.querySelectorAll('.editable').forEach(cell => {
        cell.addEventListener('click', function () {
            const currentText = cell.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentText;
            input.className = 'form-control form-control-sm';
            cell.innerHTML = ''; // Clear current content
            cell.appendChild(input); // Append the input field
            input.focus(); // Focus on the input field

            input.addEventListener('blur', saveNewText);
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') saveNewText();
            });

            function saveNewText() {
                const newText = input.value;
                cell.textContent = newText; // Update cell text
                const invoiceId = cell.getAttribute('data-invoice-id'); // Get invoice ID

                fetch('pages-invoice.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'update',
                        invoice_id: invoiceId,
                        updated_text: newText
                    })
                })
                .then(response => response.json())
                .then(data => alert(data.message)) // Notify user of the result
                .catch(() => alert('Error updating invoice.'));
            }
        });
    });
});

</script>

  </body>
</html>
<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include('config.php');
require 'vendor/autoload.php';

// Binance payment function
function createBinancePayment($amount, $currency) {
    $apiKey = getenv('oerorywnqozkuillondw6i3agatww7ohql5tqkoiozhjra9fdzxui6xqvssbqgcl');
    $apiSecret = getenv('anadyqw1l3u4abjd3lu6xkpqf88pd5ik0hnxhrlnrnxgpn8rhjgbvqtk8yrrqaqi');

    $url = "https://api.binance.com/binancepay/api/v3/order";
    $data = [
        "amount" => $amount,
        "currency" => $currency,
        // Additional payment data can be added here
    ];

    // Generate API signature
    $signature = hash_hmac('sha256', json_encode($data), $apiSecret);

    // Headers for the API request
    $headers = [
        "Content-Type: application/json",
        "Binance-Api-Key: $apiKey",
        "Binance-Api-Signature: $signature"
    ];

    // Setting up HTTP context for the API call
    $options = [
        "http" => [
            "header" => $headers,
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Return the response from Binance API
    return json_decode($result, true);
}

// Handle POST request for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $response = createBinancePayment($amount, $currency);
    echo json_encode($response);
}

try {
    // Establish database connection
    $connection = new PDO($dsn, $username, $password, $options);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Process payment form submission
        $amount = $_POST['amount'] ?? 0;
        $method = $_POST['method'] ?? 'Cash'; // Default to 'Cash' if not provided
        $status = 'Pending'; // Default status for the payment

        // Prepare SQL query to insert payment record
        $stmt = $connection->prepare("INSERT INTO payments (amount, method, status) VALUES (?, ?, ?)");
        $stmt->execute([$amount, $method, $status]);

        $paymentId = $connection->lastInsertId(); // Get the last inserted payment ID
    }

    // Fetch inventory notifications with product images
    $inventoryQuery = $connection->prepare("
        SELECT i.product_name, i.available_stock, i.inventory_qty, i.sales_qty, p.image_path
        FROM inventory i
        JOIN products p ON i.product_id = p.id
        WHERE i.available_stock < :low_stock OR i.available_stock > :high_stock
        ORDER BY i.last_updated DESC
    ");
    $inventoryQuery->execute([
        ':low_stock' => 10,
        ':high_stock' => 1000,
    ]);
    $inventoryNotifications = $inventoryQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch reports notifications with product images
    $reportsQuery = $connection->prepare("
        SELECT JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_name')) AS product_name, 
               JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) AS revenue,
               p.image_path
        FROM reports r
        JOIN products p ON JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_id')) = p.id
        WHERE JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) > :high_revenue 
           OR JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) < :low_revenue
        ORDER BY r.report_date DESC
    ");
    $reportsQuery->execute([
        ':high_revenue' => 10000,
        ':low_revenue' => 1000,
    ]);
    $reportsNotifications = $reportsQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database connection errors
    echo "Database connection failed: " . $e->getMessage();
    exit;
}


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Subscriptions</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="https://salespilot.cybertrendhub.store/assets/images/favicon.ico" />
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend-plugin.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/remixicon/fonts/remixicon.css">  </head>
      <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
        label {
            display: block;
            margin: 0.5rem 0 0.2rem;
            color: #555;
        }
        input, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Modal styling */
  .modal-content {
      background-color: #fff;
      padding: 20px;
      border: 1px solid #888;
      width: 50%;
      margin: auto;
  }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
        }
    </style>
    
      <body class="  ">
    
    <!-- Wrapper Start -->
    <div class="wrapper">
      
      <div class="iq-sidebar  sidebar-default ">
          <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
              <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                  <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">SalesPilot</h5>
              </a>
              <div class="iq-menu-bt-sidebar ml-0">
                  <i class="las la-bars wrapper-menu"></i>
              </div>
          </div>
          <div class="data-scrollbar" data-scroll="1">
              <nav class="iq-sidebar-menu">
                  <ul id="iq-sidebar-toggle" class="iq-menu">
                      <li class="">
                          <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="svg-icon">                        
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
                                  <a href="https://salespilot.cybertrendhub.store/page-list-product.php">
                                      <i class="las la-minus"></i><span>List Product</span>
                                  </a>
                              </li>
                              <li class="">
                                  <a href="https://salespilot.cybertrendhub.store/page-add-product.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-category.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-sale.php">
                                              <i class="las la-minus"></i><span>List Sale</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-sale.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-expense.php">
                                              <i class="las la-minus"></i><span>List Expenses<pan>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-expense.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-inventory.php">
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
                                  <li class="active">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-customers.php">
                                              <i class="las la-minus"></i><span>Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-customers.php">
                                              <i class="las la-minus"></i><span>Add Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-staffs.php">
                                              <i class="las la-minus"></i><span>Staff</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-staffs.php">
                                              <i class="las la-minus"></i><span>Add Staffs</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-suppliers.php">
                                              <i class="las la-minus"></i><span>Suppliers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-supplier.php">
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
                                        <a href="https://salespilot.cybertrendhub.store/analytics.php">
                                            <i class="las la-minus"></i><span>Charts</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/analytics-report.php">
                                            <i class="las la-minus"></i><span>Reports</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/sales-metrics.php">
                                            <i class="las la-minus"></i><span>Sales Metrics</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/inventory-metrics.php">
                                            <i class="las la-minus"></i><span>Inventory Metrics</span>
                                        </a>
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
                      <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                          <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal" alt="logo">
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
                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                  </a>
                                  <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <div class="card shadow-none m-0">
                                          <div class="card-body p-0 text-center">
                                              <div class="media-body profile-detail text-center">
                                                  <img src="https://salespilot.cybertrendhub.store/assets/images/page-img/profile-bg.jpg" alt="profile-bg"
                                                      class="rounded-top img-fluid mb-4">
                                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                              </div>
                                              <div class="p-3">
                                                <h5 class="mb-1"><?php echo $email; ?></h5>
                                                <p class="mb-0">Since <?php echo $date; ?></p>
                                                  <div class="d-flex align-items-center justify-content-center mt-3">
                                                      <a href="https://salespilot.cybertrendhub.store/user-profile-edit.php" class="btn border mr-2">Profile</a>
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
<div class="container">
    <h1>Renew Your Subscription</h1>
    <form id="paymentForm" method="post" action="">
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="method">Payment Method:</label>
            <select id="method" name="method" required>
                <option value="BinancePay">Binance Pay</option>
                <option value="PayPal">PayPal</option>
            </select>
        </div>

        <!-- Plan Selection for Both Payment Methods -->
        <div class="form-group" id="planSelection">
            <label for="planSelect">Choose Your Plan:</label>
            <select id="planSelect" name="planSelect">
                <option value="P-93L4325701954531KM4XJXTI">Enterprise</option>
                <option value="P-6SV46253WD2252157M4XJS5I">Growth</option>
                <option value="P-24W85808161823945M4XJJMY">Starter</option>
            </select>
        </div>

        <!-- Payment Button Containers -->
        <div id="paypal-button-container" style="display: none;"></div>
        <button type="submit" id="binancePayButton">Pay with Binance Pay</button>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodSelect = document.getElementById('method');
        const planSelection = document.getElementById('planSelection');
        const paypalButtonContainer = document.getElementById('paypal-button-container');
        const binancePayButton = document.getElementById('binancePayButton');

        // Toggle payment options based on selected method
        methodSelect.addEventListener("change", function() {
            planSelection.style.display = "block";  // Always display plan selection for both methods
            
            if (methodSelect.value === "PayPal") {
                paypalButtonContainer.style.display = "block";
                binancePayButton.style.display = "none";
            } else if (methodSelect.value === "BinancePay") {
                paypalButtonContainer.style.display = "none";
                binancePayButton.style.display = "block";
            }
        });

        // Initial state
        paypalButtonContainer.style.display = "none";
        binancePayButton.style.display = "none";

        // Initialize PayPal subscription button
        paypal.Buttons({
            style: {
                shape: 'pill',
                color: 'silver',
                layout: 'horizontal',
                label: 'subscribe'
            },
            createSubscription: function(data, actions) {
                const selectedPlan = document.getElementById('planSelect').value;
                return actions.subscription.create({
                    plan_id: selectedPlan
                });
            },
            onApprove: function(data, actions) {
                alert("Subscription successful! Your subscription ID is: " + data.subscriptionID);
            }
        }).render('#paypal-button-container');
    });
</script>


<!-- Modal for payment -->
<div id="binanceModal" style="display: none;">
    <div class="modal-content">
        <span id="closeModal">&times;</span>
        <h2>Complete Your Payment</h2>
        <p>Processing payment...</p>
    </div>
</div>
<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=AZYvY1lNRIJ-1uKK0buXQvvblKWefjilgca9HAG6YHTYkfFvriP-OHcrUZsv2RCohiWCl59FyvFUST-W&vault=true&intent=subscription" data-sdk-integration-source="button-factory"></script>



    </div>
      </div>
    </div>
    <!-- Wrapper End-->
    <footer class="iq-footer">
            <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/privacy-policy.php">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/terms-of-service.php">Terms of Use</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="">SalesPilot</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/table-treeview.js"></script>
    
    <!-- app JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/app.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
document.getElementById('createButton').addEventListener('click', function() {
    // Optional: Validate input or perform any additional checks here
    
    // Redirect to invoice-form.php
    window.location.href = 'invoice-form.php';
});

document.getElementById('binancePayButton').addEventListener('click', () => {
    // Open the modal
    document.getElementById('binanceModal').style.display = 'block';
    
    // Call server to create a payment order
    fetch('/binance/create-payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount: 10, currency: 'USDT' }) // Example payload
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Open Binance Pay or display payment info in modal
            window.open(data.payment_url, '_blank');
        } else {
            alert('Error: ' + data.message);
        }
    });
});

// Close the modal
document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('binanceModal').style.display = 'none';
});

</script>
</body>
</html>

<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // Redirect to login page if session is not set
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user's information
$username = htmlspecialchars($_SESSION["username"]);

$user_query = "SELECT username, email, date, phone, location, user_image FROM users WHERE username = :username";
$stmt = $connection->prepare($user_query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_info) {
    throw new Exception("User not found.");
}

$email = htmlspecialchars($user_info['email']);
$date = date('d F, Y', strtotime($user_info['date']));
$location = htmlspecialchars($user_info['location']);
$existing_image = htmlspecialchars($user_info['user_image']);
$image_to_display = !empty($existing_image) ? $existing_image : 'uploads/user/default.png';


// Include database connection and autoload files
include('config.php'); // Includes database connection
require 'vendor/autoload.php';

// Binance payment function
function createBinancePayment($amount, $currency) {
    $apiKey = getenv('oerorywnqozkuillondw6i3agatww7ohql5tqkoiozhjra9fdzxui6xqvssbqgcl');
    $apiSecret = getenv('anadyqw1l3u4abjd3lu6xkpqf88pd5ik0hnxhrlnrnxgpn8rhjgbvqtk8yrrqaqi');

    $url = "https://api.binance.com/binancepay/api/v3/order";
    $data = [
        "amount" => $amount,
        "currency" => $currency,
        // Additional payment data can be added here
    ];

    // Generate API signature
    $signature = hash_hmac('sha256', json_encode($data), $apiSecret);

    // Headers for the API request
    $headers = [
        "Content-Type: application/json",
        "Binance-Api-Key: $apiKey",
        "Binance-Api-Signature: $signature"
    ];

    // Setting up HTTP context for the API call
    $options = [
        "http" => [
            "header" => $headers,
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Return the response from Binance API
    return json_decode($result, true);
}

// Handle POST request for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $response = createBinancePayment($amount, $currency);
    echo json_encode($response);
}

try {
    // Establish database connection
    $connection = new PDO($dsn, $username, $password, $options);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Process payment form submission
        $amount = $_POST['amount'] ?? 0;
        $method = $_POST['method'] ?? 'Cash'; // Default to 'Cash' if not provided
        $status = 'Pending'; // Default status for the payment

        // Prepare SQL query to insert payment record
        $stmt = $connection->prepare("INSERT INTO payments (amount, method, status) VALUES (?, ?, ?)");
        $stmt->execute([$amount, $method, $status]);

        $paymentId = $connection->lastInsertId(); // Get the last inserted payment ID
    }

    // Fetch inventory notifications with product images
    $inventoryQuery = $connection->prepare("
        SELECT i.product_name, i.available_stock, i.inventory_qty, i.sales_qty, p.image_path
        FROM inventory i
        JOIN products p ON i.product_id = p.id
        WHERE i.available_stock < :low_stock OR i.available_stock > :high_stock
        ORDER BY i.last_updated DESC
    ");
    $inventoryQuery->execute([
        ':low_stock' => 10,
        ':high_stock' => 1000,
    ]);
    $inventoryNotifications = $inventoryQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch reports notifications with product images
    $reportsQuery = $connection->prepare("
        SELECT JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_name')) AS product_name, 
               JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) AS revenue,
               p.image_path
        FROM reports r
        JOIN products p ON JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_id')) = p.id
        WHERE JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) > :high_revenue 
           OR JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) < :low_revenue
        ORDER BY r.report_date DESC
    ");
    $reportsQuery->execute([
        ':high_revenue' => 10000,
        ':low_revenue' => 1000,
    ]);
    $reportsNotifications = $reportsQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database connection errors
    echo "Database connection failed: " . $e->getMessage();
    exit;
}


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Subscriptions</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="https://salespilot.cybertrendhub.store/assets/images/favicon.ico" />
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend-plugin.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="https://salespilot.cybertrendhub.store/assets/vendor/remixicon/fonts/remixicon.css">  </head>
      <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
        label {
            display: block;
            margin: 0.5rem 0 0.2rem;
            color: #555;
        }
        input, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Modal styling */
  .modal-content {
      background-color: #fff;
      padding: 20px;
      border: 1px solid #888;
      width: 50%;
      margin: auto;
  }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
        }
    </style>
    
      <body class="  ">
    
    <!-- Wrapper Start -->
    <div class="wrapper">
      
      <div class="iq-sidebar  sidebar-default ">
          <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
              <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                  <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">SalesPilot</h5>
              </a>
              <div class="iq-menu-bt-sidebar ml-0">
                  <i class="las la-bars wrapper-menu"></i>
              </div>
          </div>
          <div class="data-scrollbar" data-scroll="1">
              <nav class="iq-sidebar-menu">
                  <ul id="iq-sidebar-toggle" class="iq-menu">
                      <li class="">
                          <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="svg-icon">                        
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
                                  <a href="https://salespilot.cybertrendhub.store/page-list-product.php">
                                      <i class="las la-minus"></i><span>List Product</span>
                                  </a>
                              </li>
                              <li class="">
                                  <a href="https://salespilot.cybertrendhub.store/page-add-product.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-category.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-sale.php">
                                              <i class="las la-minus"></i><span>List Sale</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-sale.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-expense.php">
                                              <i class="las la-minus"></i><span>List Expenses<pan>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-expense.php">
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
                                          <a href="https://salespilot.cybertrendhub.store/page-list-inventory.php">
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
                                  <li class="active">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-customers.php">
                                              <i class="las la-minus"></i><span>Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-customers.php">
                                              <i class="las la-minus"></i><span>Add Customers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-staffs.php">
                                              <i class="las la-minus"></i><span>Staff</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-staffs.php">
                                              <i class="las la-minus"></i><span>Add Staffs</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-list-suppliers.php">
                                              <i class="las la-minus"></i><span>Suppliers</span>
                                          </a>
                                  </li>
                                  <li class="">
                                          <a href="https://salespilot.cybertrendhub.store/page-add-supplier.php">
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
                                        <a href="https://salespilot.cybertrendhub.store/analytics.php">
                                            <i class="las la-minus"></i><span>Charts</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/analytics-report.php">
                                            <i class="las la-minus"></i><span>Reports</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/sales-metrics.php">
                                            <i class="las la-minus"></i><span>Sales Metrics</span>
                                        </a>
                                </li>
                                <li class="">
                                        <a href="https://salespilot.cybertrendhub.store/inventory-metrics.php">
                                            <i class="las la-minus"></i><span>Inventory Metrics</span>
                                        </a>
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
                      <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="header-logo">
                          <img src="https://salespilot.cybertrendhub.store/assets/images/logo.png" class="img-fluid rounded-normal" alt="logo">
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
                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                  </a>
                                  <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <div class="card shadow-none m-0">
                                          <div class="card-body p-0 text-center">
                                              <div class="media-body profile-detail text-center">
                                                  <img src="https://salespilot.cybertrendhub.store/assets/images/page-img/profile-bg.jpg" alt="profile-bg"
                                                      class="rounded-top img-fluid mb-4">
                                                      <img src="https://salespilot.cybertrendhub.store/<?php echo htmlspecialchars($image_to_display); ?>" 
         alt="profile-img" class="rounded profile-img img-fluid avatar-70">


                                              </div>
                                              <div class="p-3">
                                                <h5 class="mb-1"><?php echo $email; ?></h5>
                                                <p class="mb-0">Since <?php echo $date; ?></p>
                                                  <div class="d-flex align-items-center justify-content-center mt-3">
                                                      <a href="https://salespilot.cybertrendhub.store/user-profile-edit.php" class="btn border mr-2">Profile</a>
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
<div class="container">
    <h1>Renew Your Subscription</h1>
    <form id="paymentForm" method="post" action="">
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="method">Payment Method:</label>
            <select id="method" name="method" required>
                <option value="BinancePay">Binance Pay</option>
                <option value="PayPal">PayPal</option>
            </select>
        </div>

        <!-- Plan Selection for Both Payment Methods -->
        <div class="form-group" id="planSelection">
            <label for="planSelect">Choose Your Plan:</label>
            <select id="planSelect" name="planSelect">
                <option value="P-93L4325701954531KM4XJXTI">Enterprise</option>
                <option value="P-6SV46253WD2252157M4XJS5I">Growth</option>
                <option value="P-24W85808161823945M4XJJMY">Starter</option>
            </select>
        </div>

        <!-- Payment Button Containers -->
        <div id="paypal-button-container" style="display: none;"></div>
        <button type="submit" id="binancePayButton">Pay with Binance Pay</button>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodSelect = document.getElementById('method');
        const planSelection = document.getElementById('planSelection');
        const paypalButtonContainer = document.getElementById('paypal-button-container');
        const binancePayButton = document.getElementById('binancePayButton');

        // Toggle payment options based on selected method
        methodSelect.addEventListener("change", function() {
            planSelection.style.display = "block";  // Always display plan selection for both methods
            
            if (methodSelect.value === "PayPal") {
                paypalButtonContainer.style.display = "block";
                binancePayButton.style.display = "none";
            } else if (methodSelect.value === "BinancePay") {
                paypalButtonContainer.style.display = "none";
                binancePayButton.style.display = "block";
            }
        });

        // Initial state
        paypalButtonContainer.style.display = "none";
        binancePayButton.style.display = "none";

        // Initialize PayPal subscription button
        paypal.Buttons({
            style: {
                shape: 'pill',
                color: 'silver',
                layout: 'horizontal',
                label: 'subscribe'
            },
            createSubscription: function(data, actions) {
                const selectedPlan = document.getElementById('planSelect').value;
                return actions.subscription.create({
                    plan_id: selectedPlan
                });
            },
            onApprove: function(data, actions) {
                alert("Subscription successful! Your subscription ID is: " + data.subscriptionID);
            }
        }).render('#paypal-button-container');
    });
</script>


<!-- Modal for payment -->
<div id="binanceModal" style="display: none;">
    <div class="modal-content">
        <span id="closeModal">&times;</span>
        <h2>Complete Your Payment</h2>
        <p>Processing payment...</p>
    </div>
</div>
<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=AZYvY1lNRIJ-1uKK0buXQvvblKWefjilgca9HAG6YHTYkfFvriP-OHcrUZsv2RCohiWCl59FyvFUST-W&vault=true&intent=subscription" data-sdk-integration-source="button-factory"></script>



    </div>
      </div>
    </div>
    <!-- Wrapper End-->
    <footer class="iq-footer">
            <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/privacy-policy.php">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/terms-of-service.php">Terms of Use</a></li>
                              <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/subscription.php">Subscriptions</a></li>
                                <li class="list-inline-item"><a href="https://salespilot.cybertrendhub.store/pay.php">Pay Now</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="https://salespilot.cybertrendhub.store/dashboard.php" class="">SalesPilot</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/table-treeview.js"></script>
    
    <!-- app JavaScript -->
    <script src="https://salespilot.cybertrendhub.store/assets/js/app.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
document.getElementById('createButton').addEventListener('click', function() {
    // Optional: Validate input or perform any additional checks here
    
    // Redirect to invoice-form.php
    window.location.href = 'invoice-form.php';
});

document.getElementById('binancePayButton').addEventListener('click', () => {
    // Open the modal
    document.getElementById('binanceModal').style.display = 'block';
    
    // Call server to create a payment order
    fetch('/binance/create-payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount: 10, currency: 'USDT' }) // Example payload
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Open Binance Pay or display payment info in modal
            window.open(data.payment_url, '_blank');
        } else {
            alert('Error: ' + data.message);
        }
    });
});

// Close the modal
document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('binanceModal').style.display = 'none';
});

</script>
</body>
</html>

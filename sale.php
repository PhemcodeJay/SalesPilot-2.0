<?php
// Start the session with secure settings
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'sid_length'      => 48,
]);

include('config.php'); // Include the database connection

// Check if the username is set in the session
if (!isset($_SESSION["username"])) {
    die("No username found in session.");
}

$username = htmlspecialchars($_SESSION["username"]);

// Retrieve user information from the `users` table
$user_query = "SELECT id, username, email, date FROM users WHERE username = :username";
$stmt = $connection->prepare($user_query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_info) {
    die("User not found.");
}

$email = htmlspecialchars($user_info['email']);
$date = htmlspecialchars($user_info['date']);
$user_id = $user_info['id']; // Logged-in user ID for later use in sales entry

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $product_name = htmlspecialchars($_POST['name']);
    $total_price = (float) $_POST['total_price'];
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $staff_name = htmlspecialchars($_POST['staff_name']);
    $sales_qty = (int) $_POST['sales_qty'];
    $sale_status = htmlspecialchars($_POST['sale_status']);
    $payment_status = htmlspecialchars($_POST['payment_status']);
    $sale_note = htmlspecialchars($_POST['sale_note']);
    
    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["document"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $valid_extensions)) {
        echo "Only image files (JPG, JPEG, PNG, GIF) are allowed.";
        exit;
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        // Retrieve the product ID
        $stmt = $connection->prepare("SELECT id FROM products WHERE name = :name");
        $stmt->execute([':name' => $product_name]);
        $product = $stmt->fetch();

        if (!$product) {
            echo "Product not found. Please enter a valid product.";
            exit;
        }
        $product_id = $product['id'];

        // Get or insert customer
        $stmt = $connection->prepare("SELECT customer_id FROM customers WHERE customer_name = :customer_name");
        $stmt->execute([':customer_name' => $customer_name]);
        $customer = $stmt->fetch();

        if (!$customer) {
            $stmt = $connection->prepare("INSERT INTO customers (customer_name) VALUES (:customer_name)");
            $stmt->execute([':customer_name' => $customer_name]);
            $customer_id = $connection->lastInsertId(); // Get the new customer ID
        } else {
            $customer_id = $customer['customer_id'];
        }

        // Get or insert staff
        $stmt = $connection->prepare("SELECT staff_id FROM staffs WHERE staff_name = :staff_name");
        $stmt->execute([':staff_name' => $staff_name]);
        $staff = $stmt->fetch();

        if (!$staff) {
            $stmt = $connection->prepare("INSERT INTO staffs (staff_name) VALUES (:staff_name)");
            $stmt->execute([':staff_name' => $staff_name]);
            $staff_id = $connection->lastInsertId(); // Get the new staff ID
        } else {
            $staff_id = $staff['staff_id'];
        }

        // Insert the sales record
        $sql = "INSERT INTO sales (product_id, user_id, customer_id, staff_id, sales_qty, total_price, sale_status, payment_status, name, product_type, sale_note, image_path)
                VALUES (:product_id, :user_id, :customer_id, :staff_id, :sales_qty, :total_price, :sale_status, :payment_status, :name, :product_type, :sale_note, :image_path)";
        
        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':product_id'    => $product_id,
                ':user_id'       => $user_id, // From the session
                ':customer_id'   => $customer_id,
                ':staff_id'      => $staff_id,
                ':sales_qty'     => $sales_qty,
                ':total_price'   => $total_price,
                ':sale_status'   => $sale_status,
                ':payment_status'=> $payment_status,
                ':name'          => $product_name,
                ':product_type'  => 'Goods', // This could be dynamic depending on your form
                ':sale_note'     => $sale_note,
                ':image_path'    => $target_file
            ]);
            echo "Sale record added successfully.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error uploading file.";
    }
}

try {
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
        JOIN products p ON JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.product_name')) = p.name
        WHERE JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) < :low_revenue OR 
              JSON_UNQUOTE(JSON_EXTRACT(revenue_by_product, '$.revenue')) > :high_revenue
        ORDER BY r.report_date DESC
    ");
    $reportsQuery->execute([
        ':low_revenue' => 1000,
        ':high_revenue' => 5000,
    ]);
    $reportsNotifications = $reportsQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
    exit();
}
?>


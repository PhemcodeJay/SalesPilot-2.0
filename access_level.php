<?php
session_start();
require_once 'config.php'; // Include your DB connection file

// Assuming the user is already authenticated and their user ID is stored in the session
$user_id = $_SESSION['user_id'];

// Step 1: Fetch the user's subscription plan and trial end date from the `subscriptions` table
$query = "SELECT subscription_plan, start_date, end_date FROM subscriptions WHERE user_id = ? AND status = 'active' LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user has a valid subscription
if (!$user_subscription) {
    header("Location: dashboard.php"); // Redirect if no active subscription
    exit();
}

// Step 2: Check if the user is in their trial period (3 months)
$current_date = new DateTime();
$trial_end_date = new DateTime($user_subscription['end_date']);

if ($user_subscription['subscription_plan'] === 'trial' && $current_date <= $trial_end_date) {
    // If the user is still in their free trial period, allow access to all pages
    $user_subscription['subscription_plan'] = 'enterprise'; // Grant enterprise access during the trial period
}

// Step 3: Get the required access level for the current page from the `page_access` table
$page_name = basename($_SERVER['PHP_SELF']); // Get the current page name
$query = "SELECT required_access_level FROM page_access WHERE page = ?";
$stmt = $db->prepare($query);
$stmt->execute([$page_name]);
$required_access_level = $stmt->fetchColumn();

// Check if the page has a defined access level
if (!$required_access_level) {
    header("Location: dashboard.php"); // Redirect if page access level is not defined
    exit();
}

// Step 4: Define the hierarchy of subscription levels for comparison
$access_levels = [
    'trial' => 4,       // Trial users have the highest priority during the trial period
    'starter' => 1,
    'business' => 2,
    'enterprise' => 3,
];

// Step 5: Check if user's subscription level meets or exceeds the required access level
if ($access_levels[$user_subscription['subscription_plan']] < $access_levels[$required_access_level]) {
    header("Location: dashboard.php"); // Redirect if access is not allowed
    exit();
}

// If the user has the required access, allow them to proceed with the page content
// === Page Content Starts Here ===
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title</title>
</head>
<body>

<h1>Welcome to the Premium Content Page</h1>
<p>This content is only accessible to users with the correct subscription level.</p>

<!-- Additional content here -->
<?php
// Dynamically display additional content based on the subscription plan
switch ($user_subscription['subscription_plan']) {
    case 'starter':
        include 'starter_content.php';
        break;
    case 'business':
        include 'business_content.php';
        break;
    case 'enterprise':
        include 'enterprise_content.php';
        break;
    default:
        echo "<p>No content available for your subscription level.</p>";
        break;
}
?>
</body>
</html>

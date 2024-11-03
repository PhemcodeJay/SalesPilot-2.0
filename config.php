<?php
$hostname = "db5016593580.hosting-data.io";
$username = "dbu458270";
$password = "kokochulo@1987#";
$database = "dbs13455438";

$dsn = "mysql:host=$hostname;dbname=$database;charset=utf8";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $connection = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}
?>

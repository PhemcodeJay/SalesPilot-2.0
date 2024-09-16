-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2024 at 03:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salespilot`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation_codes`
--

CREATE TABLE `activation_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activation_code` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activation_codes`
--

INSERT INTO `activation_codes` (`id`, `user_id`, `activation_code`, `expires_at`, `created_at`) VALUES
(6, 6, '66d0df16a863c', '2024-08-30 19:50:30', '2024-08-29 20:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(56, 'Electronics', NULL, '2024-07-25 15:32:53'),
(59, 'Jewellry', NULL, '2024-07-25 17:19:18'),
(60, 'Apparel', NULL, '2024-07-26 05:49:26'),
(61, 'Auto', NULL, '2024-07-26 05:54:21'),
(63, 'Foods', NULL, '2024-08-29 22:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_location` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_location`, `created_at`) VALUES
(5, 'chris', 'chris@gmail.com', '011185678', 'Paris', '2024-07-26 06:39:01'),
(6, 'carol', 'carol@gmail.com', '0111826845', 'Abuja', '2024-07-26 07:01:27'),
(7, 'jim', 'jim@gmail.com', '0111826456', 'Texas', '2024-07-26 07:02:25'),
(41, 'mike', 'mike@gmail.com', '0101456789', 'Lagos', '2024-09-13 08:06:33');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `description`, `amount`, `expense_date`, `created_by`) VALUES
(1, 'Rent', 1000.00, '2024-07-26 21:00:00', 'john'),
(2, 'salary for june', 5000.00, '2024-07-26 21:00:00', 'steve'),
(3, 'shop repairs', 1500.00, '2024-07-26 21:00:00', 'kyle'),
(4, 'rent', 3000.00, '2024-09-12 21:00:00', 'james');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_qty` int(11) DEFAULT NULL,
  `supply_qty` int(11) DEFAULT NULL,
  `available_stock` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty` - `sales_qty`) STORED,
  `inventory_qty` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty`) STORED,
  `product_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `sales_qty`, `last_updated`, `stock_qty`, `supply_qty`, `product_name`) VALUES
(1, 11, 2, '2024-08-21 08:33:56', 10, 15, 'Samsung Galaxy'),
(2, 12, 5, '2024-08-21 08:33:56', 12, 15, 'Necklace'),
(3, 14, 8, '2024-08-21 08:33:56', 20, 12, 'Nike Sneakers'),
(4, 15, 10, '2024-08-21 08:33:58', 30, 15, 'Floral-Pleated-Weave-Dress'),
(5, 13, 3, '2024-08-21 08:34:00', 20, 15, 'Iphone 15 128GB'),
(6, 16, 1, '2024-08-21 08:34:02', 3, 2, 'Toyota-Corolla');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `invoice_description` text DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `account_no` varchar(50) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `customer_name`, `invoice_description`, `order_date`, `order_status`, `order_id`, `billing_address`, `shipping_address`, `bank`, `account_no`, `due_date`, `subtotal`, `discount`, `total_amount`, `notes`, `item_name`, `quantity`, `price`, `total`) VALUES
(14, '001', 'mega stores', 'sales order', '2016-01-17', 'paid', '250028', 'PO Box 16122 Collins Street West, Victoria Nairobi Kenya', 'PO Box 16122 Collins Street West, Victoria Nairobi Kenya', 'Threadneedle St', '12333456789', '2020-08-12', 1200.00, 0.00, 1300.75, 'It is a long established fact that a reader will be distracted by the readable content...', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_code` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `reset_code`, `expires_at`, `created_at`) VALUES
(4, 4, 'ff0f7f87a906e21b8d0c93368e8f0b1be17be0bdba8d1b084acb9017cb0ad421', '2024-08-20 07:48:40', '2024-08-20 07:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `paypal_email` varchar(255) DEFAULT NULL,
  `bitcoin_address` varchar(255) DEFAULT NULL,
  `usdt_address` varchar(255) DEFAULT NULL,
  `usdt_network` varchar(50) DEFAULT NULL,
  `matic_address` varchar(255) DEFAULT NULL,
  `tron_address` varchar(255) DEFAULT NULL,
  `binance_pay_email` varchar(255) DEFAULT NULL,
  `bybit_pay_email` varchar(255) DEFAULT NULL,
  `okx_pay_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `amount`, `method`, `status`, `paypal_email`, `bitcoin_address`, `usdt_address`, `usdt_network`, `matic_address`, `tron_address`, `binance_pay_email`, `bybit_pay_email`, `okx_pay_email`, `created_at`) VALUES
(1, 3000.00, 'OKX Pay', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-14 01:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_qty` int(11) NOT NULL,
  `supply_qty` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `product_type` enum('Goods','Services','Digital') NOT NULL DEFAULT 'Goods',
  `staff_name` varchar(45) NOT NULL,
  `category` varchar(255) NOT NULL,
  `inventory_qty` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty`) STORED,
  `profit` decimal(10,2) GENERATED ALWAYS AS (`price` - `cost`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `cost`, `category_id`, `created_at`, `stock_qty`, `supply_qty`, `image_path`, `product_type`, `staff_name`, `category`) VALUES
(11, 'Samsung Galaxy', 'Samsung Galaxy A10S BLACK', 6000.00, 3000.00, 56, '2024-07-25 17:13:11', 10, 15, 'uploads/samsung-galaxy-a10s.jpg', 'Goods', 'Jude', 'Electronics'),
(12, 'Necklace', 'Gold Necklace Fashion Jewellry', 450.00, 200.00, 59, '2024-07-25 17:19:18', 12, 15, 'uploads/goldnecklace.jpeg', 'Goods', 'kim', 'Jewellry'),
(13, 'Iphone 15 128GB', 'Apple Iphone 15 128GB White', 1500.00, 1200.00, 56, '2024-07-26 05:37:28', 20, 15, 'uploads/iphone-15-128gb.jpg', 'Goods', 'john', 'Electronics'),
(14, 'Nike Sneakers', 'Jordan Sneakers White &amp; Black Size 40', 150.00, 100.00, 60, '2024-07-26 05:49:27', 20, 12, 'uploads/nike-sneakers.jpg', 'Goods', 'james', 'Apparel'),
(15, 'Floral-Pleated-Weave-Dress', 'Floral-Pleated-Weave-Dress', 35.00, 20.00, 60, '2024-07-26 05:52:07', 30, 15, 'uploads/floral-pleated-weave-dress.jpg', 'Goods', 'joy', 'Apparel'),
(16, 'Toyota-Corolla', 'Toyota-Corolla-2024-White', 15000.00, 10000.00, 61, '2024-07-26 05:54:21', 3, 2, 'uploads/toyota-corolla-2024.jpg', 'Goods', 'chris', 'Auto'),
(19, 'Premium Beer', 'premium larger beer 75cl', 35.00, 20.00, 63, '2024-08-29 22:43:37', 400, 200, 'uploads/beer.jpg', 'Goods', 'kim', 'Foods'),
(20, 'Sony Digital Camera', 'Digital Camera', 80.00, 60.00, 56, '2024-08-29 22:57:02', 5, 10, 'uploads/camera.jpg', 'Goods', 'kim', 'Electronics'),
(21, 'Chevrolet', 'Chevrolet latest model 2024', 25000.00, 15000.00, 61, '2024-08-29 22:59:54', 3, 1, 'uploads/Chevrolet.jpg', 'Goods', 'james', 'Auto');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `profit_margin` decimal(5,2) NOT NULL,
  `revenue_by_product` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`revenue_by_product`)),
  `year_over_year_growth` decimal(5,2) NOT NULL,
  `cost_of_selling` decimal(10,2) NOT NULL,
  `inventory_turnover_rate` decimal(5,2) NOT NULL,
  `stock_to_sales_ratio` decimal(5,2) NOT NULL,
  `sell_through_rate` decimal(5,2) NOT NULL,
  `gross_margin_by_product` decimal(10,2) NOT NULL,
  `net_margin_by_product` decimal(10,2) NOT NULL,
  `gross_margin` decimal(10,2) NOT NULL,
  `net_margin` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_sales` decimal(10,0) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `total_profit` decimal(2,0) NOT NULL,
  `total_expenses` decimal(2,0) NOT NULL,
  `net_profit` decimal(2,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_date`, `revenue`, `profit_margin`, `revenue_by_product`, `year_over_year_growth`, `cost_of_selling`, `inventory_turnover_rate`, `stock_to_sales_ratio`, `sell_through_rate`, `gross_margin_by_product`, `net_margin_by_product`, `gross_margin`, `net_margin`, `created_at`, `total_sales`, `total_quantity`, `total_profit`, `total_expenses`, `net_profit`) VALUES
(17, '2024-08-30', 35300.00, 38.81, '[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"2\",\"total_sales\":\"12000.00\",\"total_cost\":\"6000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"2250.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"1250.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Iphone 15 128GB\",\"total_quantity\":\"3\",\"total_sales\":\"4500.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"900.00\",\"inventory_turnover_rate\":\"0.1500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"8\",\"total_sales\":\"1200.00\",\"total_cost\":\"800.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral-Pleated-Weave-Dress\",\"total_quantity\":\"10\",\"total_sales\":\"350.00\",\"total_cost\":\"200.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Toyota-Corolla\",\"total_quantity\":\"1\",\"total_sales\":\"15000.00\",\"total_cost\":\"10000.00\",\"total_profit\":\"5000.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 999.99, 0.08, 999.99, 0.00, 0.00, 13700.00, -7900.00, '2024-08-29 22:19:13', 35300, 29, 99, 99, -99);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `sale_status` enum('completed','pending') NOT NULL DEFAULT 'pending',
  `payment_status` enum('completed','due','paid') NOT NULL DEFAULT 'due',
  `name` varchar(255) NOT NULL,
  `product_type` enum('Goods','Services','Digital') NOT NULL,
  `sale_note` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `user_id`, `customer_id`, `staff_id`, `sales_qty`, `total_price`, `sale_date`, `sale_status`, `payment_status`, `name`, `product_type`, `sale_note`, `image_path`) VALUES
(17, 12, 6, 6, 48, 5, 450.00, '2024-07-26 07:01:27', 'completed', 'paid', 'Necklace', 'Goods', 'sold', 'uploads/goldnecklace.jpeg'),
(18, 14, 6, 7, 49, 8, 150.00, '2024-07-26 07:02:25', 'completed', 'paid', 'Nike Sneakers', 'Goods', 'sold', 'uploads/nike-sneakers.jpg'),
(19, 15, 6, 8, 50, 10, 35.00, '2024-07-26 07:03:52', 'completed', 'paid', 'Floral-Pleated-Weave-Dress', 'Goods', 'sold', 'uploads/floral-pleated-weave-dress.jpg'),
(20, 13, 6, 9, 51, 3, 1500.00, '2024-07-26 07:05:43', 'completed', 'paid', 'Iphone 15 128GB', 'Goods', 'sold', 'uploads/iphone-15-128gb.jpg'),
(21, 16, 6, 10, 52, 1, 15000.00, '2024-07-26 07:07:04', 'completed', 'paid', 'Toyota-Corolla', 'Goods', 'sold', 'uploads/toyota-corolla-2024.jpg');

--
-- Triggers `sales`
--
DELIMITER $$
CREATE TRIGGER `update_inventory_metrics` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    -- Update available stock in inventory_metrics
    UPDATE inventory_metrics im
    SET im.available_stock = (
        SELECT p.inventory_qty
        FROM products p
        WHERE p.id = NEW.id
    ) - NEW.sales_qty
    WHERE im.product_id = NEW.id;
    
    -- Update total_sales and total_quantity in sales_analytics
    UPDATE sales_analytics sa
    SET sa.total_sales = sa.total_sales + NEW.total_price,
        sa.total_quantity = sa.total_quantity + NEW.sales_qty,
        sa.total_profit = sa.total_profit + (NEW.total_price - (NEW.sales_qty * (
            SELECT p.cost
            FROM products p
            WHERE p.id = NEW.id
        ))),
        sa.net_profit = sa.total_profit - sa.total_expenses
    WHERE sa.product_id = NEW.id;
    
    -- Update most_sold_product_id in sales_analytics if necessary
    UPDATE sales_analytics sa
    SET sa.most_sold_product_id = NEW.id
    WHERE sa.product_id = NEW.id
    AND NEW.sales_qty > (
        SELECT MAX(s.sales_qty)
        FROM sales s
        WHERE DATE(s.sale_date) = DATE(NEW.sale_date)
        AND s.product_id = NEW.id
        GROUP BY s.product_id
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_analytics`
--

CREATE TABLE `sales_analytics` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `profit_margin` decimal(5,2) NOT NULL,
  `year_over_year_growth` decimal(5,2) NOT NULL,
  `cost_of_selling` decimal(10,2) NOT NULL,
  `inventory_turnover_rate` decimal(5,2) NOT NULL,
  `stock_to_sales_ratio` decimal(5,2) NOT NULL,
  `sell_through_rate` decimal(5,2) NOT NULL,
  `gross_margin_by_category` decimal(10,2) NOT NULL,
  `net_margin_by_category` decimal(10,2) NOT NULL,
  `gross_margin` decimal(10,2) NOT NULL,
  `net_margin` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_sales` decimal(10,0) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `total_profit` decimal(2,0) NOT NULL,
  `total_expenses` decimal(2,0) NOT NULL,
  `net_profit` decimal(2,0) NOT NULL,
  `revenue_by_category` decimal(2,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_analytics`
--

INSERT INTO `sales_analytics` (`id`, `date`, `revenue`, `profit_margin`, `year_over_year_growth`, `cost_of_selling`, `inventory_turnover_rate`, `stock_to_sales_ratio`, `sell_through_rate`, `gross_margin_by_category`, `net_margin_by_category`, `gross_margin`, `net_margin`, `created_at`, `total_sales`, `total_quantity`, `total_profit`, `total_expenses`, `net_profit`, `revenue_by_category`) VALUES
(170, '2024-08-29', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-29 22:26:04', 20315, 29, 99, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `staff_id` int(11) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_email` varchar(100) NOT NULL,
  `staff_phone` varchar(20) NOT NULL,
  `position` enum('manager','sales') NOT NULL DEFAULT 'sales',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `staff_name`, `staff_email`, `staff_phone`, `position`, `created_at`) VALUES
(43, 'james', 'james@gmail.com', '0111826234', '', '2024-07-26 03:39:01'),
(48, 'john', 'john@gmail.com', '0111826678', '', '2024-07-26 04:01:27'),
(49, 'carrey', 'carrey@gmail.com', '0112346872', '', '2024-07-26 04:02:25'),
(59, 'mark', 'mark@gmail.com', '0111628872', 'sales', '2024-09-13 08:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_email` varchar(255) NOT NULL,
  `supplier_phone` varchar(20) NOT NULL,
  `supplier_location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) NOT NULL,
  `supply_qty` int(11) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `supplier_email`, `supplier_phone`, `supplier_location`, `created_at`, `product_name`, `supply_qty`, `note`) VALUES
(1, 'Demarck Suppliers', 'demarck@gmail.com', '0101675432', 'Teaxas', '2024-07-29 17:34:11', 'Iphone 15 Pro Max', 10, 'good condition'),
(3, 'Gordons', 'gordons@gmail.com', '0101674356', 'Accra', '2024-09-13 08:09:35', 'Premium Beer', 200, 'good');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `role` enum('admin','sales','inventory') DEFAULT 'sales',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmpassword` varchar(255) NOT NULL,
  `user_image` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_active`, `role`, `date`, `confirmpassword`, `user_image`, `phone`, `location`) VALUES
(6, 'megastores', 'megastores@gmail.com', '$2y$10$51AXa2QQjFX/TLN0Z7Xo0eCpzCCAJLpN7w0UaHeNSmoh6MIZP8bl2', 1, 'admin', '2024-08-29 20:50:30', 'mega1234', 'uploads/user/1726223387_20230712_130449.jpg', '0111826872', 'Texas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_codes`
--
ALTER TABLE `activation_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`category_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `unique_invoice_item` (`invoice_number`,`item_name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_ibfk_1` (`category_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `sales_analytics`
--
ALTER TABLE `sales_analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_codes`
--
ALTER TABLE `activation_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sales_analytics`
--
ALTER TABLE `sales_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

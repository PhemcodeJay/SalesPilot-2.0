-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2024 at 11:09 PM
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
(2, 2, '66a214ed6b9ab', '2024-07-26 08:03:41', '2024-07-25 09:03:41'),
(4, 4, '66c08ed220a2b', '2024-08-18 10:51:46', '2024-08-17 11:51:46');

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
(61, 'Auto', NULL, '2024-07-26 05:54:21');

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
(5, 'chris', 'chris@gmail.com', '0111826871', 'Nairobi', '2024-07-26 06:39:01'),
(6, 'carol', 'carol@gmail.com', '0111826845', 'Abuja', '2024-07-26 07:01:27'),
(7, 'jim', 'jim@gmail.com', '0111826456', 'Texas', '2024-07-26 07:02:25'),
(8, 'dave', '', '', '', '2024-07-26 07:03:52'),
(9, 'joe', '', '', '', '2024-07-26 07:05:43'),
(10, 'jerome', '', '', '', '2024-07-26 07:07:04');

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
(2, 'salary for june', 10000.00, '2024-07-26 21:00:00', 'steve'),
(3, 'shop repairs', 1500.00, '2024-07-26 21:00:00', 'kyle');

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
(13, '001', 'mega stores', 'sales order', '2016-01-17', 'paid', '250028', 'PO Box 16122 Collins Street West, Victoria Nairobi Kenya', 'PO Box 16122 Collins Street West, Victoria Nairobi Kenya', 'Threadneedle St', '12333456789', '2020-08-12', 1200.00, 0.00, 1300.75, 'It is a long established fact that a reader will be distracted by the readable content...', NULL, NULL, NULL, NULL),
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
(1, 2, 'e6a794bd7d8de2c2ea7e28ac3bb47977dd8c256beed05d6ba54f8675855ea691', '2024-07-25 10:04:22', '2024-07-25 10:04:22'),
(2, 4, '34038d77dff2783182cfd673cd87755aa37f75d2aa89981cd4e7259983267c9d', '2024-08-20 07:48:04', '2024-08-20 07:48:04'),
(3, 4, '092cc161edd95a139f83a9b31b3c60c19209b8c9d3dd71aeec5febb31ad2c7f0', '2024-08-20 07:48:31', '2024-08-20 07:48:31'),
(4, 4, 'ff0f7f87a906e21b8d0c93368e8f0b1be17be0bdba8d1b084acb9017cb0ad421', '2024-08-20 07:48:40', '2024-08-20 07:48:40');

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
(16, 'Toyota-Corolla', 'Toyota-Corolla-2024-White', 15.00, 10.00, 61, '2024-07-26 05:54:21', 3, 2, 'uploads/toyota-corolla-2024.jpg', 'Goods', 'chris', 'Auto');

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
(13, '2024-08-21', 20315.00, 42.85, '[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"2\",\"total_sales\":\"12000.00\",\"total_cost\":\"6000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"2250.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"1250.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Iphone 15 128GB\",\"total_quantity\":\"3\",\"total_sales\":\"4500.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"900.00\",\"inventory_turnover_rate\":\"0.1500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"8\",\"total_sales\":\"1200.00\",\"total_cost\":\"800.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral-Pleated-Weave-Dress\",\"total_quantity\":\"10\",\"total_sales\":\"350.00\",\"total_cost\":\"200.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Toyota-Corolla\",\"total_quantity\":\"1\",\"total_sales\":\"15.00\",\"total_cost\":\"10.00\",\"total_profit\":\"5.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 8705.00, -2905.00, '2024-08-21 09:17:01', 20315, 29, 99, 99, -99),
(14, '2024-08-26', 20315.00, 42.85, '[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"2\",\"total_sales\":\"12000.00\",\"total_cost\":\"6000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"2250.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"1250.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Iphone 15 128GB\",\"total_quantity\":\"3\",\"total_sales\":\"4500.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"900.00\",\"inventory_turnover_rate\":\"0.1500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"8\",\"total_sales\":\"1200.00\",\"total_cost\":\"800.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral-Pleated-Weave-Dress\",\"total_quantity\":\"10\",\"total_sales\":\"350.00\",\"total_cost\":\"200.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Toyota-Corolla\",\"total_quantity\":\"1\",\"total_sales\":\"15.00\",\"total_cost\":\"10.00\",\"total_profit\":\"5.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 8705.00, -2905.00, '2024-08-26 13:24:43', 20315, 29, 99, 99, -99),
(15, '2024-08-27', 20315.00, 42.85, '[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"2\",\"total_sales\":\"12000.00\",\"total_cost\":\"6000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"2250.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"1250.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Iphone 15 128GB\",\"total_quantity\":\"3\",\"total_sales\":\"4500.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"900.00\",\"inventory_turnover_rate\":\"0.1500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"8\",\"total_sales\":\"1200.00\",\"total_cost\":\"800.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral-Pleated-Weave-Dress\",\"total_quantity\":\"10\",\"total_sales\":\"350.00\",\"total_cost\":\"200.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Toyota-Corolla\",\"total_quantity\":\"1\",\"total_sales\":\"15.00\",\"total_cost\":\"10.00\",\"total_profit\":\"5.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 8705.00, -2905.00, '2024-08-27 08:41:54', 20315, 29, 99, 99, -99),
(16, '2024-08-28', 20315.00, 42.85, '[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"2\",\"total_sales\":\"12000.00\",\"total_cost\":\"6000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"2250.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"1250.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Iphone 15 128GB\",\"total_quantity\":\"3\",\"total_sales\":\"4500.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"900.00\",\"inventory_turnover_rate\":\"0.1500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"8\",\"total_sales\":\"1200.00\",\"total_cost\":\"800.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral-Pleated-Weave-Dress\",\"total_quantity\":\"10\",\"total_sales\":\"350.00\",\"total_cost\":\"200.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Toyota-Corolla\",\"total_quantity\":\"1\",\"total_sales\":\"15.00\",\"total_cost\":\"10.00\",\"total_profit\":\"5.00\",\"inventory_turnover_rate\":\"0.3333\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 8705.00, -2905.00, '2024-08-28 09:19:34', 20315, 29, 99, 99, -99);

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
(16, 11, 2, 5, 43, 2, 6000.00, '2024-07-26 06:39:01', 'completed', 'paid', 'Samsung Galaxy', 'Goods', 'sold', 'uploads/samsung-galaxy-a10s.jpg'),
(17, 12, 2, 6, 48, 5, 450.00, '2024-07-26 07:01:27', 'completed', 'paid', 'Necklace', 'Goods', 'sold', 'uploads/goldnecklace.jpeg'),
(18, 14, 2, 7, 49, 8, 150.00, '2024-07-26 07:02:25', 'completed', 'paid', 'Nike Sneakers', 'Goods', 'sold', 'uploads/nike-sneakers.jpg'),
(19, 15, 2, 8, 50, 10, 35.00, '2024-07-26 07:03:52', 'completed', 'paid', 'Floral-Pleated-Weave-Dress', 'Goods', 'sold', 'uploads/floral-pleated-weave-dress.jpg'),
(20, 13, 2, 9, 51, 3, 1500.00, '2024-07-26 07:05:43', 'completed', 'paid', 'Iphone 15 128GB', 'Goods', 'sold', 'uploads/iphone-15-128gb.jpg'),
(21, 16, 2, 10, 52, 1, 15000.00, '2024-07-26 07:07:04', 'completed', 'paid', 'Toyota-Corolla', 'Goods', 'sold', 'uploads/toyota-corolla-2024.jpg');

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
(147, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-23 07:54:21', 20315, 29, 99, 0, 0, 0),
(148, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-23 07:56:53', 20315, 29, 99, 0, 0, 0),
(149, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-26 13:23:26', 20315, 29, 99, 0, 0, 0),
(150, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 07:33:28', 20315, 29, 99, 0, 0, 0),
(151, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 08:06:01', 20315, 29, 99, 0, 0, 0),
(152, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 08:42:29', 20315, 29, 99, 0, 0, 0),
(153, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 09:00:47', 20315, 29, 99, 0, 0, 0),
(154, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 09:08:09', 20315, 29, 99, 0, 0, 0),
(155, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 09:09:41', 20315, 29, 99, 0, 0, 0),
(156, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-27 09:09:44', 20315, 29, 99, 0, 0, 0),
(157, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:17:03', 20315, 29, 99, 0, 0, 0),
(158, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:17:35', 20315, 29, 99, 0, 0, 0),
(159, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:18:18', 20315, 29, 99, 0, 0, 0),
(160, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:18:56', 20315, 29, 99, 0, 0, 0),
(161, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:23:24', 20315, 29, 99, 0, 0, 0),
(162, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:24:56', 20315, 29, 99, 0, 0, 0),
(163, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:26:10', 20315, 29, 99, 0, 0, 0),
(164, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:26:39', 20315, 29, 99, 0, 0, 0),
(165, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:27:17', 20315, 29, 99, 0, 0, 0),
(166, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:29:21', 20315, 29, 99, 0, 0, 0),
(167, '2024-08-17', 20315.00, 42.85, 0.00, 0.00, 700.52, 0.14, 999.99, 0.00, 0.00, 11610.00, 8705.00, '2024-08-28 09:44:56', 20315, 29, 99, 0, 0, 0);

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
(43, 'james', 'james@gmail.com', '0111826234', '', '2024-07-26 06:39:01'),
(48, 'john', 'john@gmail.com', '0111826678', '', '2024-07-26 07:01:27'),
(49, 'carrey', 'carrey@gmail.com', '0112346872', '', '2024-07-26 07:02:25'),
(50, 'peter', '', '', 'sales', '2024-07-26 07:03:52'),
(51, 'jean', '', '', 'sales', '2024-07-26 07:05:43'),
(52, 'king', '', '', 'sales', '2024-07-26 07:07:04');

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
(1, 'Demarck Suppliers', '', '', '', '2024-07-29 17:34:11', 'Iphone 15 Pro Max', 10, 'good condition'),
(2, 'Goons Suppliers', '', '', '', '2024-07-29 17:41:46', 'Samsung Galaxy', 15, 'good condition');

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
(2, 'megastores', 'megastores@gmail.com', '$2y$10$C0Afoh20GiefSBzZu.gRZuOXec5WnLLEPSFFD1M1jpX48EvEN0UYu', NULL, NULL, '2024-07-25 09:03:41', 'mega1234', 'uploads/.trashed-1718858585-Favicon.jpg', '0111826872', 'Texas'),
(4, 'Skystores', 'olphemie@hotmail.com', '$2y$10$kSrW8s0wVyw.E/oxxUt1Duq2aFLNdEmCyXutAIcvVMCauJ3oIUDTi', NULL, NULL, '2024-08-17 11:51:46', 'sky1234', 'uploads/FB_IMG_1673482183625.jpg', '0111826872', 'London ');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sales_analytics`
--
ALTER TABLE `sales_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation_codes`
--
ALTER TABLE `activation_codes`
  ADD CONSTRAINT `activation_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `sales_ibfk_4` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`staff_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
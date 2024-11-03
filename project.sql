-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2024 at 02:55 AM
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
-- Database: `project`
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
(1, 1, '67261b704fe59', '2024-11-03 10:30:40', '2024-11-02 12:30:40');

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
(1, 'Phones', NULL, '2024-11-02 12:34:34'),
(2, 'Foods', NULL, '2024-11-02 12:45:11'),
(3, 'Cosmetic', NULL, '2024-11-02 12:55:36'),
(4, 'Fashion', NULL, '2024-11-02 13:00:14'),
(5, 'Devices', NULL, '2024-11-02 15:51:21'),
(7, 'Auto', NULL, '2024-11-02 16:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(8, 'kyle', '', '', '', '2024-11-02 15:31:53'),
(9, 'jim', '', '', '', '2024-11-02 15:35:28'),
(10, 'joe', '', '', '', '2024-11-02 15:39:44'),
(11, 'kimolee', '', '', '', '2024-11-02 15:41:12'),
(12, 'gina', '', '', '', '2024-11-02 15:54:01'),
(14, 'chris', 'chris@gmail.com', '45678923', 'Texas', '2024-11-02 16:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `description`, `amount`, `expense_date`, `created_by`) VALUES
(1, 'Rent', 800.00, '2024-10-31 21:00:00', 'sam'),
(2, 'Renovation', 400.00, '2024-10-02 21:00:00', 'collins'),
(3, 'Loans', 3000.00, '2024-09-10 21:00:00', 'Luke');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
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

INSERT INTO `inventory` (`inventory_id`, `product_id`, `sales_qty`, `last_updated`, `stock_qty`, `supply_qty`, `product_name`) VALUES
(1, 1, 10, '2024-11-02 19:44:16', 20, 27, 'Samsung Galaxy'),
(2, 2, 60, '2024-11-02 19:44:16', 100, 200, 'Pilsner'),
(3, 7, 12, '2024-11-02 19:44:16', 20, 12, 'Apple iPhone'),
(4, 6, 40, '2024-11-02 19:44:16', 100, 80, 'Vitamin Water'),
(5, 5, 25, '2024-11-02 19:44:16', 50, 25, 'Floral Dress'),
(6, 4, 18, '2024-11-02 19:44:16', 40, 25, 'Beauty Soap'),
(7, 8, 12, '2024-11-02 19:44:16', 20, 12, 'Sony Camera');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `invoice_description` text DEFAULT NULL,
  `order_date` date NOT NULL,
  `order_status` enum('Paid','Unpaid') NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `delivery_address` text NOT NULL,
  `mode_of_payment` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS (`subtotal` - `subtotal` * (`discount` / 100)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `customer_name`, `invoice_description`, `order_date`, `order_status`, `order_id`, `delivery_address`, `mode_of_payment`, `due_date`, `subtotal`, `discount`) VALUES
(11, '435657', 'joe  23', 'delivery', '2024-10-11', 'Paid', '5678', 'Madrid ', 'Mpesa', '2024-11-01', 910.00, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `invoice_items_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (`qty` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`invoice_items_id`, `invoice_id`, `item_name`, `qty`, `price`) VALUES
(23, 11, 'Dell laptop', 2, 215.00),
(24, 11, 'Mens shoes', 3, 160.00);

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

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payments_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `paypal_email` varchar(255) DEFAULT NULL,
  `binance_pay_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Samsung Galaxy', 'A10s', 150.00, 100.00, 1, '2024-11-02 12:34:34', 20, 27, 'uploads/products/samsung-galaxy-a10s.jpg', 'Goods', 'kim', 'Phones'),
(2, 'Pilsner', 'Premium Beer', 40.00, 35.00, 2, '2024-11-02 12:45:11', 100, 200, 'uploads/products/beer.jpg', 'Goods', 'john', 'Foods'),
(4, 'Beauty Soap', 'Skin care soap', 21.00, 15.00, 3, '2024-11-02 12:55:37', 40, 25, 'uploads/products/soap.jpg', 'Goods', 'stacey', 'Cosmetic'),
(5, 'Floral Dress', 'Pleated Woven Dress', 35.00, 20.00, 4, '2024-11-02 13:00:14', 50, 25, 'uploads/products/floral-pleated-weave-dress.jpg', 'Goods', 'james', 'Fashion'),
(6, 'Vitamin Water', 'Premium Spring Water', 15.00, 10.00, 2, '2024-11-02 13:04:22', 100, 80, 'uploads/products/water.jpg', 'Goods', 'chris', 'Foods'),
(7, 'Apple iPhone', 'Apple IPhone 15 128GB', 1200.00, 900.00, 1, '2024-11-02 13:06:04', 20, 12, 'uploads/products/iphone-15-128gb.jpg', 'Goods', 'john', 'Phones'),
(8, 'Sony Camera', 'AI Digital camera', 105.00, 85.00, 5, '2024-11-02 15:51:21', 20, 12, 'uploads/products/camera.jpg', 'Goods', 'kim', 'Devices'),
(9, 'Nike Sneakers', 'Air Jordan Sneakers', 75.00, 50.00, 6, '2024-11-02 16:01:53', 30, 45, 'uploads/products/nike-sneakers.jpg', 'Goods', 'james', 'Fashion');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `reports_id` int(11) NOT NULL,
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

INSERT INTO `reports` (`reports_id`, `report_date`, `revenue`, `profit_margin`, `revenue_by_product`, `year_over_year_growth`, `cost_of_selling`, `inventory_turnover_rate`, `stock_to_sales_ratio`, `sell_through_rate`, `gross_margin_by_product`, `net_margin_by_product`, `gross_margin`, `net_margin`, `created_at`, `total_sales`, `total_quantity`, `total_profit`, `total_expenses`, `net_profit`) VALUES
(1, '2024-11-02', 21413.00, 24.86, '[{\"product_id\":1,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"10\",\"total_sales\":\"1500.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"500.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":2,\"product_name\":\"Pilsner\",\"total_quantity\":\"60\",\"total_sales\":\"2400.00\",\"total_cost\":\"2100.00\",\"total_profit\":\"300.00\",\"inventory_turnover_rate\":\"0.6000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":4,\"product_name\":\"Beauty Soap\",\"total_quantity\":\"18\",\"total_sales\":\"378.00\",\"total_cost\":\"270.00\",\"total_profit\":\"108.00\",\"inventory_turnover_rate\":\"0.4500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":5,\"product_name\":\"Floral Dress\",\"total_quantity\":\"25\",\"total_sales\":\"875.00\",\"total_cost\":\"500.00\",\"total_profit\":\"375.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":6,\"product_name\":\"Vitamin Water\",\"total_quantity\":\"40\",\"total_sales\":\"600.00\",\"total_cost\":\"400.00\",\"total_profit\":\"200.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":7,\"product_name\":\"Apple iPhone\",\"total_quantity\":\"12\",\"total_sales\":\"14400.00\",\"total_cost\":\"10800.00\",\"total_profit\":\"3600.00\",\"inventory_turnover_rate\":\"0.6000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":8,\"product_name\":\"Sony Camera\",\"total_quantity\":\"12\",\"total_sales\":\"1260.00\",\"total_cost\":\"1020.00\",\"total_profit\":\"240.00\",\"inventory_turnover_rate\":\"0.6000\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 5323.00, -10767.00, '2024-11-02 16:13:14', 21413, 177, 99, 99, -99);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `sale_status` enum('completed','pending') NOT NULL DEFAULT 'pending',
  `payment_status` enum('completed','due','paid') NOT NULL DEFAULT 'due',
  `name` varchar(255) NOT NULL,
  `product_type` enum('Goods','Services','Digital') NOT NULL,
  `sale_note` varchar(255) NOT NULL,
  `sales_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`sales_qty` * `sales_price`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `product_id`, `user_id`, `customer_id`, `staff_id`, `sales_qty`, `sale_date`, `sale_status`, `payment_status`, `name`, `product_type`, `sale_note`, `sales_price`) VALUES
(1, 1, 1, 8, 8, 10, '2024-01-16 15:31:53', 'completed', 'paid', 'Samsung Galaxy', 'Goods', 'sold', 150.00),
(2, 2, 1, 9, 9, 60, '2024-02-02 15:35:28', 'completed', 'paid', 'Pilsner', 'Goods', 'sold', 40.00),
(3, 7, 1, 10, 10, 12, '2024-03-22 15:39:44', 'completed', 'paid', 'Apple iPhone', 'Goods', 'sold', 1200.00),
(4, 6, 1, 11, 11, 40, '2024-04-11 15:41:12', 'completed', 'paid', 'Vitamin Water', 'Goods', 'sold', 15.00),
(5, 5, 1, 12, 12, 25, '2024-05-02 15:54:01', 'completed', 'paid', 'Floral Dress', 'Goods', 'sold', 35.00),
(6, 4, 1, 8, 8, 18, '2024-06-15 15:54:54', 'completed', 'paid', 'Beauty Soap', 'Goods', '', 21.00),
(7, 8, 1, 13, 9, 12, '2024-07-02 15:57:34', 'completed', 'paid', 'Sony Camera', 'Goods', '', 105.00);

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
  `revenue_by_category` decimal(2,0) NOT NULL,
  `most_sold_product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_analytics`
--

INSERT INTO `sales_analytics` (`id`, `date`, `revenue`, `profit_margin`, `year_over_year_growth`, `cost_of_selling`, `inventory_turnover_rate`, `stock_to_sales_ratio`, `sell_through_rate`, `gross_margin_by_category`, `net_margin_by_category`, `gross_margin`, `net_margin`, `created_at`, `total_sales`, `total_quantity`, `total_profit`, `total_expenses`, `net_profit`, `revenue_by_category`, `most_sold_product_id`) VALUES
(1, '2024-11-02', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-11-02 13:26:02', 0, 0, 0, 0, 0, 0, 0),
(2, '2024-11-02', 69215.00, 22.17, 0.00, 0.00, 999.99, 0.07, 999.99, 0.00, 0.00, 53870.00, 15345.00, '2024-11-02 16:07:46', 69215, 46, 99, 0, 0, 0, 0),
(3, '2024-11-02', 69215.00, 22.17, 0.00, 0.00, 999.99, 0.07, 999.99, 0.00, 0.00, 53870.00, 15345.00, '2024-11-02 16:12:53', 69215, 46, 99, 0, 0, 0, 0),
(4, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:50:12', 21413, 177, 99, 0, 0, 0, 0),
(5, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:50:56', 21413, 177, 99, 0, 0, 0, 0),
(6, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:53:46', 21413, 177, 99, 0, 0, 0, 0),
(7, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:54:32', 21413, 177, 99, 0, 0, 0, 0),
(8, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:55:23', 21413, 177, 99, 0, 0, 0, 0),
(9, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 19:55:55', 21413, 177, 99, 0, 0, 0, 0),
(10, '2024-11-02', 21413.00, 24.86, 0.00, 0.00, 120.98, 0.83, 999.99, 0.00, 0.00, 16090.00, 5323.00, '2024-11-02 20:03:11', 21413, 177, 99, 0, 0, 0, 0);

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
(8, 'james', '', '', 'sales', '2024-11-02 15:31:53'),
(9, 'chris', '', '', 'sales', '2024-11-02 15:35:28'),
(10, 'john', '', '', 'sales', '2024-11-02 15:39:44'),
(11, 'jack', 'jack@gmail.com', '123456789', 'sales', '2024-11-02 15:41:12'),
(12, 'mark', '', '', 'sales', '2024-11-02 15:54:01');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
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

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `supplier_email`, `supplier_phone`, `supplier_location`, `created_at`, `product_name`, `supply_qty`, `note`) VALUES
(1, 'East Africa Distillery', 'eadl@gmail.com', '0101674356', 'Madrid', '2024-11-02 16:28:43', 'Premium Beer', 200, 'good');

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
(1, 'superstores', 'olphemie@hotmail.com', '$2y$10$17c6i5VDkwQ0on0RqW3FX.qcmRKCIYzokQOrcP7LRFQzGQY5u6dl.', NULL, NULL, '2024-11-02 12:30:40', 'super1234', 'uploads/user/1730551257_1730520358_1726523112_20230712_130458.jpg', '', 'Texas');

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
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `unique_invoice_item` (`invoice_number`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`invoice_items_id`),
  ADD KEY `invoice_id_idx` (`invoice_id`);

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
  ADD PRIMARY KEY (`payments_id`);

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
  ADD PRIMARY KEY (`reports_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
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
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_codes`
--
ALTER TABLE `activation_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `invoice_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payments_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `reports_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales_analytics`
--
ALTER TABLE `sales_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

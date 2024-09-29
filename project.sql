-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 09:30 PM
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
CREATE DATABASE IF NOT EXISTS `project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `project`;

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
(66, 'Phones', NULL, '2024-09-16 22:23:33'),
(67, 'Foods', NULL, '2024-09-16 22:46:14'),
(68, 'Apparels', NULL, '2024-09-16 23:12:02'),
(69, 'Electronics', NULL, '2024-09-16 23:17:01'),
(70, 'Auto', NULL, '2024-09-16 23:19:18'),
(71, 'Cosmetic', NULL, '2024-09-16 23:21:29'),
(72, 'Jewellry', NULL, '2024-09-16 23:25:13'),
(73, 'Items', NULL, '2024-09-16 23:29:51');

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
(46, 'kimo', '', '', '', '2024-09-16 23:08:06'),
(47, 'chris', '', '', '', '2024-09-16 23:58:29'),
(48, 'joe', '', '', '', '2024-09-16 23:59:49'),
(49, 'jane', '', '', '', '2024-09-17 00:03:47'),
(50, 'gina', '', '', '', '2024-09-17 00:05:50'),
(51, 'paul', '', '', '', '2024-09-17 00:07:59'),
(52, 'joy', '', '', '', '2024-09-17 00:09:19'),
(53, 'dave', '', '', '', '2024-09-17 00:12:27'),
(54, 'vera', '', '', '', '2024-09-17 00:14:43'),
(55, 'lanre', '', '', '', '2024-09-17 00:15:59'),
(56, 'justina', '', '', '', '2024-09-17 00:17:18'),
(57, 'mike', '', '', '', '2024-09-17 00:20:16'),
(58, 'janice', '', '', '', '2024-09-17 00:21:31'),
(59, 'kyle', '', '', '', '2024-09-17 00:39:08'),
(60, 'olu', '', '', '', '2024-09-17 00:40:10'),
(61, 'samuel', '', '', '', '2024-09-17 00:42:22');

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
(5, 'Rent', 30000.00, '2024-09-16 21:00:00', 'james'),
(6, 'Power Bills', 10000.00, '2024-09-16 21:00:00', 'dapo'),
(7, 'Delivery fees', 25000.00, '2024-09-16 21:00:00', 'yemi');

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
(8, 25, 4, '2024-09-17 00:47:20', 10, 15, 'Samsung Galaxy'),
(9, 31, 23, '2024-09-17 00:18:46', 50, 100, 'Premium Beer'),
(10, 29, 4, '2024-09-17 00:47:21', 10, 12, 'Apple iPhone'),
(11, 30, 5, '2024-09-17 00:18:46', 20, 10, 'Floral Dress'),
(12, 27, 50, '2024-09-17 00:18:46', 100, 120, 'Bottled Water'),
(13, 32, 5, '2024-09-17 00:18:46', 10, 6, 'Camera'),
(14, 33, 2, '2024-09-17 00:47:21', 2, 1, 'Chevrolet AWD'),
(15, 34, 20, '2024-09-17 00:18:46', 28, 25, 'Mary Kay'),
(16, 35, 5, '2024-09-17 00:18:47', 40, 10, 'Necklace'),
(17, 36, 15, '2024-09-17 00:18:47', 30, 50, 'Sony Headphones'),
(18, 37, 40, '2024-09-17 00:18:47', 100, 60, 'Hike Bag'),
(19, 38, 5, '2024-09-17 00:18:47', 5, 10, 'Dior female shoes'),
(20, 39, 25, '2024-09-17 00:29:11', 20, 50, 'Lip Stick'),
(21, 40, 10, '2024-09-17 00:29:11', 70, 40, 'Make-up Kit'),
(22, 41, 15, '2024-09-17 00:29:11', 25, 80, 'Leather Shoes'),
(23, 42, 10, '2024-09-17 00:29:12', 20, 30, 'Tea mugs'),
(24, 43, 15, '2024-09-17 00:29:12', 70, 15, 'Air Jordan'),
(25, 44, 100, '2024-09-17 00:29:12', 150, 200, 'Perfume Spray'),
(26, 45, 40, '2024-09-17 00:47:21', 100, 50, 'Rayban Sunglasses'),
(27, 47, 25, '2024-09-17 00:47:21', 40, 60, 'Apple Speakers'),
(28, 48, 2, '2024-09-17 00:47:21', 3, 2, 'Toyota corolla'),
(29, 49, 10, '2024-09-17 00:47:21', 10, 30, 'Tissot  watch');

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
(25, 'Samsung Galaxy', 'Samsung Galaxy A10s', 250.00, 150.00, 66, '2024-01-16 22:23:33', 50, 25, 'uploads/products/samsung-galaxy-a10s.jpg', 'Goods', 'james', 'Phones'),
(27, 'Bottled Water', 'Spring Bottled Water', 35.00, 20.00, 67, '2024-02-16 22:46:14', 2000, 1500, 'uploads/products/water.jpg', 'Goods', 'john', 'Foods'),
(29, 'Apple iPhone', 'Apple iPhone 15', 1500.00, 1000.00, 66, '2024-03-16 23:06:06', 80, 75, 'uploads/products/iphone-15-128gb.jpg', 'Goods', 'john', 'Phones'),
(30, 'Floral Dress', 'Floral Pleated Weave Dress', 245.00, 200.00, 68, '2024-04-16 23:12:02', 20, 70, 'uploads/products/floral-pleated-weave-dress.jpg', 'Goods', 'mark', 'Apparels'),
(31, 'Premium Beer', 'Premium Beer', 35.00, 20.00, 67, '2024-05-16 23:15:20', 500, 100, 'uploads/products/beer.jpg', 'Goods', 'james', 'Foods'),
(32, 'Camera', 'Digital Camera', 280.00, 160.00, 69, '2024-06-16 23:17:01', 60, 46, 'uploads/products/camera.jpg', 'Goods', 'james', 'Electronics'),
(33, 'Chevrolet AWD', 'Chevrolet  AWD 2024 White', 65000.00, 55000.00, 70, '2024-07-16 23:19:18', 3, 2, 'uploads/products/Chevrolet.jpg', 'Goods', 'sam', 'Auto'),
(34, 'Mary Kay', 'Lip Gloss', 122.00, 84.00, 71, '2024-09-16 23:21:29', 280, 250, 'uploads/products/facials.jpg', 'Goods', 'stacey', 'Cosmetic'),
(35, 'Necklace', 'Gold Necklace 50grams', 18000.00, 12000.00, 72, '2024-08-16 23:25:13', 40, 10, 'uploads/products/goldnecklace.jpeg', 'Goods', 'kim', 'Jewellry'),
(36, 'Sony Headphones', 'Sony Digital Headphones', 300.00, 200.00, 69, '2024-09-16 23:26:41', 80, 50, 'uploads/products/headphones.jpg', 'Goods', 'chris', 'Electronics'),
(37, 'Hike Bag', 'Outdoor Hiking Bag', 550.00, 400.00, 73, '2024-10-16 23:29:51', 100, 60, 'uploads/products/hike-bag.jpg', 'Goods', 'stacey', 'Items'),
(38, 'Dior female shoes', 'Dior Female fashion shoes', 285.00, 160.00, 68, '2024-11-16 23:32:34', 50, 15, 'uploads/products/lady-shoes.jpg', 'Goods', 'james', 'Apparels'),
(39, 'Lip Stick', 'Flavored Lip Gloss', 40.00, 25.00, 71, '2024-09-16 23:34:38', 200, 500, 'uploads/products/lipstick.jpg', 'Goods', 'mark', 'Cosmetic'),
(40, 'Make-up Kit', 'Mary Kay make-up kit', 450.00, 340.00, 71, '2024-12-16 23:36:32', 700, 400, 'uploads/products/make-up.jpg', 'Goods', 'kim', 'Cosmetic'),
(41, 'Leather Shoes', 'Mens leather shoes', 350.00, 250.00, 68, '2024-01-16 23:39:14', 25, 80, 'uploads/products/men clothings.jpg', 'Goods', 'chris', 'Apparels'),
(42, 'Tea mugs', 'Plain Tea mugs', 55.00, 30.00, 73, '2024-02-16 23:41:08', 200, 350, 'uploads/products/mugs.jpg', 'Goods', 'mark', 'Items'),
(43, 'Air Jordan', 'Nike Air Jordan   Sneakers', 200.00, 130.00, 68, '2024-09-16 23:44:02', 70, 15, 'uploads/products/nike-sneakers.jpg', 'Goods', 'james', 'Apparels'),
(44, 'Perfume Spray', 'Armani perfume spray', 17.00, 10.00, 71, '2024-03-16 23:46:02', 150, 200, 'uploads/products/perfume.jpg', 'Goods', 'chris', 'Cosmetic'),
(45, 'Rayban Sunglasses', 'Rayban Sunglasses', 35.00, 20.00, 73, '2024-04-16 23:47:27', 100, 50, 'uploads/products/rayban.jpg', 'Goods', 'james', 'Apparels'),
(46, 'Herbal  Soap', 'Cosmetic  Soap', 60.00, 40.00, 71, '2024-05-16 23:49:36', 45, 80, 'uploads/products/soap.jpg', 'Goods', 'james', 'Cosmetic'),
(47, 'Apple Speakers', 'Apple Speakers', 75.00, 65.00, 69, '2024-06-16 23:53:45', 40, 60, 'uploads/products/speaker.jpg', 'Goods', 'mark', 'Electronics'),
(48, 'Toyota corolla', 'Toyota corolla sedan 2024', 18000.00, 14000.00, 70, '2024-07-16 23:55:29', 3, 2, 'uploads/products/toyota-corolla-2024.jpg', 'Goods', 'john', 'Auto'),
(49, 'Tissot  watch', 'Tissot  chronograph watch', 450.00, 300.00, 72, '2024-08-16 23:56:57', 10, 30, 'uploads/products/wristwatch.jpg', 'Goods', 'stacey', 'Apparels');

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
(18, '2024-09-17', 218765.00, 28.62, '[{\"product_id\":25,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"4\",\"total_sales\":\"600.00\",\"total_cost\":\"400.00\",\"total_profit\":\"200.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":27,\"product_name\":\"Bottled Water\",\"total_quantity\":\"50\",\"total_sales\":\"750.00\",\"total_cost\":\"450.00\",\"total_profit\":\"300.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":29,\"product_name\":\"Apple iPhone\",\"total_quantity\":\"4\",\"total_sales\":\"6000.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"2400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":30,\"product_name\":\"Floral Dress\",\"total_quantity\":\"5\",\"total_sales\":\"225.00\",\"total_cost\":\"100.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"0.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":31,\"product_name\":\"Premium Beer\",\"total_quantity\":\"23\",\"total_sales\":\"575.00\",\"total_cost\":\"460.00\",\"total_profit\":\"115.00\",\"inventory_turnover_rate\":\"0.4600\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":32,\"product_name\":\"Camera\",\"total_quantity\":\"5\",\"total_sales\":\"500.00\",\"total_cost\":\"300.00\",\"total_profit\":\"200.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":33,\"product_name\":\"Chevrolet AWD\",\"total_quantity\":\"2\",\"total_sales\":\"150000.00\",\"total_cost\":\"110000.00\",\"total_profit\":\"40000.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":34,\"product_name\":\"Mary Kay\",\"total_quantity\":\"20\",\"total_sales\":\"440.00\",\"total_cost\":\"280.00\",\"total_profit\":\"160.00\",\"inventory_turnover_rate\":\"0.7143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":35,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"9000.00\",\"total_cost\":\"2500.00\",\"total_profit\":\"6500.00\",\"inventory_turnover_rate\":\"0.1250\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":36,\"product_name\":\"Sony Headphones\",\"total_quantity\":\"15\",\"total_sales\":\"450.00\",\"total_cost\":\"300.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":37,\"product_name\":\"Hike Bag\",\"total_quantity\":\"40\",\"total_sales\":\"2200.00\",\"total_cost\":\"1600.00\",\"total_profit\":\"600.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":38,\"product_name\":\"Dior female shoes\",\"total_quantity\":\"5\",\"total_sales\":\"425.00\",\"total_cost\":\"300.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":39,\"product_name\":\"Lip Stick\",\"total_quantity\":\"25\",\"total_sales\":\"250.00\",\"total_cost\":\"125.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"1.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":40,\"product_name\":\"Make-up Kit\",\"total_quantity\":\"10\",\"total_sales\":\"450.00\",\"total_cost\":\"400.00\",\"total_profit\":\"50.00\",\"inventory_turnover_rate\":\"0.1429\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":41,\"product_name\":\"Leather Shoes\",\"total_quantity\":\"15\",\"total_sales\":\"525.00\",\"total_cost\":\"375.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.6000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":42,\"product_name\":\"Tea mugs\",\"total_quantity\":\"10\",\"total_sales\":\"150.00\",\"total_cost\":\"100.00\",\"total_profit\":\"50.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":43,\"product_name\":\"Air Jordan\",\"total_quantity\":\"15\",\"total_sales\":\"750.00\",\"total_cost\":\"450.00\",\"total_profit\":\"300.00\",\"inventory_turnover_rate\":\"0.2143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":44,\"product_name\":\"Perfume Spray\",\"total_quantity\":\"100\",\"total_sales\":\"1700.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"700.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":45,\"product_name\":\"Rayban Sunglasses\",\"total_quantity\":\"40\",\"total_sales\":\"1400.00\",\"total_cost\":\"800.00\",\"total_profit\":\"600.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":47,\"product_name\":\"Apple Speakers\",\"total_quantity\":\"25\",\"total_sales\":\"1875.00\",\"total_cost\":\"1625.00\",\"total_profit\":\"250.00\",\"inventory_turnover_rate\":\"0.6250\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":48,\"product_name\":\"Toyota corolla\",\"total_quantity\":\"2\",\"total_sales\":\"36000.00\",\"total_cost\":\"28000.00\",\"total_profit\":\"8000.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":49,\"product_name\":\"Tissot  watch\",\"total_quantity\":\"10\",\"total_sales\":\"4500.00\",\"total_cost\":\"3000.00\",\"total_profit\":\"1500.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 508.76, 0.20, 999.99, 0.00, 0.00, 62600.00, -93565.00, '2024-09-17 00:51:00', 218765, 430, 99, 99, -99),
(19, '2024-09-23', 218765.00, 28.62, '[{\"product_id\":25,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"4\",\"total_sales\":\"600.00\",\"total_cost\":\"400.00\",\"total_profit\":\"200.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":27,\"product_name\":\"Bottled Water\",\"total_quantity\":\"50\",\"total_sales\":\"750.00\",\"total_cost\":\"450.00\",\"total_profit\":\"300.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":29,\"product_name\":\"Apple iPhone\",\"total_quantity\":\"4\",\"total_sales\":\"6000.00\",\"total_cost\":\"3600.00\",\"total_profit\":\"2400.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":30,\"product_name\":\"Floral Dress\",\"total_quantity\":\"5\",\"total_sales\":\"225.00\",\"total_cost\":\"100.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"0.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":31,\"product_name\":\"Premium Beer\",\"total_quantity\":\"23\",\"total_sales\":\"575.00\",\"total_cost\":\"460.00\",\"total_profit\":\"115.00\",\"inventory_turnover_rate\":\"0.4600\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":32,\"product_name\":\"Camera\",\"total_quantity\":\"5\",\"total_sales\":\"500.00\",\"total_cost\":\"300.00\",\"total_profit\":\"200.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":33,\"product_name\":\"Chevrolet AWD\",\"total_quantity\":\"2\",\"total_sales\":\"150000.00\",\"total_cost\":\"110000.00\",\"total_profit\":\"40000.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":34,\"product_name\":\"Mary Kay\",\"total_quantity\":\"20\",\"total_sales\":\"440.00\",\"total_cost\":\"280.00\",\"total_profit\":\"160.00\",\"inventory_turnover_rate\":\"0.7143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":35,\"product_name\":\"Necklace\",\"total_quantity\":\"5\",\"total_sales\":\"9000.00\",\"total_cost\":\"2500.00\",\"total_profit\":\"6500.00\",\"inventory_turnover_rate\":\"0.1250\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":36,\"product_name\":\"Sony Headphones\",\"total_quantity\":\"15\",\"total_sales\":\"450.00\",\"total_cost\":\"300.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":37,\"product_name\":\"Hike Bag\",\"total_quantity\":\"40\",\"total_sales\":\"2200.00\",\"total_cost\":\"1600.00\",\"total_profit\":\"600.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":38,\"product_name\":\"Dior female shoes\",\"total_quantity\":\"5\",\"total_sales\":\"425.00\",\"total_cost\":\"300.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":39,\"product_name\":\"Lip Stick\",\"total_quantity\":\"25\",\"total_sales\":\"250.00\",\"total_cost\":\"125.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"1.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":40,\"product_name\":\"Make-up Kit\",\"total_quantity\":\"10\",\"total_sales\":\"450.00\",\"total_cost\":\"400.00\",\"total_profit\":\"50.00\",\"inventory_turnover_rate\":\"0.1429\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":41,\"product_name\":\"Leather Shoes\",\"total_quantity\":\"15\",\"total_sales\":\"525.00\",\"total_cost\":\"375.00\",\"total_profit\":\"150.00\",\"inventory_turnover_rate\":\"0.6000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":42,\"product_name\":\"Tea mugs\",\"total_quantity\":\"10\",\"total_sales\":\"150.00\",\"total_cost\":\"100.00\",\"total_profit\":\"50.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":43,\"product_name\":\"Air Jordan\",\"total_quantity\":\"15\",\"total_sales\":\"750.00\",\"total_cost\":\"450.00\",\"total_profit\":\"300.00\",\"inventory_turnover_rate\":\"0.2143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":44,\"product_name\":\"Perfume Spray\",\"total_quantity\":\"100\",\"total_sales\":\"1700.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"700.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":45,\"product_name\":\"Rayban Sunglasses\",\"total_quantity\":\"40\",\"total_sales\":\"1400.00\",\"total_cost\":\"800.00\",\"total_profit\":\"600.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":47,\"product_name\":\"Apple Speakers\",\"total_quantity\":\"25\",\"total_sales\":\"1875.00\",\"total_cost\":\"1625.00\",\"total_profit\":\"250.00\",\"inventory_turnover_rate\":\"0.6250\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":48,\"product_name\":\"Toyota corolla\",\"total_quantity\":\"2\",\"total_sales\":\"36000.00\",\"total_cost\":\"28000.00\",\"total_profit\":\"8000.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":49,\"product_name\":\"Tissot  watch\",\"total_quantity\":\"10\",\"total_sales\":\"4500.00\",\"total_cost\":\"3000.00\",\"total_profit\":\"1500.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 508.76, 0.20, 999.99, 0.00, 0.00, 62600.00, -93565.00, '2024-09-23 18:15:10', 218765, 430, 99, 99, -99),
(20, '2024-09-24', 1628175.00, 30.43, '[{\"product_id\":25,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"15\",\"total_sales\":\"3750.00\",\"total_cost\":\"2250.00\",\"total_profit\":\"1500.00\",\"inventory_turnover_rate\":\"0.3000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":27,\"product_name\":\"Bottled Water\",\"total_quantity\":\"500\",\"total_sales\":\"17500.00\",\"total_cost\":\"10000.00\",\"total_profit\":\"7500.00\",\"inventory_turnover_rate\":\"0.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":29,\"product_name\":\"Apple iPhone\",\"total_quantity\":\"40\",\"total_sales\":\"60000.00\",\"total_cost\":\"40000.00\",\"total_profit\":\"20000.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":30,\"product_name\":\"Floral Dress\",\"total_quantity\":\"150\",\"total_sales\":\"36750.00\",\"total_cost\":\"30000.00\",\"total_profit\":\"6750.00\",\"inventory_turnover_rate\":\"7.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":31,\"product_name\":\"Premium Beer\",\"total_quantity\":\"230\",\"total_sales\":\"8050.00\",\"total_cost\":\"4600.00\",\"total_profit\":\"3450.00\",\"inventory_turnover_rate\":\"0.4600\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":32,\"product_name\":\"Camera\",\"total_quantity\":\"25\",\"total_sales\":\"7000.00\",\"total_cost\":\"4000.00\",\"total_profit\":\"3000.00\",\"inventory_turnover_rate\":\"0.4167\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":33,\"product_name\":\"Chevrolet AWD\",\"total_quantity\":\"2\",\"total_sales\":\"130000.00\",\"total_cost\":\"110000.00\",\"total_profit\":\"20000.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":34,\"product_name\":\"Mary Kay\",\"total_quantity\":\"200\",\"total_sales\":\"24400.00\",\"total_cost\":\"16800.00\",\"total_profit\":\"7600.00\",\"inventory_turnover_rate\":\"0.7143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":35,\"product_name\":\"Necklace\",\"total_quantity\":\"50\",\"total_sales\":\"900000.00\",\"total_cost\":\"600000.00\",\"total_profit\":\"300000.00\",\"inventory_turnover_rate\":\"1.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":36,\"product_name\":\"Sony Headphones\",\"total_quantity\":\"150\",\"total_sales\":\"45000.00\",\"total_cost\":\"30000.00\",\"total_profit\":\"15000.00\",\"inventory_turnover_rate\":\"1.8750\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":37,\"product_name\":\"Hike Bag\",\"total_quantity\":\"400\",\"total_sales\":\"220000.00\",\"total_cost\":\"160000.00\",\"total_profit\":\"60000.00\",\"inventory_turnover_rate\":\"4.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":38,\"product_name\":\"Dior female shoes\",\"total_quantity\":\"50\",\"total_sales\":\"14250.00\",\"total_cost\":\"8000.00\",\"total_profit\":\"6250.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":39,\"product_name\":\"Lip Stick\",\"total_quantity\":\"250\",\"total_sales\":\"10000.00\",\"total_cost\":\"6250.00\",\"total_profit\":\"3750.00\",\"inventory_turnover_rate\":\"1.2500\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":40,\"product_name\":\"Make-up Kit\",\"total_quantity\":\"100\",\"total_sales\":\"45000.00\",\"total_cost\":\"34000.00\",\"total_profit\":\"11000.00\",\"inventory_turnover_rate\":\"0.1429\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":41,\"product_name\":\"Leather Shoes\",\"total_quantity\":\"150\",\"total_sales\":\"52500.00\",\"total_cost\":\"37500.00\",\"total_profit\":\"15000.00\",\"inventory_turnover_rate\":\"6.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":42,\"product_name\":\"Tea mugs\",\"total_quantity\":\"100\",\"total_sales\":\"5500.00\",\"total_cost\":\"3000.00\",\"total_profit\":\"2500.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":43,\"product_name\":\"Air Jordan\",\"total_quantity\":\"15\",\"total_sales\":\"3000.00\",\"total_cost\":\"1950.00\",\"total_profit\":\"1050.00\",\"inventory_turnover_rate\":\"0.2143\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":44,\"product_name\":\"Perfume Spray\",\"total_quantity\":\"100\",\"total_sales\":\"1700.00\",\"total_cost\":\"1000.00\",\"total_profit\":\"700.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":45,\"product_name\":\"Rayban Sunglasses\",\"total_quantity\":\"40\",\"total_sales\":\"1400.00\",\"total_cost\":\"800.00\",\"total_profit\":\"600.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":47,\"product_name\":\"Apple Speakers\",\"total_quantity\":\"25\",\"total_sales\":\"1875.00\",\"total_cost\":\"1625.00\",\"total_profit\":\"250.00\",\"inventory_turnover_rate\":\"0.6250\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":48,\"product_name\":\"Toyota corolla\",\"total_quantity\":\"2\",\"total_sales\":\"36000.00\",\"total_cost\":\"28000.00\",\"total_profit\":\"8000.00\",\"inventory_turnover_rate\":\"0.6667\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":49,\"product_name\":\"Tissot  watch\",\"total_quantity\":\"10\",\"total_sales\":\"4500.00\",\"total_cost\":\"3000.00\",\"total_profit\":\"1500.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"}]', 0.00, 0.00, 625.26, 0.16, 999.99, 0.00, 0.00, 495400.00, -637375.00, '2024-09-24 19:20:02', 1628175, 2604, 99, 99, -99);

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
(47, 25, 7, 46, 63, 15, 150.00, '2024-01-16 23:08:06', 'completed', 'paid', 'Samsung Galaxy', 'Goods', 'sold', 'uploads/productssamsung-galaxy-a10s.jpg'),
(48, 31, 7, 47, 64, 230, 25.00, '2024-02-16 23:58:29', 'completed', 'paid', 'Premium Beer', 'Goods', 'sold', 'uploads/productsbeer.jpg'),
(49, 29, 7, 48, 65, 40, 1500.00, '2024-03-16 23:59:49', 'completed', 'paid', 'Apple iPhone', 'Goods', 'sold', 'uploads/productsiphone-15-128gb.jpg'),
(50, 30, 7, 46, 65, 150, 45.00, '2024-04-17 00:01:24', 'completed', 'paid', 'Floral Dress', 'Goods', 'sold', 'uploads/productsfloral-pleated-weave-dress.jpg'),
(51, 27, 7, 49, 66, 500, 15.00, '2024-05-17 00:03:47', 'completed', 'paid', 'Bottled Water', 'Goods', 'sold', 'uploads/productswater.jpg'),
(52, 32, 7, 50, 63, 25, 100.00, '2024-06-17 00:05:50', 'completed', 'paid', 'Camera', 'Goods', 'sold', 'uploads/productscamera.jpg'),
(53, 33, 7, 51, 65, 2, 75000.00, '2024-07-17 00:07:59', 'completed', 'paid', 'Chevrolet AWD', 'Goods', 'sold', 'uploads/productsChevrolet.jpg'),
(54, 34, 7, 52, 67, 200, 22.00, '2024-08-17 00:09:19', 'completed', 'paid', 'Mary Kay', 'Goods', 'sold', 'uploads/productslipgloss.jpg'),
(55, 35, 7, 53, 68, 50, 1800.00, '2024-09-17 00:12:27', 'completed', 'paid', 'Necklace', 'Goods', 'sold', 'uploads/productsgoldnecklace.jpeg'),
(56, 36, 7, 54, 64, 150, 30.00, '2024-10-17 00:14:43', 'completed', 'paid', 'Sony Headphones', 'Goods', 'sold', 'uploads/productsheadphones.jpg'),
(57, 37, 7, 55, 63, 400, 55.00, '2024-11-17 00:15:59', 'completed', 'paid', 'Hike Bag', 'Goods', 'sold', 'uploads/productshike-bag.jpg'),
(58, 38, 7, 56, 65, 50, 85.00, '2024-12-17 00:17:18', 'completed', 'paid', 'Dior female shoes', 'Goods', 'sold', 'uploads/productslady-shoes.jpg'),
(59, 39, 7, 57, 65, 250, 10.00, '2024-01-17 00:20:16', 'completed', 'paid', 'Lip Stick', 'Goods', 'sold', 'uploads/productslipstick.jpg'),
(60, 40, 7, 58, 68, 100, 45.00, '2024-02-17 00:21:31', 'completed', 'paid', 'Make-up Kit', 'Goods', 'sold', 'uploads/productsmake-up.jpg'),
(61, 41, 7, 48, 66, 150, 35.00, '2024-03-17 00:23:27', 'completed', 'paid', 'Leather Shoes', 'Goods', 'sold', 'uploads/productsmen clothings.jpg'),
(62, 42, 7, 46, 66, 100, 15.00, '2024-04-17 00:24:43', 'completed', 'paid', 'Tea mugs', 'Goods', '', 'uploads/productsmugs.jpg'),
(63, 43, 7, 57, 68, 15, 50.00, '2024-05-17 00:25:56', 'completed', 'paid', 'Air Jordan', 'Goods', 'sold', 'uploads/productsnike-sneakers.jpg'),
(64, 44, 7, 48, 67, 100, 17.00, '2024-06-17 00:26:59', 'completed', 'paid', 'Perfume Spray', 'Goods', 'sold', 'uploads/productsperfume.jpg'),
(65, 45, 7, 47, 65, 40, 35.00, '2024-07-17 00:31:42', 'completed', 'paid', 'Rayban Sunglasses', 'Goods', 'sold', 'uploads/productsrayban.jpg'),
(66, 47, 7, 59, 63, 25, 75.00, '2024-08-17 00:39:08', 'completed', 'paid', 'Apple Speakers', 'Goods', 'sold', 'uploads/productsspeaker.jpg'),
(67, 48, 7, 60, 65, 2, 18000.00, '2024-09-17 00:40:10', 'completed', 'paid', 'Toyota corolla', 'Goods', 'sold', 'uploads/productstoyota-corolla-2024.jpg'),
(68, 49, 7, 61, 66, 10, 450.00, '2024-10-17 00:42:22', 'completed', 'paid', 'Tissot  watch', 'Goods', 'sold', 'uploads/productswristwatch.jpg');

--
-- Triggers `sales`
--
DELIMITER $$
CREATE TRIGGER `update_inventory` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    -- Update available stock in inventory 
    UPDATE inventory im
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
    WHERE sa.id = NEW.id;
    
    -- Update most_sold_product_id in sales_analytics if necessary
    UPDATE sales_analytics sa
    SET sa.most_sold_product_id = NEW.id
    WHERE sa.id = NEW.id
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
  `revenue_by_category` decimal(2,0) NOT NULL,
  `most_sold_product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_analytics`
--

INSERT INTO `sales_analytics` (`id`, `date`, `revenue`, `profit_margin`, `year_over_year_growth`, `cost_of_selling`, `inventory_turnover_rate`, `stock_to_sales_ratio`, `sell_through_rate`, `gross_margin_by_category`, `net_margin_by_category`, `gross_margin`, `net_margin`, `created_at`, `total_sales`, `total_quantity`, `total_profit`, `total_expenses`, `net_profit`, `revenue_by_category`, `most_sold_product_id`) VALUES
(171, '2024-09-17', 1350.00, 37.04, 0.00, 0.00, 25.00, 4.00, 999.99, 0.00, 0.00, 850.00, 500.00, '2024-09-17 00:51:03', 1350, 54, 99, 0, 0, 0, 0),
(172, '2024-09-17', 1350.00, 37.04, 0.00, 0.00, 25.00, 4.00, 999.99, 0.00, 0.00, 850.00, 500.00, '2024-09-17 18:21:44', 1350, 54, 99, 0, 0, 0, 0),
(173, '2024-09-17', 1350.00, 37.04, 0.00, 0.00, 25.00, 4.00, 999.99, 0.00, 0.00, 850.00, 500.00, '2024-09-17 18:47:37', 1350, 54, 99, 0, 0, 0, 0),
(174, '2024-09-17', 1350.00, 37.04, 0.00, 0.00, 25.00, 4.00, 999.99, 0.00, 0.00, 850.00, 500.00, '2024-09-17 18:48:36', 1350, 54, 99, 0, 0, 0, 0),
(175, '2024-09-17', 218765.00, 28.62, 0.00, 0.00, 508.76, 0.20, 999.99, 0.00, 0.00, 156165.00, 62600.00, '2024-09-23 18:14:13', 218765, 430, 99, 0, 0, 0, 0),
(176, '2024-09-17', 218765.00, 28.62, 0.00, 0.00, 508.76, 0.20, 999.99, 0.00, 0.00, 156165.00, 62600.00, '2024-09-24 18:54:59', 218765, 430, 99, 0, 0, 0, 0),
(177, '2024-09-17', 1628175.00, 30.43, 0.00, 0.00, 625.26, 0.16, 999.99, 0.00, 0.00, 1132775.00, 495400.00, '2024-09-24 19:19:36', 1628175, 2604, 99, 0, 0, 0, 0);

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
(63, 'chris', '', '', 'sales', '2024-09-16 23:08:06'),
(64, 'stacey', '', '', 'sales', '2024-09-16 23:58:29'),
(65, 'mark', '', '', 'sales', '2024-09-16 23:59:49'),
(66, 'kim', '', '', 'sales', '2024-09-17 00:03:47'),
(67, 'james', '', '', 'sales', '2024-09-17 00:09:19'),
(68, 'john', '', '', 'sales', '2024-09-17 00:12:27');

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
(4, 'Gordons', 'gordons@gmail.com', '0101674356', 'Accra', '2024-09-17 00:48:46', 'Premium Beer', 100, 'delivered');

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
(7, 'megastores', 'olphemie@hotmail.com', '$2y$10$6Xat/Bu6Vh7RJ/0P/OMi7e6Gdw9GbrE8F.DiRYZnwZu4eslLWigja', 0, 'sales', '2024-09-16 21:57:51', 'mega1234', '', '', '');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sales_analytics`
--
ALTER TABLE `sales_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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

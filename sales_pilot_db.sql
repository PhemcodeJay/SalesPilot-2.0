-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: salespilot
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activation_codes`
--

DROP TABLE IF EXISTS `activation_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activation_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activation_code` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activation_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activation_codes`
--

LOCK TABLES `activation_codes` WRITE;
/*!40000 ALTER TABLE `activation_codes` DISABLE KEYS */;
INSERT INTO `activation_codes` VALUES (2,2,'66a214ed6b9ab','2024-07-26 08:03:41','2024-07-25 09:03:41');
/*!40000 ALTER TABLE `activation_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (56,'Electronics',NULL,'2024-07-25 15:32:53'),(59,'Jewellry',NULL,'2024-07-25 17:19:18'),(60,'Apparel',NULL,'2024-07-26 05:49:26'),(61,'Auto',NULL,'2024-07-26 05:54:21');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_location` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (5,'chris','chris@gmail.com','0111826871','Nairobi','2024-07-26 06:39:01'),(6,'carol','carol@gmail.com','0111826845','Abuja','2024-07-26 07:01:27'),(7,'jim','jim@gmail.com','0111826456','Texas','2024-07-26 07:02:25'),(8,'dave','','','','2024-07-26 07:03:52'),(9,'joe','','','','2024-07-26 07:05:43'),(10,'jerome','','','','2024-07-26 07:07:04');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'Rent',1000.00,'2024-07-26 21:00:00','john'),(2,'salary for june',10000.00,'2024-07-26 21:00:00','steve'),(3,'shop repairs',1500.00,'2024-07-26 21:00:00','kyle');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_qty` int(11) DEFAULT NULL,
  `supply_qty` int(11) DEFAULT NULL,
  `available_stock` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty` - `sales_qty`) STORED,
  `inventory_qty` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty`) STORED,
  `product_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reset_code` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (1,2,'e6a794bd7d8de2c2ea7e28ac3bb47977dd8c256beed05d6ba54f8675855ea691','2024-07-25 10:04:22','2024-07-25 10:04:22');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `profit` decimal(10,2) GENERATED ALWAYS AS (`price` - `cost`) STORED,
  PRIMARY KEY (`id`),
  KEY `products_ibfk_1` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `name`, `description`, `price`, `cost`, `category_id`, `created_at`, `stock_qty`, `supply_qty`, `image_path`, `product_type`, `staff_name`, `category`) VALUES (11,'Samsung Galaxy','Samsung Galaxy A10S BLACK',6000.00,3000.00,56,'2024-07-25 17:13:11',10,15,'uploads/samsung-galaxy-a10s.jpg','Goods','Jude','Electronics'),(12,'Necklace','Gold Necklace Fashion Jewellry',450.00,200.00,59,'2024-07-25 17:19:18',12,15,'uploads/goldnecklace.jpeg','Goods','kim','Jewellry'),(13,'Iphone 15 128GB','Apple Iphone 15 128GB White',1500.00,1200.00,56,'2024-07-26 05:37:28',20,15,'uploads/iphone-15-128gb.jpg','Goods','john','Electronics'),(14,'Nike Sneakers','Jordan Sneakers White &amp; Black Size 40',150.00,100.00,60,'2024-07-26 05:49:27',20,12,'uploads/nike-sneakers.jpg','Goods','james','Apparel'),(15,'Floral-Pleated-Weave-Dress','Floral-Pleated-Weave-Dress',35.00,20.00,60,'2024-07-26 05:52:07',30,15,'uploads/floral-pleated-weave-dress.jpg','Goods','joy','Apparel'),(16,'Toyota-Corolla','Toyota-Corolla-2024-White',15.00,10.00,61,'2024-07-26 05:54:21',3,2,'uploads/toyota-corolla-2024.jpg','Goods','chris','Auto');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `net_profit` decimal(2,0) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,'2024-07-30',20315.00,42.85,'[{\"product_id\":11,\"revenue\":\"12000.00\"},{\"product_id\":12,\"revenue\":\"2250.00\"},{\"product_id\":13,\"revenue\":\"4500.00\"},{\"product_id\":14,\"revenue\":\"1200.00\"},{\"product_id\":15,\"revenue\":\"350.00\"},{\"product_id\":16,\"revenue\":\"15.00\"}]',0.00,11610.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 16:29:56',20315,29,99,99,99);
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `customer_id` (`customer_id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  CONSTRAINT `sales_ibfk_4` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (16,11,2,5,43,2,6000.00,'2024-07-26 06:39:01','completed','paid','Samsung Galaxy','Goods','sold','uploads/samsung-galaxy-a10s.jpg'),(17,12,2,6,48,5,450.00,'2024-07-26 07:01:27','completed','paid','Necklace','Goods','sold','uploads/goldnecklace.jpeg'),(18,14,2,7,49,8,150.00,'2024-07-26 07:02:25','completed','paid','Nike Sneakers','Goods','sold','uploads/nike-sneakers.jpg'),(19,15,2,8,50,10,35.00,'2024-07-26 07:03:52','completed','paid','Floral-Pleated-Weave-Dress','Goods','sold','uploads/floral-pleated-weave-dress.jpg'),(20,13,2,9,51,3,1500.00,'2024-07-26 07:05:43','completed','paid','Iphone 15 128GB','Goods','sold','uploads/iphone-15-128gb.jpg'),(21,16,2,10,52,1,15000.00,'2024-07-26 07:07:04','completed','paid','Toyota-Corolla','Goods','sold','uploads/toyota-corolla-2024.jpg');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER update_inventory_metrics
AFTER INSERT ON sales
FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sales_analytics`
--

DROP TABLE IF EXISTS `sales_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `total_profit` decimal(10,2) NOT NULL,
  `total_expenses` decimal(10,2) NOT NULL,
  `net_profit` decimal(10,2) NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `profit_margin` decimal(5,2) NOT NULL,
  `revenue_by_product` decimal(10,2) NOT NULL,
  `year_over_year_growth` decimal(5,2) NOT NULL,
  `cost_of_selling` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `total_sales` decimal(10,2) NOT NULL,
  `available_stock` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_analytics`
--

LOCK TABLES `sales_analytics` WRITE;
/*!40000 ALTER TABLE `sales_analytics` DISABLE KEYS */;
INSERT INTO `sales_analytics` VALUES (112,11,2,6000.00,6000.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',12000.00,0,'Samsung Galaxy'),(113,12,5,1250.00,1000.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',2250.00,0,'Necklace'),(114,13,3,900.00,3600.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',4500.00,0,'Iphone 15 128GB'),(115,14,8,400.00,800.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',1200.00,0,'Nike Sneakers'),(116,15,10,150.00,200.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',350.00,0,'Floral-Pleated-Weave-Dress'),(117,16,1,5.00,10.00,0.00,0.00,0.00,0.00,0.00,0.00,'2024-07-30 20:59:10',15.00,0,'Toyota-Corolla');
/*!40000 ALTER TABLE `sales_analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staffs`
--

DROP TABLE IF EXISTS `staffs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staffs` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` varchar(100) NOT NULL,
  `staff_email` varchar(100) NOT NULL,
  `staff_phone` varchar(20) NOT NULL,
  `position` enum('manager','sales') NOT NULL DEFAULT 'sales',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staffs`
--

LOCK TABLES `staffs` WRITE;
/*!40000 ALTER TABLE `staffs` DISABLE KEYS */;
INSERT INTO `staffs` VALUES (43,'james','james@gmail.com','0111826234','','2024-07-26 06:39:01'),(48,'john','john@gmail.com','0111826678','','2024-07-26 07:01:27'),(49,'carrey','carrey@gmail.com','0112346872','','2024-07-26 07:02:25'),(50,'peter','','','sales','2024-07-26 07:03:52'),(51,'jean','','','sales','2024-07-26 07:05:43'),(52,'king','','','sales','2024-07-26 07:07:04');
/*!40000 ALTER TABLE `staffs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_email` varchar(255) NOT NULL,
  `supplier_phone` varchar(20) NOT NULL,
  `supplier_location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) NOT NULL,
  `supply_qty` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Demarck Suppliers','','','','2024-07-29 17:34:11','Iphone 15 Pro Max',10,'good condition'),(2,'Goons Suppliers','','','','2024-07-29 17:41:46','Samsung Galaxy',15,'good condition');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `role` enum('admin','sales','inventory') DEFAULT 'sales',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmpassword` varchar(255) NOT NULL,
  `user_image` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'megastores','megastores@gmail.com','$2y$10$C0Afoh20GiefSBzZu.gRZuOXec5WnLLEPSFFD1M1jpX48EvEN0UYu',NULL,NULL,'2024-07-25 09:03:41','mega1234','uploads/.trashed-1718858585-Favicon.jpg','0111826872','Texas');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'salespilot'
--

--
-- Dumping routines for database 'salespilot'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-30 21:07:49

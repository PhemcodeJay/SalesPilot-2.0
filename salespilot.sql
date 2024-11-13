-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: dbs13455438
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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activation_codes`
--

LOCK TABLES `activation_codes` WRITE;
/*!40000 ALTER TABLE `activation_codes` DISABLE KEYS */;
INSERT INTO `activation_codes` VALUES (2,2,'6726e9481e942','2024-11-04 04:08:56','2024-11-03 03:08:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (8,'Devices',NULL,'2024-11-03 03:13:26'),(9,'Foods',NULL,'2024-11-03 19:35:05'),(10,'Auto',NULL,'2024-11-03 19:38:49'),(11,'Fashion',NULL,'2024-11-03 19:40:54'),(12,'Cosmetic',NULL,'2024-11-03 19:46:28');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hvijolm,nm','2024-11-10 00:07:21','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fdguiuopo[','2024-11-10 00:10:40','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fxdhgkjl','2024-11-10 00:12:10','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hbjklk','2024-11-10 00:15:57','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','ytuioklmnbhvcfxdzsaw','2024-11-10 00:19:01','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','vcghvkjlmknjbhagx','2024-11-10 00:22:09','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fsdjklbmnbvf','2024-11-10 00:24:20','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fsdjklbmnbvf','2024-11-10 00:24:23','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hkjbl;lj','2024-11-10 00:24:59','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','vhuiouxaihscj','2024-11-10 00:43:10','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hbjixahucs','2024-11-10 00:44:55','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hvijolm,nm','2024-11-10 00:07:21','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fdguiuopo[','2024-11-10 00:10:40','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fxdhgkjl','2024-11-10 00:12:10','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hbjklk','2024-11-10 00:15:57','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','ytuioklmnbhvcfxdzsaw','2024-11-10 00:19:01','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','vcghvkjlmknjbhagx','2024-11-10 00:22:09','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fsdjklbmnbvf','2024-11-10 00:24:20','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','fsdjklbmnbvf','2024-11-10 00:24:23','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hkjbl;lj','2024-11-10 00:24:59','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','vhuiouxaihscj','2024-11-10 00:43:10','0111826872'),(0,'OLUWAFEMI JEGEDE','olphemie@gmail.com','hbjixahucs','2024-11-10 00:44:55','0111826872');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (15,'james','james@gmail.com','876543','Madrid','2024-11-03 03:27:42'),(16,'sam','','','','2024-11-03 19:49:49'),(17,'neema','','','','2024-11-03 19:50:49'),(18,'tom','','','','2024-11-03 19:52:34');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (4,'Rent',500.00,'2024-11-02 04:00:00','Tom'),(5,'Taxes and Levy',800.00,'2024-10-03 04:00:00','karim'),(6,'Renovation',800.00,'2024-09-06 04:00:00','jerry'),(7,'Marketing',1000.00,'2024-07-19 04:00:00','john');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sales_qty` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_qty` int(11) DEFAULT NULL,
  `supply_qty` int(11) DEFAULT NULL,
  `available_stock` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty` - `sales_qty`) STORED,
  `inventory_qty` int(11) GENERATED ALWAYS AS (`stock_qty` + `supply_qty`) STORED,
  `product_name` varchar(255) NOT NULL,
  PRIMARY KEY (`inventory_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` (`inventory_id`, `product_id`, `sales_qty`, `last_updated`, `stock_qty`, `supply_qty`, `product_name`) VALUES (5,11,20,'2024-11-03 03:33:41',15,12,'Samsung Galaxy'),(6,18,20,'2024-11-03 20:02:26',25,50,'Makeup kit'),(7,17,40,'2024-11-03 20:02:26',40,100,'Beauty Soap'),(8,16,20,'2024-11-03 20:02:26',49,60,'Nike Sneakers'),(9,15,15,'2024-11-03 20:02:26',40,50,'Floral Dress'),(10,14,2,'2024-11-03 20:02:26',5,10,'Chevrolet AWD'),(11,13,5,'2024-11-03 20:02:26',25,10,'Sony Camera'),(12,12,20,'2024-11-03 20:02:26',50,20,'Pilsner Beer'),(13,19,10,'2024-11-04 08:58:16',20,50,'Apple IPhone 15');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `invoice_items_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (`qty` * `price`) STORED,
  PRIMARY KEY (`invoice_items_id`),
  KEY `invoice_id_idx` (`invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` (`invoice_items_id`, `invoice_id`, `item_name`, `qty`, `price`) VALUES (23,12,'laptop',2,200.00),(24,12,'bag',2,120.00);
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `total_amount` decimal(10,2) GENERATED ALWAYS AS (`subtotal` - `subtotal` * (`discount` / 100)) STORED,
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `unique_invoice_item` (`invoice_number`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `customer_name`, `invoice_description`, `order_date`, `order_status`, `order_id`, `delivery_address`, `mode_of_payment`, `due_date`, `subtotal`, `discount`) VALUES (12,'567','james','delivery','2024-11-01','Paid','4567','112 freway oho','mpesa','2024-11-05',640.00,2.00);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_access`
--

DROP TABLE IF EXISTS `page_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `required_access_level` enum('trial','starter','business','enterprise') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_access`
--

LOCK TABLES `page_access` WRITE;
/*!40000 ALTER TABLE `page_access` DISABLE KEYS */;
INSERT INTO `page_access` VALUES (1,'dashboard.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(2,'loginpage.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(3,'logout.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(4,'index.html','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(5,'reg-success.html','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(6,'sign-up.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(7,'user-confirm.html','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(8,'passwordreset.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(9,'recoverpwd.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(10,'subscription.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(11,'pay.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(12,'user-profile-edit.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(13,'page-add-expense.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(14,'page-add-product.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(15,'page-list-inventory.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(16,'page-list-sale.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(17,'page-add-sale.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(18,'page-list-category.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(19,'page-list-product.php','trial','2024-11-12 18:01:55','2024-11-12 18:01:55'),(20,'analytics-report.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(21,'analytics.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(22,'inventory-metrics.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(23,'sales-metrics.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(24,'analytics-report.php','enterprise','2024-11-12 18:01:55','2024-11-12 18:01:55'),(25,'inventory-metrics.php','enterprise','2024-11-12 18:01:55','2024-11-12 18:01:55'),(26,'sales-metrics.php','enterprise','2024-11-12 18:01:55','2024-11-12 18:01:55'),(27,'analytics.php','enterprise','2024-11-12 18:01:55','2024-11-12 18:01:55'),(28,'invoice-form.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(29,'edit_invoice.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(30,'subscription.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(31,'pay.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(32,'pages-invoice.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(33,'page-add-customers.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(34,'page-add-expense.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(35,'page-add-product.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(36,'page-add-sale.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(37,'page-add-staffs.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(38,'page-add-supplier.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(39,'page-list-category.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(40,'page-list-customers.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(41,'page-list-expense.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(42,'page-list-inventory.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(43,'page-list-product.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(44,'page-list-sale.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(45,'page-list-staffs.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55'),(46,'page-list-suppliers.php','business','2024-11-12 18:01:55','2024-11-12 18:01:55');
/*!40000 ALTER TABLE `page_access` ENABLE KEYS */;
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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `payment_method` enum('paypal','binance','mpesa','naira') NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subscription_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `subscription_id` (`subscription_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
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
  KEY `products_ibfk_1` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `name`, `description`, `price`, `cost`, `category_id`, `created_at`, `stock_qty`, `supply_qty`, `image_path`, `product_type`, `staff_name`, `category`) VALUES (11,'Samsung Galaxy','A10s',150.00,100.00,8,'2024-11-03 03:13:26',15,12,'uploads/products/samsung-galaxy-a10s.jpg','Goods','joe','Devices'),(12,'Pilsner Beer','Premium Beer',25.00,20.00,9,'2024-11-03 19:35:05',50,20,'uploads/products/beer.jpg','Goods','kim','Food'),(13,'Sony Camera','AI Digital Camera',75.00,50.00,8,'2024-11-03 19:36:50',25,10,'uploads/products/camera.jpg','Goods','mike','Devices'),(14,'Chevrolet AWD','Chevrolet AWD 2024',18000.00,15000.00,10,'2024-11-03 19:38:49',5,10,'uploads/products/Chevrolet.jpg','Goods','james','Auto'),(15,'Floral Dress','floral-pleated-weave-dress',55.00,40.00,11,'2024-11-03 19:40:54',40,50,'uploads/products/floral-pleated-weave-dress.jpg','Goods','joe','Fashon'),(16,'Nike Sneakers','Nike Air Jordan',95.00,70.00,11,'2024-11-03 19:42:47',49,60,'uploads/products/nike-sneakers.jpg','Goods','mike','Fashion'),(17,'Beauty Soap','Skincare Soap',20.00,10.00,12,'2024-11-03 19:46:28',40,100,'uploads/products/soap.jpg','Goods','james','Cosmetic'),(18,'Makeup kit','Mary Kay Makeup kit',25.00,20.00,12,'2024-11-03 19:48:20',25,50,'uploads/products/make-up.jpg','Goods','kim','Cosmetic'),(19,'Apple IPhone 15','Apple IPhone 15 128GB',1500.00,900.00,8,'2024-11-03 20:19:50',20,50,'uploads/products/iphone-15-128gb.jpg','Goods','james','Devices');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `reports_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`reports_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (2,'2024-11-02',3000.00,33.33,'[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"20\",\"total_sales\":\"3000.00\",\"total_cost\":\"2000.00\",\"total_profit\":\"1000.00\",\"inventory_turnover_rate\":\"1.3333\",\"sell_through_rate\":\"100.0000\"}]',0.00,0.00,150.00,0.67,999.99,0.00,0.00,1000.00,-1000.00,'2024-11-03 03:42:08',3000,20,99,99,-99),(3,'2024-11-03',43900.00,19.25,'[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"20\",\"total_sales\":\"3000.00\",\"total_cost\":\"2000.00\",\"total_profit\":\"1000.00\",\"inventory_turnover_rate\":\"1.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Pilsner Beer\",\"total_quantity\":\"20\",\"total_sales\":\"500.00\",\"total_cost\":\"400.00\",\"total_profit\":\"100.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Sony Camera\",\"total_quantity\":\"5\",\"total_sales\":\"375.00\",\"total_cost\":\"250.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Chevrolet AWD\",\"total_quantity\":\"2\",\"total_sales\":\"36000.00\",\"total_cost\":\"30000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral Dress\",\"total_quantity\":\"15\",\"total_sales\":\"825.00\",\"total_cost\":\"600.00\",\"total_profit\":\"225.00\",\"inventory_turnover_rate\":\"0.3750\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"20\",\"total_sales\":\"1900.00\",\"total_cost\":\"1400.00\",\"total_profit\":\"500.00\",\"inventory_turnover_rate\":\"0.4082\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":17,\"product_name\":\"Beauty Soap\",\"total_quantity\":\"40\",\"total_sales\":\"800.00\",\"total_cost\":\"400.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":18,\"product_name\":\"Makeup kit\",\"total_quantity\":\"20\",\"total_sales\":\"500.00\",\"total_cost\":\"400.00\",\"total_profit\":\"100.00\",\"inventory_turnover_rate\":\"0.8000\",\"sell_through_rate\":\"100.0000\"}]',0.00,0.00,309.15,0.32,999.99,0.00,0.00,8450.00,-27000.00,'2024-11-03 09:55:06',43900,142,99,99,-99),(4,'2024-11-09',58900.00,24.53,'[{\"product_id\":11,\"product_name\":\"Samsung Galaxy\",\"total_quantity\":\"20\",\"total_sales\":\"3000.00\",\"total_cost\":\"2000.00\",\"total_profit\":\"1000.00\",\"inventory_turnover_rate\":\"1.3333\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":12,\"product_name\":\"Pilsner Beer\",\"total_quantity\":\"20\",\"total_sales\":\"500.00\",\"total_cost\":\"400.00\",\"total_profit\":\"100.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":13,\"product_name\":\"Sony Camera\",\"total_quantity\":\"5\",\"total_sales\":\"375.00\",\"total_cost\":\"250.00\",\"total_profit\":\"125.00\",\"inventory_turnover_rate\":\"0.2000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":14,\"product_name\":\"Chevrolet AWD\",\"total_quantity\":\"2\",\"total_sales\":\"36000.00\",\"total_cost\":\"30000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.4000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":15,\"product_name\":\"Floral Dress\",\"total_quantity\":\"15\",\"total_sales\":\"825.00\",\"total_cost\":\"600.00\",\"total_profit\":\"225.00\",\"inventory_turnover_rate\":\"0.3750\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":16,\"product_name\":\"Nike Sneakers\",\"total_quantity\":\"20\",\"total_sales\":\"1900.00\",\"total_cost\":\"1400.00\",\"total_profit\":\"500.00\",\"inventory_turnover_rate\":\"0.4082\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":17,\"product_name\":\"Beauty Soap\",\"total_quantity\":\"40\",\"total_sales\":\"800.00\",\"total_cost\":\"400.00\",\"total_profit\":\"400.00\",\"inventory_turnover_rate\":\"1.0000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":18,\"product_name\":\"Makeup kit\",\"total_quantity\":\"20\",\"total_sales\":\"500.00\",\"total_cost\":\"400.00\",\"total_profit\":\"100.00\",\"inventory_turnover_rate\":\"0.8000\",\"sell_through_rate\":\"100.0000\"},{\"product_id\":19,\"product_name\":\"Apple IPhone 15\",\"total_quantity\":\"10\",\"total_sales\":\"15000.00\",\"total_cost\":\"9000.00\",\"total_profit\":\"6000.00\",\"inventory_turnover_rate\":\"0.5000\",\"sell_through_rate\":\"100.0000\"}]',0.00,0.00,387.50,0.26,999.99,0.00,0.00,14450.00,-30000.00,'2024-11-09 08:59:27',58900,152,99,99,-99);
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`sales_qty` * `sales_price`) VIRTUAL,
  PRIMARY KEY (`sales_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `customer_id` (`customer_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` (`sales_id`, `product_id`, `user_id`, `customer_id`, `staff_id`, `sales_qty`, `sale_date`, `sale_status`, `payment_status`, `name`, `product_type`, `sale_note`, `sales_price`) VALUES (10,11,2,15,13,20,'2024-01-03 04:32:21','completed','paid','Samsung Galaxy','Goods','sold',150.00),(11,18,2,16,13,20,'2024-02-03 19:49:49','completed','paid','Makeup kit','Goods','sold',25.00),(12,17,2,17,14,40,'2024-03-13 18:50:49','completed','paid','Beauty Soap','Goods','sold',20.00),(13,16,2,18,15,20,'2024-04-23 18:52:34','completed','paid','Nike Sneakers','Goods','sold',95.00),(14,15,2,15,16,15,'2024-05-03 18:54:15','completed','paid','Floral Dress','Goods','sold',55.00),(15,14,2,16,13,2,'2024-06-23 18:56:16','completed','paid','Chevrolet AWD','Goods','sold',18000.00),(16,13,2,17,17,5,'2024-07-09 18:57:31','completed','paid','Sony Camera','Goods','SOLD',75.00),(17,12,2,18,15,20,'2024-11-03 19:58:32','completed','paid','Pilsner Beer','Goods','sold',25.00),(18,19,2,15,13,10,'2024-11-03 20:21:11','completed','paid','Apple IPhone 15','Goods','sold',1500.00);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_analytics`
--

DROP TABLE IF EXISTS `sales_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `most_sold_product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_analytics`
--

LOCK TABLES `sales_analytics` WRITE;
/*!40000 ALTER TABLE `sales_analytics` DISABLE KEYS */;
INSERT INTO `sales_analytics` VALUES (4,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 03:41:58',3000,20,99,0,0,0,0),(5,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 09:54:59',3000,20,99,0,0,0,0),(6,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 19:15:03',3000,20,99,0,0,0,0),(7,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 19:15:34',3000,20,99,0,0,0,0),(8,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 19:15:38',3000,20,99,0,0,0,0),(9,'2024-11-02',3000.00,33.33,0.00,0.00,150.00,0.67,999.99,0.00,0.00,2000.00,1000.00,'2024-11-03 19:17:23',3000,20,99,0,0,0,0),(10,'2024-11-02',43900.00,19.25,0.00,0.00,309.15,0.32,999.99,0.00,0.00,35450.00,8450.00,'2024-11-03 20:03:18',43900,142,99,0,0,0,0),(11,'2024-11-02',58900.00,24.53,0.00,0.00,387.50,0.26,999.99,0.00,0.00,44450.00,14450.00,'2024-11-04 07:07:40',58900,152,99,0,0,0,0),(12,'2024-11-02',58900.00,24.53,0.00,0.00,387.50,0.26,999.99,0.00,0.00,44450.00,14450.00,'2024-11-04 10:28:01',58900,152,99,0,0,0,0),(13,'2024-11-02',58900.00,24.53,0.00,0.00,387.50,0.26,999.99,0.00,0.00,44450.00,14450.00,'2024-11-10 09:42:49',58900,152,99,0,0,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staffs`
--

LOCK TABLES `staffs` WRITE;
/*!40000 ALTER TABLE `staffs` DISABLE KEYS */;
INSERT INTO `staffs` VALUES (13,'joe','joe@gmail.com','123456876','sales','2024-11-03 03:27:42'),(14,'jack','','','sales','2024-11-03 19:50:49'),(15,'chris','','','sales','2024-11-03 19:52:34'),(16,'mike','','','sales','2024-11-03 19:54:15'),(17,'james','','','sales','2024-11-03 19:57:31');
/*!40000 ALTER TABLE `staffs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscription_plan` enum('starter','business','enterprise') NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` timestamp NOT NULL DEFAULT '2030-12-31 20:59:59',
  `status` enum('active','expired','canceled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_free_trial_used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_email` varchar(255) NOT NULL,
  `supplier_phone` varchar(20) NOT NULL,
  `supplier_location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) NOT NULL,
  `supply_qty` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (2,'skystore','skystores@gmail.com','876544567','London','2024-11-03 03:37:04','Samsung Galaxy',10,'');
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
  `google_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Megastores','olphemie@hotmail.com','$2y$10$X8uPrpPbouNMJNqhZvfKMOk.cxbTO0Cmqm2UIj9Y2r/f3wEyXA2sm',0,'sales','2024-11-03 03:08:56','mega1234','uploads/user/1730604250_1730520358_1726523112_20230712_130458.jpg','','Texas',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-12 21:14:20

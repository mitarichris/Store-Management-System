-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: store
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `store`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `store`;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_user_id_foreign` (`user_id`),
  CONSTRAINT `accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_created_by_foreign` (`created_by`),
  CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Electronics','electronics','Electronic gadgets and devices',NULL,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(2,'Clothing','clothing','Apparel and fashion items',NULL,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(3,'Food & Beverages','food-beverages','Consumable goods',NULL,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(4,'Beauty & Health','beauty-health','Personal care products',NULL,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2024_01_01_000001_create_users_table',1),(4,'2024_01_01_000002_create_categories_table',1),(5,'2024_01_01_000003_create_products_table',1),(6,'2024_01_01_000004_create_sales_tables',1),(7,'2024_01_01_000005_create_accounting_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `category_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_created_by_foreign` (`created_by`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Samsung Galaxy A54','PRD-6A896835ABD70',NULL,350000.00,280000.00,25,5,NULL,1,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(2,'Wireless Earbuds Pro','PRD-6A896835AE120',NULL,45000.00,30000.00,50,10,NULL,1,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(3,'USB-C Charging Cable','PRD-6A896835AEF76',NULL,5000.00,2500.00,200,30,NULL,1,1,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(4,'Men\'s Classic T-Shirt','PRD-6A896835B00E1',NULL,8000.00,4000.00,100,20,NULL,1,2,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(5,'Women\'s Denim Jeans','PRD-6A896835B0E89',NULL,25000.00,15000.00,60,10,NULL,1,2,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(6,'Running Shoes','PRD-6A896835B19E3',NULL,35000.00,22000.00,40,8,NULL,1,2,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(7,'Mineral Water 500ml','PRD-6A896835B24FA',NULL,500.00,200.00,500,50,NULL,1,3,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(8,'Energy Drink 250ml','PRD-6A896835B3476',NULL,1500.00,800.00,8,20,NULL,1,3,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(9,'Face Moisturizer SPF30','PRD-6A896835B472A',NULL,12000.00,7000.00,3,10,NULL,1,4,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL),(10,'Shampoo 400ml','PRD-6A896835B5851',NULL,6500.00,3500.00,80,15,NULL,1,4,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1,1,7,3,500.00,1500.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(2,2,6,2,35000.00,70000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(3,3,6,1,35000.00,35000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(4,3,10,3,6500.00,19500.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(5,3,5,1,25000.00,25000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(6,4,5,2,25000.00,50000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(7,4,3,2,5000.00,10000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(8,5,8,2,1500.00,3000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(9,5,5,3,25000.00,75000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(10,5,7,3,500.00,1500.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(11,6,6,1,35000.00,35000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(12,6,9,1,12000.00,12000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(13,7,1,1,350000.00,350000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(14,7,10,1,6500.00,6500.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(15,7,4,2,8000.00,16000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(16,8,1,3,350000.00,1050000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(17,9,3,2,5000.00,10000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(18,9,2,2,45000.00,90000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(19,10,4,3,8000.00,24000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(20,11,10,1,6500.00,6500.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(21,12,10,2,6500.00,13000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(22,13,5,1,25000.00,25000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(23,13,10,2,6500.00,13000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(24,13,1,1,350000.00,350000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(25,14,4,1,8000.00,8000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(26,14,5,3,25000.00,75000.00,'2026-08-22 07:13:25','2026-08-22 07:13:25'),(27,15,7,2,500.00,1000.00,'2026-08-22 07:13:26','2026-08-22 07:13:26'),(28,15,4,2,8000.00,16000.00,'2026-08-22 07:13:26','2026-08-22 07:13:26'),(29,15,5,3,25000.00,75000.00,'2026-08-22 07:13:26','2026-08-22 07:13:26');
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_number` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mobile_money','credit') NOT NULL DEFAULT 'cash',
  `status` enum('completed','pending','cancelled','refunded') NOT NULL DEFAULT 'completed',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `seller_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  KEY `sales_seller_id_foreign` (`seller_id`),
  CONSTRAINT `sales_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'SAL-000001',1500.00,75.00,0.00,1425.00,'cash','completed','Customer 1',NULL,NULL,3,'2026-07-30 07:13:25','2026-08-22 07:13:25',NULL),(2,'SAL-000002',70000.00,0.00,0.00,70000.00,'card','completed','Customer 2',NULL,NULL,4,'2026-08-16 07:13:25','2026-08-22 07:13:25',NULL),(3,'SAL-000003',79500.00,0.00,0.00,79500.00,'card','completed','Customer 3',NULL,NULL,3,'2026-07-30 07:13:25','2026-08-22 07:13:25',NULL),(4,'SAL-000004',60000.00,0.00,0.00,60000.00,'card','completed','Customer 4',NULL,NULL,4,'2026-08-14 07:13:25','2026-08-22 07:13:25',NULL),(5,'SAL-000005',79500.00,0.00,0.00,79500.00,'card','completed','Customer 5',NULL,NULL,3,'2026-08-05 07:13:25','2026-08-22 07:13:25',NULL),(6,'SAL-000006',47000.00,2350.00,0.00,44650.00,'cash','completed','Customer 6',NULL,NULL,4,'2026-07-25 07:13:25','2026-08-22 07:13:25',NULL),(7,'SAL-000007',372500.00,0.00,0.00,372500.00,'mobile_money','completed','Customer 7',NULL,NULL,3,'2026-08-11 07:13:25','2026-08-22 07:13:25',NULL),(8,'SAL-000008',1050000.00,0.00,0.00,1050000.00,'mobile_money','completed','Customer 8',NULL,NULL,4,'2026-08-05 07:13:25','2026-08-22 07:13:25',NULL),(9,'SAL-000009',100000.00,0.00,0.00,100000.00,'card','completed','Customer 9',NULL,NULL,3,'2026-08-15 07:13:25','2026-08-22 07:13:25',NULL),(10,'SAL-000010',24000.00,0.00,0.00,24000.00,'mobile_money','completed','Customer 10',NULL,NULL,4,'2026-07-24 07:13:25','2026-08-22 07:13:25',NULL),(11,'SAL-000011',6500.00,325.00,0.00,6175.00,'card','completed','Customer 11',NULL,NULL,3,'2026-08-01 07:13:25','2026-08-22 07:13:25',NULL),(12,'SAL-000012',13000.00,0.00,0.00,13000.00,'mobile_money','completed','Customer 12',NULL,NULL,4,'2026-08-20 07:13:25','2026-08-22 07:13:25',NULL),(13,'SAL-000013',388000.00,0.00,0.00,388000.00,'cash','completed','Customer 13',NULL,NULL,3,'2026-08-20 07:13:25','2026-08-22 07:13:25',NULL),(14,'SAL-000014',83000.00,0.00,0.00,83000.00,'mobile_money','completed','Customer 14',NULL,NULL,4,'2026-08-15 07:13:25','2026-08-22 07:13:25',NULL),(15,'SAL-000015',92000.00,0.00,0.00,92000.00,'cash','completed','Customer 15',NULL,NULL,3,'2026-08-11 07:13:26','2026-08-22 07:13:26',NULL);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('opening_balance','inventory_purchase','sale_revenue') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_sale_id_foreign` (`sale_id`),
  KEY `transactions_product_id_foreign` (`product_id`),
  CONSTRAINT `transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','shop_owner','seller') NOT NULL DEFAULT 'seller',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_created_by_foreign` (`created_by`),
  CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@store.com','$2y$12$CWdgSkzQscHK1ZOfQqHUkOWEnnWI5Y0oC1f1Xr4Mvu6a9n5KSbEE6','super_admin',1,'+250788000001',NULL,NULL,'2026-08-22 07:13:22','2026-08-22 07:13:22',NULL),(2,'Shop Owner','owner@store.com','$2y$12$hSvtk0nsP2Wx1uNIza2PLuKHi29oM3H7su.YGB94LZW6BeaTaApl6','shop_owner',1,'+250788000002',NULL,1,'2026-08-22 07:13:23','2026-08-22 07:13:23',NULL),(3,'John Seller','seller1@store.com','$2y$12$4dCLxFgMKinsAzXDuypNju/GwEFqpqpqywexfBzGfNn9mEmX.njSu','seller',1,'+250788000003',NULL,2,'2026-08-22 07:13:24','2026-08-22 07:13:24',NULL),(4,'Jane Doe','seller2@store.com','$2y$12$.dWtv5cupcC7nvTouEG3xuCH9040DTOExXvY.IG9.cS93cotJl1N6','seller',1,'+250788000004',NULL,2,'2026-08-22 07:13:25','2026-08-22 07:13:25',NULL);
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

-- Dump completed on 2026-08-22 11:17:13

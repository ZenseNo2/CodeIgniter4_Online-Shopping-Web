-- Database ShopEase (CodeIgniter 4) — data contoh untuk praktikum
-- Import lewat phpMyAdmin: buat/pilih database lalu tab Import, atau jalankan seluruh file ini.

CREATE DATABASE IF NOT EXISTS `db_shopease` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_shopease`;


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
DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Rumah',
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_foreign` (`user_id`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES
(1,3,'Rumah','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih','Surabaya','60111',1,'2026-11-25 12:01:17','2026-11-25 12:01:17'),
(2,4,'Kos','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22','Sidoarjo','61219',1,'2026-11-25 12:01:17','2026-11-25 12:01:17'),
(3,5,'Rumah','Rina Kartika','081234567803','Jl. Contoh Baru No. 5','Malang','65145',1,'2026-11-25 12:01:17','2026-11-25 12:01:17');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES
(1,1,1,1,'2026-11-25 12:01:17','2026-11-25 12:01:17'),
(2,1,9,2,'2026-11-25 12:01:17','2026-11-25 12:01:17');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES
(1,3,'2026-11-25 12:01:17','2026-11-25 12:01:17');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Elektronik','elektronik','Gadget dan perangkat elektronik sehari-hari.','2026-11-25 12:01:17','2026-11-25 12:01:17'),
(2,'Fashion','fashion','Pakaian dan alas kaki untuk berbagai gaya.','2026-11-25 12:01:17','2026-11-25 12:01:17'),
(3,'Aksesoris','aksesoris','Pelengkap penampilan dan kebutuhan harian.','2026-11-25 12:01:17','2026-11-25 12:01:17'),
(4,'Rumah Tangga','rumah-tangga','Perlengkapan rumah dan dapur.','2026-11-25 12:01:17','2026-11-25 12:01:17'),
(5,'Olahraga','olahraga','Peralatan untuk hidup aktif dan sehat.','2026-11-25 12:01:17','2026-11-25 12:01:17');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(3,'2026-11-25-000001','App\\Database\\Migrations\\CreateShopeaseTables','default','App',1795582877,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(1,1,19,'Set Pisau Dapur',249000.00,2,498000.00),
(2,2,2,'Sneakers Casual',350000.00,1,350000.00),
(3,2,4,'Bluetooth Speaker Mini',189000.00,1,189000.00),
(4,2,5,'Mechanical Keyboard',549000.00,2,1098000.00),
(5,3,21,'Dumbbell 2 x 3 kg',210000.00,1,210000.00),
(6,4,12,'Celana Chino',229000.00,2,458000.00),
(7,4,14,'Dompet Kulit',199000.00,1,199000.00),
(8,4,18,'Lampu Meja LED',159000.00,2,318000.00),
(9,5,4,'Bluetooth Speaker Mini',189000.00,2,378000.00),
(10,5,20,'Matras Yoga 6 mm',189000.00,1,189000.00),
(11,5,22,'Botol Minum Sport',69000.00,2,138000.00),
(12,6,3,'Smart Watch',450000.00,1,450000.00),
(13,6,8,'Earbuds TWS',259000.00,1,259000.00),
(14,6,18,'Lampu Meja LED',159000.00,1,159000.00),
(15,7,11,'Kemeja Flanel',179000.00,2,358000.00),
(16,7,12,'Celana Chino',229000.00,1,229000.00),
(17,8,7,'Power Bank 10.000 mAh',219000.00,1,219000.00),
(18,9,14,'Dompet Kulit',199000.00,1,199000.00),
(19,10,5,'Mechanical Keyboard',549000.00,2,1098000.00),
(20,10,16,'Tas Ransel Laptop',329000.00,1,329000.00),
(21,11,13,'Kacamata Hitam',149000.00,1,149000.00),
(22,11,18,'Lampu Meja LED',159000.00,2,318000.00),
(23,12,2,'Sneakers Casual',350000.00,1,350000.00),
(24,12,13,'Kacamata Hitam',149000.00,1,149000.00),
(25,12,19,'Set Pisau Dapur',249000.00,1,249000.00),
(26,13,2,'Sneakers Casual',350000.00,2,700000.00),
(27,13,4,'Bluetooth Speaker Mini',189000.00,2,378000.00),
(28,14,6,'Wireless Mouse',129000.00,2,258000.00),
(29,14,20,'Matras Yoga 6 mm',189000.00,1,189000.00),
(30,15,21,'Dumbbell 2 x 3 kg',210000.00,1,210000.00),
(31,16,1,'Wireless Headphone',299000.00,1,299000.00),
(32,16,6,'Wireless Mouse',129000.00,2,258000.00),
(33,16,9,'Kaos Polos Premium',89000.00,1,89000.00),
(34,17,5,'Mechanical Keyboard',549000.00,2,1098000.00),
(35,17,6,'Wireless Mouse',129000.00,1,129000.00),
(36,17,22,'Botol Minum Sport',69000.00,2,138000.00),
(37,18,3,'Smart Watch',450000.00,1,450000.00),
(38,18,15,'Topi Baseball',79000.00,1,79000.00),
(39,19,4,'Bluetooth Speaker Mini',189000.00,2,378000.00),
(40,20,2,'Sneakers Casual',350000.00,2,700000.00),
(41,20,19,'Set Pisau Dapur',249000.00,1,249000.00),
(42,20,20,'Matras Yoga 6 mm',189000.00,2,378000.00),
(43,21,1,'Wireless Headphone',299000.00,1,299000.00),
(44,21,4,'Bluetooth Speaker Mini',189000.00,1,189000.00),
(45,21,11,'Kemeja Flanel',179000.00,2,358000.00),
(46,22,16,'Tas Ransel Laptop',329000.00,1,329000.00),
(47,23,8,'Earbuds TWS',259000.00,1,259000.00),
(48,23,13,'Kacamata Hitam',149000.00,1,149000.00),
(49,24,14,'Dompet Kulit',199000.00,2,398000.00),
(50,24,16,'Tas Ransel Laptop',329000.00,2,658000.00),
(51,24,17,'Tumbler Stainless 750 ml',119000.00,1,119000.00),
(52,25,5,'Mechanical Keyboard',549000.00,2,1098000.00),
(53,25,11,'Kemeja Flanel',179000.00,2,358000.00),
(54,26,9,'Kaos Polos Premium',89000.00,2,178000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `courier` varchar(50) NOT NULL,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `order_status` varchar(30) NOT NULL DEFAULT 'pending_payment',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `order_status` (`order_status`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,4,'SE-20261105-0001','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,498000.00,512000.00,'completed',NULL,'2026-11-05 11:47:14','2026-11-06 11:47:14'),
(2,5,'SE-20261105-0002','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,1637000.00,1650000.00,'completed',NULL,'2026-11-05 14:02:22','2026-11-06 14:02:22'),
(3,3,'SE-20261106-0003','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,210000.00,225000.00,'completed',NULL,'2026-11-06 09:33:46','2026-11-07 09:33:46'),
(4,4,'SE-20261107-0004','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,975000.00,989000.00,'completed',NULL,'2026-11-07 19:37:36','2026-11-08 19:37:36'),
(5,5,'SE-20261108-0005','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,705000.00,718000.00,'completed',NULL,'2026-11-08 08:14:44','2026-11-09 08:14:44'),
(6,3,'SE-20261108-0006','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,868000.00,883000.00,'completed',NULL,'2026-11-08 13:47:04','2026-11-09 13:47:04'),
(7,4,'SE-20261109-0007','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,587000.00,601000.00,'completed',NULL,'2026-11-09 18:43:56','2026-11-10 18:43:56'),
(8,5,'SE-20261110-0008','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,219000.00,232000.00,'completed',NULL,'2026-11-10 17:49:12','2026-11-11 17:49:12'),
(9,3,'SE-20261111-0009','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,199000.00,214000.00,'cancelled','Dibatalkan oleh customer.','2026-11-11 13:03:58','2026-11-12 13:03:58'),
(10,4,'SE-20261111-0010','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,1427000.00,1441000.00,'completed',NULL,'2026-11-11 16:55:20','2026-11-12 16:55:20'),
(11,5,'SE-20261112-0011','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,467000.00,480000.00,'completed',NULL,'2026-11-12 19:45:50','2026-11-13 19:45:50'),
(12,3,'SE-20261113-0012','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,748000.00,763000.00,'completed',NULL,'2026-11-13 12:54:40','2026-11-14 12:54:40'),
(13,4,'SE-20261114-0013','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,1078000.00,1092000.00,'completed',NULL,'2026-11-14 14:56:36','2026-11-15 14:56:36'),
(14,5,'SE-20261115-0014','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,447000.00,460000.00,'completed',NULL,'2026-11-15 13:06:16','2026-11-16 13:06:16'),
(15,3,'SE-20261115-0015','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,210000.00,225000.00,'completed',NULL,'2026-11-15 14:12:23','2026-11-16 14:12:23'),
(16,4,'SE-20261116-0016','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,646000.00,660000.00,'shipped',NULL,'2026-11-16 11:58:57','2026-11-17 11:58:57'),
(17,5,'SE-20261117-0017','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,1365000.00,1378000.00,'shipped',NULL,'2026-11-17 08:03:34','2026-11-18 08:03:34'),
(18,3,'SE-20261118-0018','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,529000.00,544000.00,'shipped',NULL,'2026-11-18 18:09:23','2026-11-19 18:09:23'),
(19,4,'SE-20261118-0019','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,378000.00,392000.00,'shipped',NULL,'2026-11-18 09:32:42','2026-11-19 09:32:42'),
(20,5,'SE-20261119-0020','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,1327000.00,1340000.00,'processing',NULL,'2026-11-19 12:52:02','2026-11-20 12:52:02'),
(21,3,'SE-20261120-0021','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,846000.00,861000.00,'processing',NULL,'2026-11-20 15:28:34','2026-11-21 15:28:34'),
(22,4,'SE-20261121-0022','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,329000.00,343000.00,'processing',NULL,'2026-11-21 10:51:03','2026-11-22 10:51:03'),
(23,5,'SE-20261121-0023','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,408000.00,421000.00,'waiting_verification',NULL,'2026-11-21 16:22:56','2026-11-22 16:22:56'),
(24,3,'SE-20261122-0024','Budi Pratama','081234567801','Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111','JNE Reguler',15000.00,1175000.00,1190000.00,'waiting_verification',NULL,'2026-11-22 16:45:48','2026-11-23 16:45:48'),
(25,4,'SE-20261123-0025','Sari Wulandari','081234567802','Jl. Contoh Indah No. 22\nSidoarjo 61219','J&T Express',14000.00,1456000.00,1470000.00,'pending_payment',NULL,'2026-11-23 16:57:35','2026-11-24 16:57:35'),
(26,5,'SE-20261124-0026','Rina Kartika','081234567803','Jl. Contoh Baru No. 5\nMalang 65145','SiCepat REG',13000.00,178000.00,191000.00,'pending_payment',NULL,'2026-11-24 18:16:50','2026-11-25 18:16:50');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `payment_status` enum('unpaid','submitted','verified','rejected') NOT NULL DEFAULT 'unpaid',
  `note` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `verified_by` int(10) unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,1,'transfer_demo_b',512000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-05 14:47:14',1,'2026-11-05 17:47:14'),
(2,2,'transfer_demo_a',1650000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-05 17:02:22',2,'2026-11-05 20:02:22'),
(3,3,'transfer_demo_b',225000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-06 12:33:46',1,'2026-11-06 15:33:46'),
(4,4,'transfer_demo_a',989000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-07 22:37:36',2,'2026-11-08 01:37:36'),
(5,5,'transfer_demo_b',718000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-08 11:14:44',1,'2026-11-08 14:14:44'),
(6,6,'transfer_demo_a',883000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-08 16:47:04',2,'2026-11-08 19:47:04'),
(7,7,'transfer_demo_b',601000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-09 21:43:56',1,'2026-11-10 00:43:56'),
(8,8,'transfer_demo_a',232000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-10 20:49:12',2,'2026-11-10 23:49:12'),
(9,9,'transfer_demo_b',214000.00,NULL,'unpaid',NULL,NULL,NULL,NULL),
(10,10,'transfer_demo_a',1441000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-11 19:55:20',2,'2026-11-11 22:55:20'),
(11,11,'transfer_demo_b',480000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-12 22:45:50',1,'2026-11-13 01:45:50'),
(12,12,'transfer_demo_a',763000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-13 15:54:40',2,'2026-11-13 18:54:40'),
(13,13,'transfer_demo_b',1092000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-14 17:56:36',1,'2026-11-14 20:56:36'),
(14,14,'transfer_demo_a',460000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-15 16:06:16',2,'2026-11-15 19:06:16'),
(15,15,'transfer_demo_b',225000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-15 17:12:23',1,'2026-11-15 20:12:23'),
(16,16,'transfer_demo_a',660000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-16 14:58:57',2,'2026-11-16 17:58:57'),
(17,17,'transfer_demo_b',1378000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-17 11:03:34',1,'2026-11-17 14:03:34'),
(18,18,'transfer_demo_a',544000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-18 21:09:23',2,'2026-11-19 00:09:23'),
(19,19,'transfer_demo_b',392000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-18 12:32:42',1,'2026-11-18 15:32:42'),
(20,20,'transfer_demo_a',1340000.00,'contoh-bukti-3.jpg','verified',NULL,'2026-11-19 15:52:02',2,'2026-11-19 18:52:02'),
(21,21,'transfer_demo_b',861000.00,'contoh-bukti-1.jpg','verified',NULL,'2026-11-20 18:28:34',1,'2026-11-20 21:28:34'),
(22,22,'transfer_demo_a',343000.00,'contoh-bukti-2.jpg','verified',NULL,'2026-11-21 13:51:03',2,'2026-11-21 16:51:03'),
(23,23,'transfer_demo_b',421000.00,'contoh-bukti-3.jpg','submitted',NULL,'2026-11-21 19:22:56',NULL,NULL),
(24,24,'transfer_demo_a',1190000.00,'contoh-bukti-1.jpg','submitted',NULL,'2026-11-22 19:45:48',NULL,NULL),
(25,25,'transfer_demo_b',1470000.00,NULL,'unpaid',NULL,NULL,NULL,NULL),
(26,26,'transfer_demo_a',191000.00,NULL,'unpaid',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `status` (`status`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,1,'Wireless Headphone','Headphone Bluetooth dengan bantalan empuk dan baterai hingga 30 jam.',299000.00,20,'wireless-headphone.jpg','active','2026-10-11 12:01:17','2026-10-11 12:01:17'),
(2,2,'Sneakers Casual','Sepatu casual pria berbahan kanvas, nyaman dipakai seharian.',350000.00,15,'sneakers-casual.jpg','active','2026-10-12 12:01:17','2026-10-12 12:01:17'),
(3,3,'Smart Watch','Jam tangan digital dengan pemantau detak jantung dan notifikasi.',450000.00,10,'smart-watch.jpg','active','2026-10-13 12:01:17','2026-10-13 12:01:17'),
(4,1,'Bluetooth Speaker Mini','Speaker portabel tahan cipratan air dengan suara jernih.',189000.00,25,'bluetooth-speaker.jpg','active','2026-10-14 12:01:17','2026-10-14 12:01:17'),
(5,1,'Mechanical Keyboard','Keyboard mekanik 87 tombol dengan switch taktil.',549000.00,12,'mechanical-keyboard.jpg','active','2026-10-15 12:01:17','2026-10-15 12:01:17'),
(6,1,'Wireless Mouse','Mouse nirkabel senyap, ergonomis, dan hemat baterai.',129000.00,30,'wireless-mouse.jpg','active','2026-10-16 12:01:17','2026-10-16 12:01:17'),
(7,1,'Power Bank 10.000 mAh','Pengisian cepat 18W dengan dua port USB.',219000.00,18,'power-bank.jpg','active','2026-10-17 12:01:17','2026-10-17 12:01:17'),
(8,1,'Earbuds TWS','Earbuds true wireless dengan case pengisi daya.',259000.00,22,'earbuds-tws.jpg','active','2026-10-18 12:01:17','2026-10-18 12:01:17'),
(9,2,'Kaos Polos Premium','Kaos katun combed 30s, adem dan tidak mudah melar.',89000.00,50,'kaos-polos.jpg','active','2026-10-19 12:01:17','2026-10-19 12:01:17'),
(10,2,'Jaket Denim','Jaket denim klasik dengan potongan regular fit.',399000.00,8,'jaket-denim.jpg','active','2026-10-20 12:01:17','2026-10-20 12:01:17'),
(11,2,'Kemeja Flanel','Kemeja flanel motif kotak, cocok untuk santai maupun kuliah.',179000.00,20,'kemeja-flanel.jpg','active','2026-10-21 12:01:17','2026-10-21 12:01:17'),
(12,2,'Celana Chino','Celana chino slim fit berbahan stretch.',229000.00,14,'celana-chino.jpg','active','2026-10-22 12:01:17','2026-10-22 12:01:17'),
(13,3,'Kacamata Hitam','Kacamata hitam dengan lensa UV400.',149000.00,16,'kacamata-hitam.jpg','active','2026-10-23 12:01:17','2026-10-23 12:01:17'),
(14,3,'Dompet Kulit','Dompet lipat dari kulit sintetis dengan banyak slot kartu.',199000.00,12,'dompet-kulit.jpg','active','2026-10-24 12:01:17','2026-10-24 12:01:17'),
(15,3,'Topi Baseball','Topi baseball katun dengan tali pengatur.',79000.00,30,'topi-baseball.jpg','active','2026-10-25 12:01:17','2026-10-25 12:01:17'),
(16,3,'Tas Ransel Laptop','Ransel laptop 15 inci dengan kompartemen empuk.',329000.00,9,'tas-ransel.jpg','active','2026-10-26 12:01:17','2026-10-26 12:01:17'),
(17,4,'Tumbler Stainless 750 ml','Menjaga minuman tetap dingin hingga 24 jam.',119000.00,40,'tumbler-stainless.jpg','active','2026-10-27 12:01:17','2026-10-27 12:01:17'),
(18,4,'Lampu Meja LED','Lampu belajar LED dengan tiga tingkat kecerahan.',159000.00,15,'lampu-meja.jpg','active','2026-10-28 12:01:17','2026-10-28 12:01:17'),
(19,4,'Set Pisau Dapur','Set tiga pisau dapur stainless anti karat.',249000.00,7,'set-pisau.jpg','active','2026-10-29 12:01:17','2026-10-29 12:01:17'),
(20,5,'Matras Yoga 6 mm','Matras yoga anti selip, ringan, dan mudah digulung.',189000.00,20,'matras-yoga.jpg','active','2026-10-30 12:01:17','2026-10-30 12:01:17'),
(21,5,'Dumbbell 2 x 3 kg','Sepasang dumbbell berlapis vinyl.',210000.00,10,'dumbbell.jpg','active','2026-10-31 12:01:17','2026-10-31 12:01:17'),
(22,5,'Botol Minum Sport','Botol 1 liter bebas BPA dengan penanda waktu minum.',69000.00,35,'botol-sport.jpg','active','2026-11-01 12:01:17','2026-11-01 12:01:17'),
(23,5,'Tali Skipping','Tali skipping dengan bearing, cocok untuk kardio.',49000.00,0,'tali-skipping.jpg','active','2026-11-02 12:01:17','2026-11-02 12:01:17'),
(24,1,'Kamera Aksi 4K','Kamera aksi tahan air. Sementara tidak dijual.',899000.00,5,'kamera-aksi.jpg','inactive','2026-11-03 12:01:17','2026-11-03 12:01:17');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `courier` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipment_status` varchar(30) NOT NULL DEFAULT 'pending',
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  CONSTRAINT `shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES
(1,1,'J&T Express','JNTDEMO001037','delivered','2026-11-06 17:47:14','2026-11-08 11:47:14'),
(2,2,'SiCepat REG','SICEPATDEMO001074','delivered','2026-11-06 20:02:22','2026-11-08 14:02:22'),
(3,3,'JNE Reguler','JNEDEMO001111','delivered','2026-11-07 15:33:46','2026-11-09 09:33:46'),
(4,4,'J&T Express','JNTDEMO001148','delivered','2026-11-09 01:37:36','2026-11-10 19:37:36'),
(5,5,'SiCepat REG','SICEPATDEMO001185','delivered','2026-11-09 14:14:44','2026-11-11 08:14:44'),
(6,6,'JNE Reguler','JNEDEMO001222','delivered','2026-11-09 19:47:04','2026-11-11 13:47:04'),
(7,7,'J&T Express','JNTDEMO001259','delivered','2026-11-11 00:43:56','2026-11-12 18:43:56'),
(8,8,'SiCepat REG','SICEPATDEMO001296','delivered','2026-11-11 23:49:12','2026-11-13 17:49:12'),
(9,9,'JNE Reguler',NULL,'pending',NULL,NULL),
(10,10,'J&T Express','JNTDEMO001370','delivered','2026-11-12 22:55:20','2026-11-14 16:55:20'),
(11,11,'SiCepat REG','SICEPATDEMO001407','delivered','2026-11-14 01:45:50','2026-11-15 19:45:50'),
(12,12,'JNE Reguler','JNEDEMO001444','delivered','2026-11-14 18:54:40','2026-11-16 12:54:40'),
(13,13,'J&T Express','JNTDEMO001481','delivered','2026-11-15 20:56:36','2026-11-17 14:56:36'),
(14,14,'SiCepat REG','SICEPATDEMO001518','delivered','2026-11-16 19:06:16','2026-11-18 13:06:16'),
(15,15,'JNE Reguler','JNEDEMO001555','delivered','2026-11-16 20:12:23','2026-11-18 14:12:23'),
(16,16,'J&T Express','JNTDEMO001592','shipped','2026-11-17 17:58:57',NULL),
(17,17,'SiCepat REG','SICEPATDEMO001629','shipped','2026-11-18 14:03:34',NULL),
(18,18,'JNE Reguler','JNEDEMO001666','shipped','2026-11-20 00:09:23',NULL),
(19,19,'J&T Express','JNTDEMO001703','shipped','2026-11-19 15:32:42',NULL),
(20,20,'SiCepat REG',NULL,'pending',NULL,NULL),
(21,21,'JNE Reguler',NULL,'pending',NULL,NULL),
(22,22,'J&T Express',NULL,'pending',NULL,NULL),
(23,23,'SiCepat REG',NULL,'pending',NULL,NULL),
(24,24,'JNE Reguler',NULL,'pending',NULL,NULL),
(25,25,'J&T Express',NULL,'pending',NULL,NULL),
(26,26,'SiCepat REG',NULL,'pending',NULL,NULL);
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','operator') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Administrator ShopEase','admin@shopease.com','$2y$10$5A4ZlFK8/s/KMvgcy4YKOuNRU39KUicb59.sSmIO82SVzSabLmF4G','admin','081200000001','Kantor ShopEase, Surabaya','2026-10-16 12:01:17','2026-11-25 12:01:17'),
(2,'Operator Gudang','operator@shopease.com','$2y$10$5A4ZlFK8/s/KMvgcy4YKOuNRU39KUicb59.sSmIO82SVzSabLmF4G','operator','081200000002','Gudang ShopEase, Surabaya','2026-10-16 12:01:17','2026-11-25 12:01:17'),
(3,'Budi Pratama','budi@example.com','$2y$10$5A4ZlFK8/s/KMvgcy4YKOuNRU39KUicb59.sSmIO82SVzSabLmF4G','customer','081234567801','Jl. Contoh Raya No. 10, Surabaya','2026-10-16 12:01:17','2026-11-25 12:01:17'),
(4,'Sari Wulandari','sari@example.com','$2y$10$5A4ZlFK8/s/KMvgcy4YKOuNRU39KUicb59.sSmIO82SVzSabLmF4G','customer','081234567802','Jl. Contoh Indah No. 22, Sidoarjo','2026-10-16 12:01:17','2026-11-25 12:01:17'),
(5,'Rina Kartika','rina@example.com','$2y$10$5A4ZlFK8/s/KMvgcy4YKOuNRU39KUicb59.sSmIO82SVzSabLmF4G','customer','081234567803','Jl. Contoh Baru No. 5, Malang','2026-10-16 12:01:17','2026-11-25 12:01:17');
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


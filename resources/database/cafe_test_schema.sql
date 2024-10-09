-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cafe
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
-- Table structure for table `administrator`
--

DROP TABLE IF EXISTS `administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrator` (
  `user_id` int(11) unsigned NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `admin_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `job_title_length` CHECK (char_length(`job_title`) > 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrator`
--

LOCK TABLES `administrator` WRITE;
/*!40000 ALTER TABLE `administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `user_id` int(11) unsigned NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `district_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `client_district_district_id_fk` (`district_id`),
  CONSTRAINT `client_district_district_id_fk` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON UPDATE CASCADE,
  CONSTRAINT `client_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `client_city_length` CHECK (char_length(`city`) > 2),
  CONSTRAINT `client_street_length` CHECK (char_length(`street`) > 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1,'Royal Road','Rochester',2),(2,'Main Road','Curepipe',9);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(2000) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `parent_comment_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `review_id` int(10) unsigned NOT NULL COMMENT 'ID of review under which comment is found	',
  PRIMARY KEY (`comment_id`),
  KEY `comment_comment_comment_id_fk` (`parent_comment_id`),
  KEY `comment_user_user_id_fk` (`user_id`),
  KEY `comment_review_review_id_fk` (`review_id`),
  CONSTRAINT `comment_comment_comment_id_fk` FOREIGN KEY (`parent_comment_id`) REFERENCES `comment` (`comment_id`) ON DELETE CASCADE,
  CONSTRAINT `comment_review_review_id_fk` FOREIGN KEY (`review_id`) REFERENCES `review` (`review_id`),
  CONSTRAINT `comment_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (2,'i am commenting something here','2024-05-16 11:21:36',NULL,2,1),(3,'comments can be nested','2024-05-16 11:21:46',2,2,1),(4,'test','2024-05-16 11:22:02',NULL,2,5),(5,'Awesome !!!','2024-05-16 11:22:10',NULL,2,5),(6,'Topp!! Above my expectations','2024-06-12 12:49:46',NULL,2,5);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `district`
--

DROP TABLE IF EXISTS `district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `district` (
  `district_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('Moka','Port Louis','Flacq','Curepipe','Black River','Savanne','Grand Port','Riviere du Rempart','Pamplemousses','Mahebourg','Plaines Wilhems') NOT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `district`
--

LOCK TABLES `district` WRITE;
/*!40000 ALTER TABLE `district` DISABLE KEYS */;
INSERT INTO `district` VALUES (1,'Moka'),(2,'Port Louis'),(3,'Flacq'),(4,'Black River'),(5,'Savanne'),(6,'Grand Port'),(7,'Riviere du Rempart'),(8,'Pamplemousses'),(9,'Plaines Wilhems');
/*!40000 ALTER TABLE `district` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('pending','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `pickup_date` datetime DEFAULT NULL COMMENT 'Date when client picks up his order at the store',
  `client_id` int(11) unsigned DEFAULT NULL,
  `store_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_fk` (`client_id`),
  KEY `order_store_store_id_fk` (`store_id`),
  CONSTRAINT `order_fk` FOREIGN KEY (`client_id`) REFERENCES `client` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_store_store_id_fk` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`),
  CONSTRAINT `pickup_date_range` CHECK (`pickup_date` is null or `pickup_date` >= `created_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (2,'pending','2024-06-08 17:29:32',NULL,2,1);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_product` (
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `cup_size` enum('small','medium','large') NOT NULL,
  `milk_type` enum('almond','coconut','oat','soy') NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL COMMENT 'Unit price of product',
  PRIMARY KEY (`order_id`,`product_id`,`cup_size`,`milk_type`),
  KEY `order_product_product_product_id_fk` (`product_id`),
  CONSTRAINT `order_product_order_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_product_product_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  CONSTRAINT `quantity_range` CHECK (`quantity` > 0),
  CONSTRAINT `unit_price_range` CHECK (`unit_price` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_product`
--

LOCK TABLES `order_product` WRITE;
/*!40000 ALTER TABLE `order_product` DISABLE KEYS */;
INSERT INTO `order_product` VALUES (2,2,'small','almond',1,4.99);
/*!40000 ALTER TABLE `order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_change_request`
--

DROP TABLE IF EXISTS `password_change_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_change_request` (
  `request_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether token has been used once	',
  PRIMARY KEY (`request_id`),
  KEY `request_fk` (`user_id`),
  CONSTRAINT `request_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_change_request`
--

LOCK TABLES `password_change_request` WRITE;
/*!40000 ALTER TABLE `password_change_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_change_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `calories` int(11) unsigned NOT NULL,
  `img_url` varchar(255) NOT NULL,
  `img_alt_text` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL CHECK (char_length(`description`) > 0),
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`product_id`),
  CONSTRAINT `name_length` CHECK (char_length(`name`) > 2),
  CONSTRAINT `img_alt_text_length` CHECK (char_length(`img_alt_text`) between 5 and 150),
  CONSTRAINT `category_length` CHECK (char_length(`category`) > 2),
  CONSTRAINT `img_url_format` CHECK (`img_url` like '%.png' or `img_url` like '%.jpeg' or `img_url` like '%.avif' or `img_url` like '%.jpg' or `img_url` like '%.webp')
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Espresso',5,'espresso.webp','Espresso in a white cup. Source: Dolce Gusto','Espresso',2.99,'A strong and concentrated coffee drink.','2024-04-28 12:37:10'),(2,'Cappuccino',120,'cappuccino.webp','Close-up of a steaming cup of freshly brewed Espresso with frothy milk on top. Source: Discount Coffee','Cappuccino',4.99,'An Italian coffee drink made with espresso, hot milk, and steamed milk foam.','2024-05-21 12:37:10'),(3,'Caff?? Latte',190,'latte.avif','A latte with a spoon. Source: Peet\'s Coffee.','Latte',3.99,'A coffee drink made with espresso and steamed milk.','2024-04-23 12:37:10'),(4,'Caff?? Americano',15,'americano.webp','Close-up of a clear glass mug filled with hot, black Americano coffee, topped with a thin layer of creme. Source: Peet\'s Coffee.','Americano',3.49,'A coffee drink prepared by diluting espresso with hot water.','2024-01-21 12:37:10'),(5,'Caff?? Mocha',370,'mocha.png','Rich and indulgent mocha served in a ceramic mug, topped with whipped cream and a dusting of cocoa powder. Source: Starbucks','Mocha',4.49,'A chocolate-flavored variant of a latte, often with whipped cream on top.','2024-04-21 12:37:10'),(6,'White Chocolate Mocha',390,'white-chocolate-mocha.png','Rich and indulgent mocha served in a ceramic mug, topped with whipped cream and a dusting of cocoa powder. Source: Starbucks','Mocha',5.69,'Our signature mocha meets white chocolate sauce and steamed milk, and then is finished off with sweetened whipped cream to create this supreme white chocolate delight.','2024-04-25 10:33:02'),(8,'Caramel Macchiato',250,'caramel-macchiato.png','Freshly steamed milk with vanilla-flavored syrup marked with espresso and topped with a caramel drizzle for an oh-so-sweet finish. Source: Starbucks','Macchiato',3.33,'Freshly steamed milk with vanilla-flavored syrup marked with espresso and topped with a caramel drizzle for an oh-so-sweet finish.','2024-04-25 10:45:50'),(9,'Espresso Macchiato',15,'espresso-macchiato.png','Our rich espresso marked with dollop of steamed milk and foam. A European-style classic. Source: Starbucks','Macchiato',8.85,'Our rich espresso marked with dollop of steamed milk and foam. A European-style classic.','2024-04-25 10:48:35'),(10,'Espresso Con Panna',35,'espresso-con-panna.webp','Espresso meets a dollop of whipped cream to enhance the rich and caramelly flavors of a straight-up shot. Source: Starbucks','Espresso',4.34,'Espresso meets a dollop of whipped cream to enhance the rich and caramelly flavors of a straight-up shot.','2024-04-25 10:52:07'),(11,'Velvet',150,'velvet.png','velvet','latte',9.99,'A delicious coffee','2024-06-10 19:39:05');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `review_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rating` int(11) unsigned NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `text` varchar(2000) NOT NULL,
  `client_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `review_1fk` (`client_id`),
  KEY `review_2fk` (`product_id`),
  CONSTRAINT `review_1fk` FOREIGN KEY (`client_id`) REFERENCES `client` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `review_2fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `check_rating` CHECK (`rating` between 1 and 5),
  CONSTRAINT `text_length` CHECK (char_length(`text`) >= 2)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,5,'2024-05-16 11:11:02','i really like this product. I have a verified badge since I have actually bought this product.',1,1),(2,3,'2024-05-16 11:11:24','the coffee is decent but nothing special',1,2),(3,2,'2024-05-16 11:11:53','i would love to buy this again but the price is not worth it',1,3),(4,1,'2024-05-16 11:14:04','Note the calculation for average rating does not consider unverified reviews',1,3),(5,1,'2024-05-16 11:21:56','this is a negative review',2,1),(6,3,'2024-05-16 11:22:44','this is a wonderful product that changed my life',2,3),(7,3,'2024-06-10 19:51:05','Top!!',2,2);
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store` (
  `store_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone_no` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `coordinate` point NOT NULL,
  `district_id` int(10) unsigned NOT NULL,
  `city` varchar(255) NOT NULL,
  PRIMARY KEY (`store_id`),
  KEY `store_district_district_id_fk` (`district_id`),
  SPATIAL KEY `store_coordinate_index` (`coordinate`),
  CONSTRAINT `store_district_district_id_fk` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (1,'+230 630 1329','Royal Road','\0\0\0\0\0\0\0??????&??74???J{???/L???L@',1,'Bagatelle'),(2,'+230 630 1234','Angus Road','\0\0\0\0\0\0\0??????&??74????????????&???U@',4,'Albion');
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_product`
--

DROP TABLE IF EXISTS `store_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_product` (
  `store_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `stock_level` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`store_id`,`product_id`),
  KEY `store_product_product_product_id_fk` (`product_id`),
  CONSTRAINT `store_product_product_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  CONSTRAINT `store_product_store_store_id_fk` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_product`
--

LOCK TABLES `store_product` WRITE;
/*!40000 ALTER TABLE `store_product` DISABLE KEYS */;
INSERT INTO `store_product` VALUES (1,1,3),(1,2,5438),(1,3,54),(1,4,38),(1,5,998),(2,1,22),(2,3,13),(2,4,12);
/*!40000 ALTER TABLE `store_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(320) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_email` (`email`),
  CONSTRAINT `email_format` CHECK (`email` like '%@%.%'),
  CONSTRAINT `password_length` CHECK (char_length(`password`) > 8),
  CONSTRAINT `phone_number_length` CHECK (char_length(`phone_no`) > 6),
  CONSTRAINT `first_name_length` CHECK (char_length(`first_name`) > 2),
  CONSTRAINT `last_name_length` CHECK (char_length(`first_name`) > 2)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'c34560814@gmail.com','john','$2y$10$Gz37vrrGkoaOSoGKvJWgRegKWExfRAKs8UjxUdyyyrRwD6Q43sfZW','+230-5-123-4567','????????????'),(2,'divjok28@outlook.com','Divyesh','$2y$10$6Bj0y0J2r9OoGjjYpTThQenCsR6RHQ14fNm4E.CPBmHDqJbKsuPju','+230-5-123-4567','Jokhoo');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-12 16:14:37

-- MySQL dump 10.19  Distrib 10.3.38-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: cafe
-- ------------------------------------------------------
-- Server version	10.3.38-MariaDB-0ubuntu0.20.04.1

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
  `is_superadmin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `admin_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `job_title_length` CHECK (char_length(`job_title`) > 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
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
  `district_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `client_district_district_id_fk` (`district_id`),
  CONSTRAINT `client_district_district_id_fk` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`),
  CONSTRAINT `client_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `city_length` CHECK (char_length(`city`) > 2),
  CONSTRAINT `street_length` CHECK (char_length(`street`) > 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `district`
--

DROP TABLE IF EXISTS `district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `district` (
  `district_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `name` (`name`),
  CONSTRAINT `name_values` CHECK (`name` in ('Moka','Port Louis','Flacq','Curepipe','Black River','Savanne','Grand Port','Riviere du Rempart','Pamplemousses','Mahebourg','Plaines Wilhems'))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `district`
--

LOCK TABLES `district` WRITE;
/*!40000 ALTER TABLE `district` DISABLE KEYS */;
INSERT INTO `district` VALUES (4,'Black River'),(3,'Flacq'),(6,'Grand Port'),(1,'Moka'),(8,'Pamplemousses'),(9,'Plaines Wilhems'),(2,'Port Louis'),(7,'Riviere du Rempart'),(5,'Savanne');
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
  `status` varchar(20) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `district_id` int(11) unsigned DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL CHECK (`total_price` >= 0),
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_fk` (`user_id`),
  KEY `order_district_district_id_fk` (`district_id`),
  CONSTRAINT `order_district_district_id_fk` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`),
  CONSTRAINT `order_fk` FOREIGN KEY (`user_id`) REFERENCES `client` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `pickup_date_range` CHECK (`pickup_date` is null or `pickup_date` >= `created_date`),
  CONSTRAINT `city_length` CHECK (char_length(`city`) > 2),
  CONSTRAINT `street_length` CHECK (char_length(`street`) > 3)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
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
  `cup_size` varchar(20) DEFAULT NULL,
  `milk_type` varchar(20) DEFAULT NULL,
  `quantity` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`order_id`,`product_id`),
  KEY `order_product_2fk` (`product_id`),
  CONSTRAINT `order_product_1fk` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_product_2fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  CONSTRAINT `quantity_range` CHECK (`quantity` >= 0),
  CONSTRAINT `cup_size` CHECK (`cup_size` in ('small','medium','large')),
  CONSTRAINT `milk_type` CHECK (`milk_type` in ('almond','coconut','oat','soy'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_product`
--

LOCK TABLES `order_product` WRITE;
/*!40000 ALTER TABLE `order_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_product` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `UpdateStockLevel` AFTER INSERT ON `order_product` FOR EACH ROW

BEGIN

    DECLARE quantity_ordered INT;

    DECLARE product_id INT;



    SET quantity_ordered = NEW.quantity;

    SET product_id = NEW.product_id;



    UPDATE `product` SET stock_level = stock_level - quantity_ordered WHERE product_id = product_id;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `calories` int(11) unsigned DEFAULT NULL CHECK (`calories` >= 0),
  `stock_level` int(11) unsigned DEFAULT NULL CHECK (`stock_level` >= 0),
  `img_url` varchar(255) NOT NULL,
  `img_alt_text` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL CHECK (char_length(`description`) > 0),
  PRIMARY KEY (`product_id`),
  CONSTRAINT `name_length` CHECK (char_length(`name`) > 2),
  CONSTRAINT `img_url_format` CHECK (`img_url` like '%.png' or `img_url` like '%.jpeg' or `img_url` like '%.avif'),
  CONSTRAINT `img_alt_text_length` CHECK (char_length(`img_alt_text`) between 5 and 150),
  CONSTRAINT `category_length` CHECK (char_length(`category`) > 2)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Espresso',5,100,'espresso.png','Espresso Image','Espresso',2.99,'A strong and concentrated coffee drink.'),(2,'Cappuccino',120,75,'cappuccino.jpeg','Cappuccino Image','Cappuccino',4.99,'An Italian coffee drink made with espresso, hot milk, and steamed milk foam.'),(3,'Latte',150,60,'latte.png','Latte Image','Latte',3.99,'A coffee drink made with espresso and steamed milk.'),(4,'Americano',5,80,'americano.avif','Americano Image','Americano',3.49,'A coffee drink prepared by diluting espresso with hot water.'),(5,'Mocha',200,70,'mocha.jpeg','Mocha Image','Mocha',4.49,'A chocolate-flavored variant of a latte, often with whipped cream on top.');
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
  `date` date NOT NULL,
  `text` text NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `product_id` int(11) unsigned DEFAULT NULL,
  `parent_review_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `review_1fk` (`user_id`),
  KEY `review_2fk` (`product_id`),
  KEY `review_3fk` (`parent_review_id`),
  CONSTRAINT `review_1fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `review_2fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `review_3fk` FOREIGN KEY (`parent_review_id`) REFERENCES `review` (`review_id`) ON DELETE SET NULL,
  CONSTRAINT `check_rating` CHECK (`rating` between 1 and 5),
  CONSTRAINT `text_length` CHECK (char_length(`text`) >= 2)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
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
  `first_name` varchar(255) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `phone_no` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_email` (`email`),
  CONSTRAINT `email_format` CHECK (`email` like '%@%.%'),
  CONSTRAINT `password_length` CHECK (char_length(`password`) > 8),
  CONSTRAINT `phone_number_length` CHECK (char_length(`phone_no`) > 6),
  CONSTRAINT `first_name_length` CHECK (char_length(`first_name`) > 2),
  CONSTRAINT `last_name_length` CHECK (char_length(`first_name`) > 2)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
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

-- Dump completed on 2024-02-24 10:37:26

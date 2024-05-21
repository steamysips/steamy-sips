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
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `administrator`
--

LOCK TABLES `administrator` WRITE;
/*!40000 ALTER TABLE `administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1,'Royal Road','Rochester',2),(2,'Main Road','Curepipe',9);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (1,'i can comment on my own review','2024-05-16 11:12:19',NULL,1,3),(2,'i am commenting something here','2024-05-16 11:21:36',NULL,2,1),(3,'comments can be nested','2024-05-16 11:21:46',2,2,1),(4,'test','2024-05-16 11:22:02',NULL,2,5),(5,'ok','2024-05-16 11:22:10',NULL,2,2);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `district`
--

LOCK TABLES `district` WRITE;
/*!40000 ALTER TABLE `district` DISABLE KEYS */;
INSERT INTO `district` VALUES (4,'Black River'),(3,'Flacq'),(6,'Grand Port'),(1,'Moka'),(8,'Pamplemousses'),(9,'Plaines Wilhems'),(2,'Port Louis'),(7,'Riviere du Rempart'),(5,'Savanne');
/*!40000 ALTER TABLE `district` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1,'pending','2024-05-16 10:33:54',NULL,1,1);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `order_product`
--

LOCK TABLES `order_product` WRITE;
/*!40000 ALTER TABLE `order_product` DISABLE KEYS */;
INSERT INTO `order_product` VALUES (1,1,'medium','oat',1,2.99),(1,2,'small','almond',1,4.99);
/*!40000 ALTER TABLE `order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `password_change_request`
--

LOCK TABLES `password_change_request` WRITE;
/*!40000 ALTER TABLE `password_change_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_change_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Espresso',5,'espresso.webp','Espresso in a white cup. Source: Dolce Gusto','Espresso',2.99,'A strong and concentrated coffee drink.','2024-04-28 12:37:10'),(2,'Cappuccino',120,'cappuccino.webp','Close-up of a steaming cup of freshly brewed Espresso with frothy milk on top. Source: Discount Coffee','Cappuccino',4.99,'An Italian coffee drink made with espresso, hot milk, and steamed milk foam.','2024-05-21 12:37:10'),(3,'Caffè Latte',190,'latte.avif','A latte with a spoon. Source: Peet\'s Coffee.','Latte',3.99,'A coffee drink made with espresso and steamed milk.','2024-04-23 12:37:10'),(4,'Caffè Americano',15,'americano.webp','Close-up of a clear glass mug filled with hot, black Americano coffee, topped with a thin layer of creme. Source: Peet\'s Coffee.','Americano',3.49,'A coffee drink prepared by diluting espresso with hot water.','2024-01-21 12:37:10'),(5,'Caffè Mocha',370,'mocha.png','Rich and indulgent mocha served in a ceramic mug, topped with whipped cream and a dusting of cocoa powder. Source: Starbucks','Mocha',4.49,'A chocolate-flavored variant of a latte, often with whipped cream on top.','2024-04-21 12:37:10'),(6,'White Chocolate Mocha',390,'white-chocolate-mocha.png','Rich and indulgent mocha served in a ceramic mug, topped with whipped cream and a dusting of cocoa powder. Source: Starbucks','Mocha',5.69,'Our signature mocha meets white chocolate sauce and steamed milk, and then is finished off with sweetened whipped cream to create this supreme white chocolate delight.','2024-04-25 10:33:02'),(7,'Cinnamon Dolce Latte\n',340,'cinnamon-dolce-latte.webp','Steamed milk and cinnamon dolce-flavored syrup on Latte. Source: Starbucks ','Latte',7.88,'We add freshly steamed milk and cinnamon dolce-flavored syrup to our classic espresso, topped with sweetened whipped cream and a cinnamon dolce topping to bring you specialness in a treat.','2024-04-25 10:37:23'),(8,'Caramel Macchiato',250,'caramel-macchiato.png','Freshly steamed milk with vanilla-flavored syrup marked with espresso and topped with a caramel drizzle for an oh-so-sweet finish. Source: Starbucks','Macchiato',3.33,'Freshly steamed milk with vanilla-flavored syrup marked with espresso and topped with a caramel drizzle for an oh-so-sweet finish.','2024-04-25 10:45:50'),(9,'Espresso Macchiato',15,'espresso-macchiato.png','Our rich espresso marked with dollop of steamed milk and foam. A European-style classic. Source: Starbucks','Macchiato',8.85,'Our rich espresso marked with dollop of steamed milk and foam. A European-style classic.','2024-04-25 10:48:35'),(10,'Espresso Con Panna',35,'espresso-con-panna.webp','Espresso meets a dollop of whipped cream to enhance the rich and caramelly flavors of a straight-up shot. Source: Starbucks','Espresso',4.34,'Espresso meets a dollop of whipped cream to enhance the rich and caramelly flavors of a straight-up shot.','2024-04-25 10:52:07');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,5,'2024-05-16 11:11:02','i really like this product. I have a verified badge since I have actually bought this product.',1,1),(2,3,'2024-05-16 11:11:24','the coffee is decent but nothing special',1,2),(3,2,'2024-05-16 11:11:53','i would love to buy this again but the price is not worth it',1,3),(4,1,'2024-05-16 11:14:04','Note the calculation for average rating does not consider unverified reviews',1,3),(5,1,'2024-05-16 11:21:56','this is a negative review',2,1),(6,3,'2024-05-16 11:22:44','this is a wonderful product that changed my life',2,3);
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (1,'+230 630 1329','Royal Road','\0\0\0\0\0\0\0��&74�J{�/L�L@',1,'Bagatelle'),(2,'+230 630 1234','Angus Road','\0\0\0\0\0\0\0��&74����&�U@',4,'Albion');
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `store_product`
--

LOCK TABLES `store_product` WRITE;
/*!40000 ALTER TABLE `store_product` DISABLE KEYS */;
INSERT INTO `store_product` VALUES (1,1,3),(1,2,5439),(1,3,54),(1,4,38),(1,5,998),(2,1,22),(2,3,13),(2,4,12);
/*!40000 ALTER TABLE `store_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'c34560814@gmail.com','john','$2y$10$Gz37vrrGkoaOSoGKvJWgRegKWExfRAKs8UjxUdyyyrRwD6Q43sfZW','+230-5-123-4567','💋💋💋'),(2,'divjok28@outlook.com','Divyesh','$2y$10$6Bj0y0J2r9OoGjjYpTThQenCsR6RHQ14fNm4E.CPBmHDqJbKsuPju','+230-5-123-4567','Jokhoo');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-16 11:23:20

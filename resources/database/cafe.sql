-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2024 at 09:24 AM
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
-- Database: `cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `is_superadmin` tinyint(1) DEFAULT 0
) ;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`user_id`, `job_title`, `is_superadmin`) VALUES
(1, 'Cafe Manager', 1),
(2, 'Barista', 0),
(3, 'Barista', 0);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `user_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`user_id`, `street`, `city`, `district`) VALUES
(4, 'telfair', 'Port-louis', 'Moka');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL CHECK (`status` in ('pending','cancelled','completed')),
  `created_date` date NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL
) ;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `status`, `created_date`, `pickup_date`, `street`, `city`, `district`, `total_price`, `user_id`) VALUES
(1, 'pending', '2024-02-17', '2024-02-18', '123 Main St', 'Port-louis', 'Moka', 50.00, 4),
(2, 'cancelled', '2024-02-18', '2024-02-19', '456 Elm St', 'Port-louis', 'Moka', 75.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

CREATE TABLE `order_product` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cup_size` varchar(50) NOT NULL,
  `milk_type` varchar(50) NOT NULL
) ;

--
-- Dumping data for table `order_product`
--

INSERT INTO `order_product` (`order_id`, `product_id`, `quantity`, `cup_size`, `milk_type`) VALUES
(1, 1, 2, 'medium', 'soy'),
(1, 2, 1, 'large', 'oat'),
(2, 3, 3, 'small', 'almond'),
(2, 4, 1, 'medium', 'coconut'),
(2, 5, 2, 'large', 'chocolate');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `calories` int(11) DEFAULT NULL CHECK (`calories` >= 0),
  `stock_level` int(11) DEFAULT NULL CHECK (`stock_level` >= 0),
  `img_url` varchar(255) NOT NULL,
  `img_alt_text` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL CHECK (char_length(`description`) > 0)
) ;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `calories`, `stock_level`, `img_url`, `img_alt_text`, `category`, `price`, `description`) VALUES
(1, 'Espresso', 5, 100, 'espresso.png', 'Espresso Image', 'Espresso', 2.99, 'A strong and concentrated coffee drink.'),
(2, 'Cappuccino', 120, 75, 'cappuccino.jpeg', 'Cappuccino Image', 'Cappuccino', 4.99, 'An Italian coffee drink made with espresso, hot milk, and steamed milk foam.'),
(3, 'Latte', 150, 60, 'latte.png', 'Latte Image', 'Latte', 3.99, 'A coffee drink made with espresso and steamed milk.'),
(4, 'Americano', 5, 80, 'americano.avif', 'Americano Image', 'Americano', 3.49, 'A coffee drink prepared by diluting espresso with hot water.'),
(5, 'Mocha', 200, 70, 'mocha.jpeg', 'Mocha Image', 'Mocha', 4.49, 'A chocolate-flavored variant of a latte, often with whipped cream on top.');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `date` date NOT NULL,
  `text` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `parent_review_id` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `rating`, `date`, `text`, `user_id`, `product_id`, `parent_review_id`) VALUES
(1, 5, '2024-02-17', 'Excellent coffee, loved the taste and aroma!', 1, 1, NULL),
(2, 4, '2024-02-18', 'Great service, but the coffee was a bit too bitter for my liking.', 2, 1, NULL),
(3, 3, '2024-02-19', 'I agree with the previous review, the coffee was indeed excellent!', 3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(320) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `name`, `password`, `phone_no`) VALUES
(1, 'divjok28@gmail.com', 'divyesh jokhoo', '123456789', '5674675432'),
(2, 'tomabc@gmail.com', 'tom abc', '987654321', '593749393'),
(3, 'devsing@gmail.com', 'deving soopal', '8484848484', '538937439'),
(4, 'jerry@gmail.com', 'jerry sawyer', '5555555555', '57473999283');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `order_fk` (`user_id`);

--
-- Indexes for table `order_product`
--
ALTER TABLE `order_product`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `review_1fk` (`user_id`),
  ADD KEY `review_2fk` (`product_id`),
  ADD KEY `review_3fk` (`parent_review_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administrator`
--
ALTER TABLE `administrator`
  ADD CONSTRAINT `admin_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order_product`
--
ALTER TABLE `order_product`
  ADD CONSTRAINT `order_product_1fk` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `order_product_2fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_1fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `review_2fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `review_3fk` FOREIGN KEY (`parent_review_id`) REFERENCES `review` (`review_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

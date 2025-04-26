
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `bill_number` varchar(20) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `bills` (`id`, `bill_number`, `customer_name`, `customer_phone`, `customer_address`, `total_amount`, `gst_amount`, `discount_amount`, `final_amount`, `payment_method`, `created_at`) VALUES
(1, '202504215149', NULL, NULL, NULL, 1150.00, 207.00, 0.00, 1357.00, NULL, '2025-04-21 14:45:26'),
(2, '202504217968', NULL, NULL, NULL, 31050.00, 5589.00, 0.00, 36639.00, NULL, '2025-04-21 14:46:58'),
(3, '202504218328', NULL, NULL, NULL, 2300.00, 414.00, 0.00, 2714.00, NULL, '2025-04-21 14:49:29'),
(4, '202504215091', NULL, NULL, NULL, 14950.00, 2691.00, 0.00, 17641.00, NULL, '2025-04-21 14:51:50'),
(5, '202504211031', NULL, NULL, NULL, 19550.00, 3519.00, 0.00, 23069.00, NULL, '2025-04-21 14:54:47'),
(6, '202504221405', NULL, NULL, NULL, 11500.00, 2070.00, 0.00, 13570.00, NULL, '2025-04-22 00:39:53'),
(7, '202504228711', NULL, NULL, NULL, 4600.00, 828.00, 0.00, 5428.00, NULL, '2025-04-22 06:57:34'),
(8, '202504245001', NULL, NULL, NULL, 2250.00, 405.00, 0.00, 2655.00, NULL, '2025-04-24 12:41:38'),
(9, '202504243300', NULL, NULL, NULL, 29250.00, 5265.00, 0.00, 34515.00, NULL, '2025-04-24 12:43:00'),
(10, '202504245606', NULL, NULL, NULL, 1125.00, 202.50, 0.00, 1327.50, NULL, '2025-04-24 12:45:41'),
(11, '202504248724', NULL, NULL, NULL, 1125.00, 202.50, 0.00, 1327.50, NULL, '2025-04-24 12:46:12');


CREATE TABLE `bill_items` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `bill_items` (`id`, `bill_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 1, NULL, 1, 1150.00, 1150.00, '2025-04-21 14:45:26'),
(2, 2, NULL, 27, 1150.00, 31050.00, '2025-04-21 14:46:58'),
(3, 3, NULL, 2, 1150.00, 2300.00, '2025-04-21 14:49:29'),
(4, 4, NULL, 13, 1150.00, 14950.00, '2025-04-21 14:51:50'),
(5, 5, NULL, 17, 1150.00, 19550.00, '2025-04-21 14:54:47'),
(6, 6, NULL, 10, 1150.00, 11500.00, '2025-04-22 00:39:53'),
(7, 7, NULL, 4, 1150.00, 4600.00, '2025-04-22 06:57:34'),
(8, 8, 4, 2, 1125.00, 2250.00, '2025-04-24 12:41:38'),
(9, 9, 4, 26, 1125.00, 29250.00, '2025-04-24 12:43:00'),
(10, 10, 4, 1, 1125.00, 1125.00, '2025-04-24 12:45:41'),
(11, 11, 4, 1, 1125.00, 1125.00, '2025-04-24 12:46:12');


CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `brands` (`id`, `name`, `category_id`, `created_at`) VALUES
(1, 'rr', 1, '2025-04-21 14:43:08'),
(2, 'Havells', NULL, '2025-04-21 15:02:48'),
(3, 'Crompton', NULL, '2025-04-21 15:02:48'),
(4, 'Philips', NULL, '2025-04-21 15:02:48'),
(5, 'Anchor', NULL, '2025-04-21 15:02:48'),
(6, 'Polycab', NULL, '2025-04-21 15:02:48'),
(7, 'Syska', NULL, '2025-04-21 15:02:48'),
(8, 'Orient', NULL, '2025-04-21 15:02:48'),
(9, 'Bajaj', NULL, '2025-04-21 15:02:48'),
(10, 'Schneider', NULL, '2025-04-21 15:02:48'),
(11, 'Legrand', NULL, '2025-04-21 15:02:48'),
(12, 'V-Guard', NULL, '2025-04-21 15:02:48'),
(13, 'Wipro', NULL, '2025-04-21 15:02:48'),
(14, 'GM', NULL, '2025-04-21 15:02:48'),
(15, 'HPL', NULL, '2025-04-21 15:02:48'),
(16, 'rr', 8, '2025-04-24 12:40:18');


CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'fan', '2025-04-21 14:43:00'),
(2, 'Switches', '2025-04-21 15:01:55'),
(3, 'LED Bulbs', '2025-04-21 15:01:55'),
(4, 'Tubelights', '2025-04-21 15:01:55'),
(5, 'MCB', '2025-04-21 15:01:55'),
(6, 'Wires', '2025-04-21 15:01:55'),
(7, 'Sockets', '2025-04-21 15:01:55'),
(8, 'Fans', '2025-04-21 15:01:55'),
(9, 'Meters', '2025-04-21 15:01:55'),
(10, 'Circuit Breakers', '2025-04-21 15:01:55'),
(11, 'Conduits', '2025-04-21 15:01:55'),
(12, 'Distribution Boards', '2025-04-21 15:01:55'),
(13, 'Cables', '2025-04-21 15:01:55');


CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model_number` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `products` (`id`, `name`, `category_id`, `brand_id`, `model_number`, `quantity`, `price`, `description`, `created_at`, `updated_at`) VALUES
(4, 'rr fans', 8, 16, '12', 0, 1125.00, '', '2025-04-24 12:40:51', '2025-04-24 12:46:12');


ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_number` (`bill_number`);


ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `product_id` (`product_id`);


ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);


ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);


ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);


ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `bill_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;


ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;


ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;


ALTER TABLE `brands`
  ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;


ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;
COMMIT;

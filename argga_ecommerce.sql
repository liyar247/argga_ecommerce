-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 05:31 AM
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
-- Database: `argga_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(21, 8, 24, 1, '2026-04-30 12:37:18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `user_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `user_name`, `user_email`, `user_phone`, `user_address`, `total_amount`, `status`, `order_date`) VALUES
(1, 'ORD-20260430-DED979', 8, 'Liyar Ahmed', 'liyar11223344@gmail.com', '01758823629', 'California', 5.00, 'pending', '2026-04-30 05:16:29'),
(2, 'ORD-20260430-1335', 8, 'Liyar Ahmed', 'liyar11223344@gmail.com', '01848599651', 'Mirpur 14', 3.00, 'processing', '2026-04-30 05:18:24'),
(3, 'ORD-20260430-7476', 8, 'Liyar Ahmed', 'liyar11223344@gmail.com', '01758823629', 'Mirpur 14', 311.60, 'pending', '2026-04-30 08:00:48'),
(4, 'ORD-20260430-8947', 9, 'Aminul', 'aminul1@gmail.com', '01758823629', 'Mirpur 14', 200.00, 'pending', '2026-04-30 12:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 13, 'salain', 5.00, 1, 5.00),
(2, 2, 9, 'Shampoo', 3.00, 1, 3.00),
(3, 3, 19, 'Sergel 20mg', 60.00, 1, 60.00),
(4, 3, 22, 'Napa 500', 10.80, 2, 21.60),
(5, 3, 20, 'Monas 10', 230.00, 1, 230.00),
(6, 4, 24, 'Azmasol HFA Refill', 200.00, 1, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `discount` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 10,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `old_price`, `discount`, `image`, `category`, `stock`, `description`, `created_at`) VALUES
(9, 'Shampoo', 3.00, 4.00, '20', '1777475052_images (1).jfif', 'beauty', 100, '', '2026-04-29 15:04:12'),
(13, 'salain', 5.00, 6.00, '10', '1777520887_or.jpg', 'medicine', 10, '', '2026-04-30 03:48:07'),
(14, 'Nido', 1590.00, 1650.00, '', '1777527414_images (2).jfif', 'baby', 10, 'Baby milk', '2026-04-30 05:36:54'),
(15, 'Biomil 1', 600.00, 625.00, '', '1777530175_bio1.jfif', 'baby', 10, 'Baby milk', '2026-04-30 06:22:55'),
(16, 'Biomilk 3', 800.00, 860.00, '', '1777530234_bio3.jpg', 'baby', 10, 'Baby milk catagory 3', '2026-04-30 06:23:54'),
(17, 'Orange Face Wash', 300.00, 320.00, '', '1777530286_orange.jfif', 'healthcare', 10, '', '2026-04-30 06:24:46'),
(18, 'Garneir Men', 400.00, 430.00, '', '1777530336_man.jpg', 'healthcare', 10, '', '2026-04-30 06:25:36'),
(19, 'Sergel 20mg', 60.00, 65.00, '', '1777531035_sergel.jpg', 'medicine', 30, 'Sergel 20 (Esomeprazole 20 mg) is a proton pump inhibitor that reduces stomach acid production. It is used to relieve heartburn, acid reflux (GERD), and to treat and prevent gastric and duodenal ulcers by protecting the stomach lining and promoting healing.', '2026-04-30 06:37:15'),
(20, 'Monas 10', 230.00, 260.00, '', '1777531127_monus.jfif', 'medicine', 10, 'Monas 10 Tablet is a prescription medicine commonly used for asthma prevention and to help manage allergy symptoms such as sneezing and runny nose. It works by reducing inflammation in the airways, making breathing easier and more comfortable for individuals with asthma or allergic rhinitis. Suitable for both adults and children above a certain age, this tablet is taken once daily as directed by a healthcare professional. Monas 10 is not intended for sudden breathing problems, so patients should continue to keep a rescue inhaler for immediate relief when needed. This medicine offers a reliable option for those seeking long-term control of asthma and allergy symptoms, supporting better respiratory health and daily comfort.', '2026-04-30 06:38:47'),
(21, 'Lifebuoy Soap Bar Lemon Fresh 90g', 55.00, 60.00, '', '1777531271_lifeboy.jpeg', 'beauty', 10, 'Experience a refreshing shower every day with LIFEBUOY Lemon Fresh Soap Bar. It rejuvenates your senses keeping you fresh throughout the day. Formulated with an efficient cleansing property of lemons, this soap bar is packed with natural antibacterial properties. This disinfectant soap protects your skin from disease-causing bacteria and ensures 100% better germ protection for you and your family. The citrusy fragrance will leave you thoroughly fresh. Shop now!', '2026-04-30 06:41:11'),
(22, 'Napa 500', 10.80, 12.00, '', '1777531368_napa.jfif', 'medicine', 10, 'Napa 500 mg tablets contain paracetamol (acetaminophen) and are used for relieving various types of mild-to-moderate pain and fever. This medication is manufactured by Beximco Pharmaceuticals Ltd.', '2026-04-30 06:42:48'),
(23, 'Gavisol', 225.00, 250.00, '', '1777549512_gavisol.jfif', 'medicine', 100, 'Indication\r\n\r\nSymptomatic relief of, upset stomach or dyspepsia, associated w/ hyperacidity. Alleviates the painful conditions resulting from gastric acid & bile reflux into oesophagus. Dyspepsia, associated w/, gastric reflux, reflux oesophagitis, heartburn, hiatus hernia, flatulence associated w/ gastric reflux, heartburn of pregnancy, regurgitation & in all cases of epigastric & retrosternal distress where the underlying cause is gastric reflux.\r\nAdministration\r\n\r\nShould be taken with food: Take after meals & at bedtime.\r\nAdult Dose\r\n\r\nSuspension Adults: 10-20ml after meals and at bedtime, up to four times a day.\r\nChild Dose\r\n\r\nSuspension Children over 12 years: 10-20ml after meals and at bedtime, up to four times a day.\r\nContraindication\r\n\r\nHypersensitivity.', '2026-04-30 11:45:12'),
(24, 'Azmasol HFA Refill', 200.00, 220.00, '', '1777549636_Azmasol-Refill-2.jpg', 'medicine', 19, 'Azmasol HFA Refill belongs to a group of medicines called fast-acting bronchodilators or “relievers”. It’s used to treat the symptoms of asthma and chronic obstructive pulmonary disease (COPD) such as coughing, wheezing and feeling short of breath. You can take Azmasol HFA Refill with or without food. The dose will depend on your condition and how your respond to the medicine. Try to take it at the same time each day. It\'s important to keep taking this medicine until your doctor tells you not to. Use this medicine regularly to get the most benefit from it even if you feel well. Azmasol HFA Refill is generally safe and effective but some common side effects include tremor, headache. fast heart rate, and muscle cramps. These side effects aren\'t often dangerous and they should gradually improve as your body gets used to this medicine. There are other, rarer, side effects and you should call your doctor straight away if you get chest pain, a very bad headache or very bad dizziness. Before using Azmasol HFA Refilll, you should tell your doctor if you have high blood pressure, an overactive thyroid gland, a history of heart problems, diabetes or low levels of potassium in your blood to make sure it\'s safe. Also make sure your doctor knows if you\'re pregnant or breast-feeding before taking this medicine. Because this medicine can make you feel dizzy or shaky, don\'t drive, cycle or use tools or machinery until you feel better. And you shouldn\'t smoke. Smoking causes damage to your lungs and will make your condition worse.', '2026-04-30 11:47:16'),
(25, 'Napa Syrup', 30.00, 35.00, '', '1777549806_napa-syrup-100ml.jfif', 'medicine', 12, 'Indication\r\n\r\nFever, Mild to moderate pain, osteoarthritis, rheumatoid arthritis, chronic low back pain, Renal stone pain, neuropathic pain, toothache, migraine, postoperative mild to moderate pain.\r\nAdministration\r\n\r\nMay be taken with or without food.\r\nAdult Dose\r\n\r\nOral Mild to moderate pain and fever Tablet Adult: 1 - 2 tablets every 4 to 6 hours up to a maximum of 4 g (8 tablets) daily Extended Release (XR) Tablet Adults: 2 tablets, swallowed whole, every 6 to 8 hours (maximum of 6 tablets in any 24 hours). Syrup/Suspension: Adults: 4-8 Measuring spoonful 3-4 times daily; Rectal Suppository Adults: 500 mg-1 g every 4-6 hours to a maximum of 4 g daily.\r\nChild Dose\r\n\r\nOral Mild to moderate pain and fever Tablet Children (6 - 12 years) : 1/2 to 1 tablet 3 to 4 times daily Extended Release (XR) Tablet Children over 12 years: 2 tablets, swallowed whole, every 6 to 8 hours (maximum of 6 tablets in any 24 hours). Syrup Mild to moderate pain and fever Children: 3 months - <1 year : 60 - 120 mg (1/2 - 1 measuring spoonful), 1 - 5 years : 1 - 2 measuring spoonful 6 - 12 years : 2 - 4 measuring spoonful Children: 2 months: 60 mg (1/2 measuring spoonful) for post immunization pyrexia; Paediatric Drops Mild to moderate pain and fever Children Up to 3 months: 0.5 ml (40 mg) 4 to 11 months: 1.0 ml (80 mg) 1 to <2 years: 1.5 ml (120 mg) 2 to 3 years: 2 ml (160mg) 4 to 5 years: 3 ml (240 mg) Dose can be repeated, every 4 hours. Rectal Mild to moderate pain and fever Suppository Children: 3 months-<1 year: 60-125 mg 1-<5 years: 125-250 mg 5-12 years: 250-500 mg These doses may be repeated every 4-6 hours as necessary (maximum 4 doses in 24 hours). Children over 12 years: 500 mg-1 g every 4-6 hours to a maximum of 4 g daily. Post-immunisation pyrexia Child: 2-3 mth 60 mg. If necessary, a 2nd dose may be given after 4-6 hr.', '2026-04-30 11:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(2, 'Test User', 'test@argga.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2026-04-29 14:13:51'),
(6, 'Admin', 'admin@argga.com', 'admin123', 1, '2026-04-29 14:34:12'),
(7, 'Liyar Ahmed', 'example2@gmail.conm', '$2y$10$JO0DU8hZANdWqyxJ.B1XCOnActPjbyy.c26EnldGEagW./58e0xBi', 0, '2026-04-30 03:21:02'),
(8, 'Liyar Ahmed', 'liyar11223344@gmail.com', '$2y$10$CVwLN9Z1KmuDGdYw7QzJIuv7r6R1if2UBnpTGh7nzMfzibo/Tu0t6', 0, '2026-04-30 03:25:13'),
(9, 'Aminul', 'aminul1@gmail.com', '$2y$10$PaCe6fQnip0w9ou5yZE4OuOQWjV3MHmNsF9PoO/bpPApF7SJAC95i', 0, '2026-04-30 12:09:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

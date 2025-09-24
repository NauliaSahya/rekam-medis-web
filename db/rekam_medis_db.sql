-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 07:51 AM
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
-- Database: `rekam_medis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `encounters`
--

CREATE TABLE `encounters` (
  `encounter_id` int(11) NOT NULL,
  `registration_number` varchar(6) NOT NULL,
  `med_record_number` varchar(6) NOT NULL,
  `visit_date` date NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `vitals_systolic` int(11) DEFAULT NULL,
  `vitals_diastolic` int(11) DEFAULT NULL,
  `vitals_heart_rate` int(11) DEFAULT NULL,
  `vitals_respiratory_rate` int(11) DEFAULT NULL,
  `vitals_temperature` decimal(4,1) DEFAULT NULL,
  `vitals_oxygen_saturation` int(11) DEFAULT NULL,
  `vitals_weight` decimal(5,2) DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `disposition` text DEFAULT NULL,
  `chronic_diseases` text DEFAULT NULL,
  `pacs_orders` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `encounters`
--

INSERT INTO `encounters` (`encounter_id`, `registration_number`, `med_record_number`, `visit_date`, `diagnosis`, `vitals_systolic`, `vitals_diastolic`, `vitals_heart_rate`, `vitals_respiratory_rate`, `vitals_temperature`, `vitals_oxygen_saturation`, `vitals_weight`, `treatment`, `disposition`, `chronic_diseases`, `pacs_orders`, `created_by`, `created_at`) VALUES
(1, '000001', '000001', '2025-09-23', 'Bronchial Asthma', 125, 95, 79, 16, 98.0, 99, 44.00, 'Indomethacin 25mg Tablet, Paracetamol 125mg/5ml Suspension', 'Referred for further care', 'Asthma', 'Hip-AP, Foot-toe-AP', NULL, '2025-09-23 09:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `lab_result_id` int(11) NOT NULL,
  `encounter_id` int(11) DEFAULT NULL,
  `test_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `med_record_number` varchar(6) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `med_record_number`, `full_name`, `date_of_birth`, `gender`, `address`, `phone_number`, `created_at`) VALUES
(1, '000001', 'Dummy Dat', '1995-01-15', 'Perempuan', 'Asemrowo, Surabaya', '081234567890', '2025-09-23 09:46:41'),
(2, '000002', 'Dummy sec', '2005-03-14', 'Perempuan', 'Asemrowo', '081234567890', '2025-09-23 14:15:46'),
(3, '000003', 'Belva', '2005-01-13', 'Perempuan', 'ITS', '0822222222', '2025-09-23 14:20:28');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_stock`
--

CREATE TABLE `pharmacy_stock` (
  `drug_id` int(11) NOT NULL,
  `drug_name` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `encounter_id` int(11) DEFAULT NULL,
  `drug_name` varchar(100) NOT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `encounter_id`, `drug_name`, `dosage`, `frequency`, `duration`, `notes`, `created_at`) VALUES
(1, 1, 'Indomethacin 25mg Tablet', '1 Tablet', 'Thrice a day', '3 Day(s)', NULL, '2025-09-23 09:46:41'),
(2, 1, 'Paracetamol 125mg/5ml Suspension', '3 ml', 'Once a day', '13 Day(s)', NULL, '2025-09-23 09:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `radiology_results`
--

CREATE TABLE `radiology_results` (
  `radiology_result_id` int(11) NOT NULL,
  `encounter_id` int(11) DEFAULT NULL,
  `test_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','dokter','laboratorium','farmasi','radiologi') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$yO/QYcnf/9U7Tm90yBRTPumJZISiIydDawvjmhlc4ZTeOHrVxC/b2', 'admin', '2025-09-23 10:05:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `encounters`
--
ALTER TABLE `encounters`
  ADD PRIMARY KEY (`encounter_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `med_record_number` (`med_record_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`lab_result_id`),
  ADD KEY `encounter_id` (`encounter_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `med_record_number` (`med_record_number`);

--
-- Indexes for table `pharmacy_stock`
--
ALTER TABLE `pharmacy_stock`
  ADD PRIMARY KEY (`drug_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `encounter_id` (`encounter_id`);

--
-- Indexes for table `radiology_results`
--
ALTER TABLE `radiology_results`
  ADD PRIMARY KEY (`radiology_result_id`),
  ADD KEY `encounter_id` (`encounter_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `encounters`
--
ALTER TABLE `encounters`
  MODIFY `encounter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `lab_result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pharmacy_stock`
--
ALTER TABLE `pharmacy_stock`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `radiology_results`
--
ALTER TABLE `radiology_results`
  MODIFY `radiology_result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `encounters`
--
ALTER TABLE `encounters`
  ADD CONSTRAINT `encounters_ibfk_1` FOREIGN KEY (`med_record_number`) REFERENCES `patients` (`med_record_number`),
  ADD CONSTRAINT `encounters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `lab_results_ibfk_1` FOREIGN KEY (`encounter_id`) REFERENCES `encounters` (`encounter_id`),
  ADD CONSTRAINT `lab_results_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`encounter_id`) REFERENCES `encounters` (`encounter_id`);

--
-- Constraints for table `radiology_results`
--
ALTER TABLE `radiology_results`
  ADD CONSTRAINT `radiology_results_ibfk_1` FOREIGN KEY (`encounter_id`) REFERENCES `encounters` (`encounter_id`),
  ADD CONSTRAINT `radiology_results_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

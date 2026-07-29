-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2026 at 12:47 PM
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
-- Database: `titaclinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `parent_profile_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_type` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_profile_id` bigint(20) UNSIGNED NOT NULL,
  `patient_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `sex` varchar(255) NOT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_profiles`
--

CREATE TABLE `doctor_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `schedule_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `immunizations`
--

CREATE TABLE `immunizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_dose` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `recommended_age` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batches`
--

CREATE TABLE `inventory_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `manufacture_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `quantity_received` int(11) NOT NULL,
  `quantity_available` int(11) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `received_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `unit` varchar(255) NOT NULL,
  `minimum_stock_level` int(11) NOT NULL DEFAULT 0,
  `storage_location` varchar(255) DEFAULT NULL,
  `requires_expiration` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_immunization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `performed_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_27_000003_add_clinic_fields_to_users_table', 1),
(5, '2026_07_27_000004_create_parent_profiles_table', 1),
(6, '2026_07_27_000005_create_doctor_profiles_table', 1),
(7, '2026_07_27_000006_create_children_table', 1),
(8, '2026_07_27_000007_create_appointments_table', 1),
(9, '2026_07_27_000008_create_schedule_blocks_table', 1),
(10, '2026_07_27_000009_create_patient_visits_table', 1),
(11, '2026_07_27_000010_create_immunizations_table', 1),
(12, '2026_07_27_000011_create_inventory_items_table', 1),
(13, '2026_07_27_000012_create_inventory_batches_table', 1),
(14, '2026_07_27_000013_create_inventory_transactions_table', 1),
(15, '2026_07_27_000014_create_patient_immunizations_table', 1),
(16, '2026_07_27_000015_add_clinic_indexes_and_constraints', 1);

-- --------------------------------------------------------

--
-- Table structure for table `parent_profiles`
--

CREATE TABLE `parent_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_immunizations`
--

CREATE TABLE `patient_immunizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `immunization_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_user_id` bigint(20) UNSIGNED NOT NULL,
  `administered_at` datetime NOT NULL,
  `dose` varchar(255) DEFAULT NULL,
  `dose_number` int(11) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `administration_site` varchar(255) DEFAULT NULL,
  `next_due_date` date DEFAULT NULL,
  `reaction_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_user_id` bigint(20) UNSIGNED NOT NULL,
  `visit_date` date NOT NULL,
  `reason_for_visit` varchar(255) NOT NULL,
  `symptoms` text DEFAULT NULL,
  `height_cm` decimal(6,2) DEFAULT NULL,
  `weight_kg` decimal(6,2) DEFAULT NULL,
  `temperature_c` decimal(4,2) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `systolic_bp` int(11) DEFAULT NULL,
  `diastolic_bp` int(11) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `clinical_notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_blocks`
--

CREATE TABLE `schedule_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doctor_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `block_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_whole_day` tinyint(1) NOT NULL DEFAULT 0,
  `reason` varchar(255) DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('14bZcvO2SacgeN7Icu6CAQgzvlPycKeyh1UxLUwu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibGVNdm1BaG1sNnJ3Qk9KSXdnUTJCZmxVaGhOSXN4aGtRU3dIM3QydyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWdpc3RlciI7czo1OiJyb3V0ZSI7czo4OiJyZWdpc3RlciI7fX0=', 1785321779),
('rTySSsbIvmXWm5D1F5C7e26eFjbv1CGWsmHxxD21', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiakxpSjFSMVBWS0syZUlNbkdVeml3akZqNm1sa0Vsbm5aOW5hc0NyciI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3VzZXJzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91c2VycyI7czo1OiJyb3V0ZSI7czoxMToiYWRtaW4udXNlcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1785319347);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'parent',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `name`, `email`, `phone_number`, `role`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Clinic', NULL, 'Administrator', NULL, 'admin@titaclinic.test', NULL, 'admin', 'active', NULL, '$2y$12$RK66HMIHkvi.KsY4bp8KYu7SmFElSUtDmRKfGPAFaPR9WI0lmy58W', NULL, '2026-07-28 22:57:44', '2026-07-28 22:57:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `appointments_child_id_appointment_date_index` (`child_id`,`appointment_date`),
  ADD KEY `appointments_parent_profile_id_appointment_date_index` (`parent_profile_id`,`appointment_date`),
  ADD KEY `appointments_doctor_user_id_appointment_date_index` (`doctor_user_id`,`appointment_date`),
  ADD KEY `appointments_appointment_date_status_index` (`appointment_date`,`status`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `children_patient_number_unique` (`patient_number`),
  ADD KEY `children_parent_profile_id_status_index` (`parent_profile_id`,`status`),
  ADD KEY `children_last_name_birth_date_index` (`last_name`,`birth_date`);

--
-- Indexes for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_profiles_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `doctor_profiles_license_number_unique` (`license_number`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `immunizations`
--
ALTER TABLE `immunizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `immunizations_type_is_active_index` (`type`,`is_active`);

--
-- Indexes for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_batches_inventory_item_id_status_index` (`inventory_item_id`,`status`),
  ADD KEY `inventory_batches_expiration_date_status_index` (`expiration_date`,`status`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_items_sku_unique` (`sku`),
  ADD KEY `inventory_items_category_is_active_index` (`category`,`is_active`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_transactions_inventory_batch_id_foreign` (`inventory_batch_id`),
  ADD KEY `inventory_transactions_performed_by_user_id_foreign` (`performed_by_user_id`),
  ADD KEY `inventory_transactions_inventory_item_id_transaction_date_index` (`inventory_item_id`,`transaction_date`),
  ADD KEY `inventory_transactions_transaction_type_transaction_date_index` (`transaction_type`,`transaction_date`),
  ADD KEY `inventory_transactions_patient_immunization_id_index` (`patient_immunization_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent_profiles`
--
ALTER TABLE `parent_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parent_profiles_user_id_unique` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patient_immunizations`
--
ALTER TABLE `patient_immunizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_immunizations_inventory_batch_id_foreign` (`inventory_batch_id`),
  ADD KEY `patient_immunizations_doctor_user_id_foreign` (`doctor_user_id`),
  ADD KEY `patient_immunizations_child_id_administered_at_index` (`child_id`,`administered_at`),
  ADD KEY `patient_immunizations_immunization_id_administered_at_index` (`immunization_id`,`administered_at`),
  ADD KEY `patient_immunizations_next_due_date_index` (`next_due_date`);

--
-- Indexes for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_visits_appointment_id_foreign` (`appointment_id`),
  ADD KEY `patient_visits_child_id_visit_date_index` (`child_id`,`visit_date`),
  ADD KEY `patient_visits_doctor_user_id_visit_date_index` (`doctor_user_id`,`visit_date`);

--
-- Indexes for table `schedule_blocks`
--
ALTER TABLE `schedule_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_blocks_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `schedule_blocks_doctor_user_id_block_date_index` (`doctor_user_id`,`block_date`),
  ADD KEY `schedule_blocks_block_date_is_whole_day_index` (`block_date`,`is_whole_day`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_role_status_index` (`role`,`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `immunizations`
--
ALTER TABLE `immunizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `parent_profiles`
--
ALTER TABLE `parent_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_immunizations`
--
ALTER TABLE `patient_immunizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_blocks`
--
ALTER TABLE `schedule_blocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_doctor_user_id_foreign` FOREIGN KEY (`doctor_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_parent_profile_id_foreign` FOREIGN KEY (`parent_profile_id`) REFERENCES `parent_profiles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_parent_profile_id_foreign` FOREIGN KEY (`parent_profile_id`) REFERENCES `parent_profiles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  ADD CONSTRAINT `doctor_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD CONSTRAINT `inventory_batches_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_inventory_batch_id_foreign` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transactions_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transactions_patient_immunization_id_foreign` FOREIGN KEY (`patient_immunization_id`) REFERENCES `patient_immunizations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transactions_performed_by_user_id_foreign` FOREIGN KEY (`performed_by_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `parent_profiles`
--
ALTER TABLE `parent_profiles`
  ADD CONSTRAINT `parent_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `patient_immunizations`
--
ALTER TABLE `patient_immunizations`
  ADD CONSTRAINT `patient_immunizations_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_immunizations_doctor_user_id_foreign` FOREIGN KEY (`doctor_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_immunizations_immunization_id_foreign` FOREIGN KEY (`immunization_id`) REFERENCES `immunizations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_immunizations_inventory_batch_id_foreign` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `patient_visits_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_visits_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_visits_doctor_user_id_foreign` FOREIGN KEY (`doctor_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `schedule_blocks`
--
ALTER TABLE `schedule_blocks`
  ADD CONSTRAINT `schedule_blocks_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `schedule_blocks_doctor_user_id_foreign` FOREIGN KEY (`doctor_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

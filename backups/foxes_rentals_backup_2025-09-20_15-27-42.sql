-- MySQL dump 10.13  Distrib 8.4.4, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: foxes_rentals
-- ------------------------------------------------------
-- Server version	8.4.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_one` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_two` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addressable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addressable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_addressable_type_addressable_id_index` (`addressable_type`,`addressable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_keys` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_type` enum('api_key','secret','token','webhook_url','client_id','client_secret','public_key','private_key') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api_key',
  `environment` enum('production','staging','development') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'development',
  `encrypted_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `last_used_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `rate_limit` int DEFAULT NULL,
  `allowed_ips` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_keys_last_used_by_foreign` (`last_used_by`),
  KEY `api_keys_created_by_foreign` (`created_by`),
  KEY `api_keys_service_name_environment_index` (`service_name`,`environment`),
  KEY `api_keys_is_active_expires_at_index` (`is_active`,`expires_at`),
  KEY `api_keys_last_used_at_index` (`last_used_at`),
  KEY `api_keys_service_name_index` (`service_name`),
  KEY `api_keys_is_active_index` (`is_active`),
  CONSTRAINT `api_keys_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `api_keys_last_used_by_foreign` FOREIGN KEY (`last_used_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `automation_executions`
--

DROP TABLE IF EXISTS `automation_executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automation_executions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `automation_rule_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `execution_time_ms` int unsigned DEFAULT NULL,
  `trigger_data` json DEFAULT NULL,
  `action_data` json DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `execution_log` json DEFAULT NULL,
  `affected_records_count` int unsigned DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_executions_automation_rule_id_status_index` (`automation_rule_id`,`status`),
  KEY `automation_executions_status_started_at_index` (`status`,`started_at`),
  KEY `automation_executions_automation_rule_id_started_at_index` (`automation_rule_id`,`started_at`),
  KEY `automation_executions_started_at_index` (`started_at`),
  KEY `automation_executions_completed_at_index` (`completed_at`),
  KEY `automation_executions_created_by_index` (`created_by`),
  KEY `automation_executions_status_index` (`status`),
  CONSTRAINT `automation_executions_automation_rule_id_foreign` FOREIGN KEY (`automation_rule_id`) REFERENCES `automation_rules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `automation_executions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automation_executions`
--

LOCK TABLES `automation_executions` WRITE;
/*!40000 ALTER TABLE `automation_executions` DISABLE KEYS */;
/*!40000 ALTER TABLE `automation_executions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `automation_rules`
--

DROP TABLE IF EXISTS `automation_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automation_rules` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `rule_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_conditions` json DEFAULT NULL,
  `action_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_parameters` json DEFAULT NULL,
  `target_conditions` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int NOT NULL DEFAULT '5',
  `execution_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_executed_at` timestamp NULL DEFAULT NULL,
  `next_execution_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_rules_created_by_foreign` (`created_by`),
  KEY `automation_rules_rule_type_is_active_index` (`rule_type`,`is_active`),
  KEY `automation_rules_trigger_type_is_active_index` (`trigger_type`,`is_active`),
  KEY `automation_rules_action_type_is_active_index` (`action_type`,`is_active`),
  KEY `automation_rules_is_active_next_execution_at_index` (`is_active`,`next_execution_at`),
  KEY `automation_rules_priority_is_active_index` (`priority`,`is_active`),
  KEY `automation_rules_rule_type_index` (`rule_type`),
  KEY `automation_rules_trigger_type_index` (`trigger_type`),
  KEY `automation_rules_action_type_index` (`action_type`),
  KEY `automation_rules_is_active_index` (`is_active`),
  KEY `automation_rules_priority_index` (`priority`),
  CONSTRAINT `automation_rules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automation_rules`
--

LOCK TABLES `automation_rules` WRITE;
/*!40000 ALTER TABLE `automation_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `automation_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c2b_requests`
--

DROP TABLE IF EXISTS `c2b_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `c2b_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `TransactionType` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TransID` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TransTime` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TransAmount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BusinessShortCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BillRefNumber` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `InvoiceNumber` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OrgAccountBalance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ThirdPartyTransID` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MSISDN` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reconciliation_status` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c2b_requests`
--

LOCK TABLES `c2b_requests` WRITE;
/*!40000 ALTER TABLE `c2b_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `c2b_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_invoices`
--

DROP TABLE IF EXISTS `custom_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_invoices` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_invoices_landlord_id_foreign` (`landlord_id`),
  KEY `custom_invoices_property_id_foreign` (`property_id`),
  KEY `custom_invoices_house_id_foreign` (`house_id`),
  CONSTRAINT `custom_invoices_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `custom_invoices_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `custom_invoices_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_invoices`
--

LOCK TABLES `custom_invoices` WRITE;
/*!40000 ALTER TABLE `custom_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposits`
--

DROP TABLE IF EXISTS `deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deposits` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `lease_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_amount` decimal(8,2) DEFAULT NULL,
  `refund_date` date DEFAULT NULL,
  `refund_paid` tinyint(1) NOT NULL DEFAULT '0',
  `refund_receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposits_lease_id_foreign` (`lease_id`),
  KEY `deposits_tenant_id_foreign` (`tenant_id`),
  KEY `deposits_status_index` (`status`),
  CONSTRAINT `deposits_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deposits_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `environment_settings`
--

DROP TABLE IF EXISTS `environment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `environment_settings` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `environment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `environment_settings_environment_setting_key_unique` (`environment`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `environment_settings`
--

LOCK TABLES `environment_settings` WRITE;
/*!40000 ALTER TABLE `environment_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `environment_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_types`
--

DROP TABLE IF EXISTS `expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_types` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_types`
--

LOCK TABLES `expense_types` WRITE;
/*!40000 ALTER TABLE `expense_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `expense_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `incurred_on` date NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_type_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_type_id_foreign` (`expense_type_id`),
  KEY `expenses_landlord_id_foreign` (`landlord_id`),
  KEY `expenses_property_id_foreign` (`property_id`),
  KEY `expenses_house_id_foreign` (`house_id`),
  CONSTRAINT `expenses_expense_type_id_foreign` FOREIGN KEY (`expense_type_id`) REFERENCES `expense_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `house_types`
--

DROP TABLE IF EXISTS `house_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `house_types` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_types`
--

LOCK TABLES `house_types` WRITE;
/*!40000 ALTER TABLE `house_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `house_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `houses`
--

DROP TABLE IF EXISTS `houses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `houses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rent` decimal(16,2) NOT NULL,
  `deposit` decimal(16,2) DEFAULT NULL,
  `is_vacant` tinyint(1) NOT NULL DEFAULT '1',
  `status` smallint NOT NULL DEFAULT '0',
  `property_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission` decimal(16,2) NOT NULL,
  `electricity_id` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `houses_property_id_foreign` (`property_id`),
  KEY `houses_landlord_id_foreign` (`landlord_id`),
  KEY `houses_status_index` (`status`),
  CONSTRAINT `houses_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`),
  CONSTRAINT `houses_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `houses`
--

LOCK TABLES `houses` WRITE;
/*!40000 ALTER TABLE `houses` DISABLE KEYS */;
/*!40000 ALTER TABLE `houses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `identity_documents`
--

DROP TABLE IF EXISTS `identity_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `identity_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `identity_documents_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `identity_documents_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `identity_documents`
--

LOCK TABLES `identity_documents` WRITE;
/*!40000 ALTER TABLE `identity_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `identity_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `custom_invoice_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_scanned_copy` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_custom_invoice_id_foreign` (`custom_invoice_id`),
  CONSTRAINT `invoice_items_custom_invoice_id_foreign` FOREIGN KEY (`custom_invoice_id`) REFERENCES `custom_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lease_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `bills_amount` decimal(16,2) DEFAULT NULL,
  `paid_amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `bills` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_tenant_id_foreign` (`tenant_id`),
  KEY `invoices_property_id_foreign` (`property_id`),
  KEY `invoices_house_id_foreign` (`house_id`),
  KEY `invoices_status_index` (`status`),
  KEY `invoices_status_tenant_id_index` (`status`,`tenant_id`),
  CONSTRAINT `invoices_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landlord_remittances`
--

DROP TABLE IF EXISTS `landlord_remittances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landlord_remittances` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_on` date NOT NULL,
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `payment_receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landlord_remittances_landlord_id_foreign` (`landlord_id`),
  CONSTRAINT `landlord_remittances_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landlord_remittances`
--

LOCK TABLES `landlord_remittances` WRITE;
/*!40000 ALTER TABLE `landlord_remittances` DISABLE KEYS */;
/*!40000 ALTER TABLE `landlord_remittances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lease_bills`
--

DROP TABLE IF EXISTS `lease_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_bills` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `lease_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lease_bills_lease_id_foreign` (`lease_id`),
  CONSTRAINT `lease_bills_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lease_bills`
--

LOCK TABLES `lease_bills` WRITE;
/*!40000 ALTER TABLE `lease_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `lease_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lease_documents`
--

DROP TABLE IF EXISTS `lease_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_documents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lease_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lease_documents_lease_id_foreign` (`lease_id`),
  CONSTRAINT `lease_documents_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lease_documents`
--

LOCK TABLES `lease_documents` WRITE;
/*!40000 ALTER TABLE `lease_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `lease_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lease_properties`
--

DROP TABLE IF EXISTS `lease_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_properties` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `property_type_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lease_amount` decimal(10,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','pending','sold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `lease_duration_months` int NOT NULL DEFAULT '12',
  `minimum_lease_period` int DEFAULT NULL,
  `maximum_lease_period` int DEFAULT NULL,
  `renewal_terms` text COLLATE utf8mb4_unicode_ci,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `year_built` int DEFAULT NULL,
  `property_size` decimal(10,2) DEFAULT NULL,
  `bedrooms` int DEFAULT NULL,
  `bathrooms` int DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `applications_count` int NOT NULL DEFAULT '0',
  `lease_terms` json DEFAULT NULL,
  `special_conditions` json DEFAULT NULL,
  `marketing_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lease_properties_property_type_id_foreign` (`property_type_id`),
  KEY `lease_properties_landlord_id_foreign` (`landlord_id`),
  CONSTRAINT `lease_properties_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lease_properties_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lease_properties`
--

LOCK TABLES `lease_properties` WRITE;
/*!40000 ALTER TABLE `lease_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `lease_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lease_templates`
--

DROP TABLE IF EXISTS `lease_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lease_templates` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `template_type` enum('residential','commercial','short_term','long_term','monthly','weekly','daily','vacation','student','senior') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `terms` json DEFAULT NULL,
  `variables` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lease_templates_name_unique` (`name`),
  KEY `lease_templates_template_type_is_active_index` (`template_type`,`is_active`),
  KEY `lease_templates_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `lease_templates_template_type_index` (`template_type`),
  KEY `lease_templates_is_active_index` (`is_active`),
  KEY `lease_templates_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lease_templates`
--

LOCK TABLES `lease_templates` WRITE;
/*!40000 ALTER TABLE `lease_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `lease_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leases`
--

DROP TABLE IF EXISTS `leases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leases` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lease_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rent` decimal(12,2) NOT NULL,
  `status` enum('active','expired','terminated','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `termination_date_notice` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `rent_cycle` smallint unsigned NOT NULL DEFAULT '1',
  `invoice_generation_day` int NOT NULL DEFAULT '28',
  `next_billing_date` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leases_lease_id_unique` (`lease_id`),
  KEY `leases_property_id_foreign` (`property_id`),
  KEY `leases_house_id_foreign` (`house_id`),
  KEY `leases_tenant_id_foreign` (`tenant_id`),
  KEY `leases_status_tenant_id_index` (`status`,`tenant_id`),
  KEY `leases_status_property_id_index` (`status`,`property_id`),
  KEY `leases_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `leases_next_billing_date_index` (`next_billing_date`),
  CONSTRAINT `leases_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leases_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leases_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leases`
--

LOCK TABLES `leases` WRITE;
/*!40000 ALTER TABLE `leases` DISABLE KEYS */;
/*!40000 ALTER TABLE `leases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_activities`
--

DROP TABLE IF EXISTS `login_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_activities` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_at` timestamp NOT NULL,
  `logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `login_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_activities`
--

LOCK TABLES `login_activities` WRITE;
/*!40000 ALTER TABLE `login_activities` DISABLE KEYS */;
INSERT INTO `login_activities` VALUES ('9fe83769-24f1-40fc-8ff9-ffea7477ace9','9fe836c4-0572-4911-a5ac-d7d1a3477e40','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','WebKit','Chrome','Windows','10.0','140.0.0.0',NULL,'2025-09-18 12:05:08',NULL,'2025-09-18 12:05:08','2025-09-18 12:05:08'),('9fea3ea4-40ad-4fb8-bccf-66d55a44a1c4','9fe836d2-467b-4cb0-8ac5-c1ba0f90cfb5','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','WebKit','Chrome','Windows','10.0','140.0.0.0',NULL,'2025-09-19 12:16:58',NULL,'2025-09-19 12:17:02','2025-09-19 12:17:02');
/*!40000 ALTER TABLE `login_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2017_08_24_000000_create_settings_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2022_10_17_114813_create_permission_tables',1),(7,'2022_10_21_135114_create_properties_table',1),(8,'2022_10_21_135158_create_houses_table',1),(9,'2022_10_21_135218_create_addresses_table',1),(10,'2022_10_22_190244_create_leases_table',1),(11,'2022_10_22_191024_create_lease_bills_table',1),(12,'2022_10_22_191025_create_lease_documents_table',1),(13,'2022_10_22_204619_create_invoices_table',1),(14,'2022_10_24_085146_create_payments_table',1),(15,'2022_10_25_013743_create_house_types_table',1),(16,'2022_10_25_013832_create_property_types_table',1),(17,'2022_11_20_093420_create_expense_types_table',1),(18,'2022_11_20_093421_create_expenses_table',1),(19,'2022_12_24_110034_create_deposits_table',1),(20,'2022_12_25_193313_create_overpayments_table',1),(21,'2022_12_26_171615_create_landlord_remittances_table',1),(22,'2022_12_26_184523_create_payment_methods_table',1),(23,'2022_12_26_210558_create_notifications_table',1),(24,'2022_12_27_011458_create_support_tickets_table',1),(25,'2022_12_27_011718_create_ticket_replies_table',1),(26,'2022_12_27_011745_create_ticket_attachments_table',1),(27,'2022_12_27_020041_create_ticket_counts_table',1),(28,'2022_12_30_035435_create_login_activities_table',1),(29,'2022_12_31_191912_create_vouchers_table',1),(30,'2022_12_31_192412_create_voucher_documents_table',1),(31,'2022_12_31_192620_create_voucher_items_table',1),(32,'2022_12_31_194746_create_custom_invoices_table',1),(33,'2022_12_31_194805_create_invoice_items_table',1),(34,'2023_01_03_002932_create_activity_log_table',1),(35,'2023_01_03_002933_add_event_column_to_activity_log_table',1),(36,'2023_01_03_002934_add_batch_uuid_column_to_activity_log_table',1),(37,'2023_02_04_145508_create_identity_documents_table',1),(38,'2023_02_05_171859_add_preferred_locale_to_users_table',1),(39,'2023_06_03_230230_create_c2b_requests_table',1),(40,'2023_06_03_230243_create_stk_requests_table',1),(41,'2023_06_09_003830_add_reconciliation_status_to_c2b_requests_table',1),(42,'2023_06_09_131325_add_lease_reference_to_invoices_table',1),(43,'2024_01_15_000001_create_property_types_table',1),(44,'2024_01_15_000002_create_rental_properties_table',1),(45,'2024_01_15_000003_create_sale_properties_table',1),(46,'2024_01_15_000004_create_rental_units_table',1),(47,'2024_01_15_000005_create_property_inquiries_table',1),(48,'2024_09_08_100038_exit',1),(49,'2024_12_19_000001_create_properties_consolidated_table',1),(50,'2025_01_27_000001_create_api_keys_table',1),(51,'2025_01_27_000003_create_property_amenities_table',1),(52,'2025_01_27_000004_create_pricing_rules_table',1),(53,'2025_01_27_000005_create_lease_templates_table',1),(54,'2025_01_27_000006_create_property_amenities_pivot_table',1),(55,'2025_01_27_000008_create_property_pricing_rules_table',1),(56,'2025_01_27_000009_create_pricing_rule_applications_table',1),(57,'2025_01_27_000010_create_user_profiles_table',1),(58,'2025_01_27_000012_create_user_activities_table',1),(59,'2025_01_27_000013_create_automation_rules_table',1),(60,'2025_01_27_000014_create_automation_executions_table',1),(61,'2025_01_27_000015_create_system_metrics_table',1),(62,'2025_01_27_000016_create_system_alerts_table',1),(63,'2025_01_27_000017_create_report_templates_table',1),(64,'2025_01_27_000018_create_scheduled_reports_table',1),(65,'2025_09_10_145513_add_deleted_at_to_houses_table',1),(66,'2025_09_10_163940_add_user_id_and_invoice_id_to_stk_requests_table',1),(67,'2025_09_10_181556_create_sessions_table',1),(68,'2025_09_10_183434_add_soft_deletes_to_properties_table',1),(69,'2025_09_10_214626_add_verification_fields_to_payments_table',1),(70,'2025_09_10_223135_update_stk_requests_detailed_status',1),(71,'2025_09_18_001606_create_lease_properties_table',1),(72,'2025_09_18_114250_create_enhanced_settings_structure',1),(74,'2024_01_15_000001_create_user_invitations_table',2),(75,'2024_01_15_000002_create_password_history_table',2),(76,'2024_01_15_000003_add_missing_columns_to_users_table',2),(77,'2025_09_18_115134_add_order_index_to_settings_items_table',2),(78,'2025_09_19_151813_add_status_to_leases_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (4,'App\\Models\\User','9fe836c4-0572-4911-a5ac-d7d1a3477e40'),(4,'App\\Models\\User','9fe836d2-467b-4cb0-8ac5-c1ba0f90cfb5'),(4,'App\\Models\\User','9fe836f2-af66-49c7-9fde-97f5c51a8ab2'),(3,'App\\Models\\User','9fe836f2-dbb8-4d38-bf6f-205c04e8cf3f');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overpayments`
--

DROP TABLE IF EXISTS `overpayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `overpayments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overpayments_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `overpayments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overpayments`
--

LOCK TABLES `overpayments` WRITE;
/*!40000 ALTER TABLE `overpayments` DISABLE KEYS */;
/*!40000 ALTER TABLE `overpayments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `password_history_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `password_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_history`
--

LOCK TABLES `password_history` WRITE;
/*!40000 ALTER TABLE `password_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission` decimal(16,2) NOT NULL,
  `recorded_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_landlord_id_foreign` (`landlord_id`),
  KEY `payments_property_id_foreign` (`property_id`),
  KEY `payments_house_id_foreign` (`house_id`),
  KEY `payments_recorded_by_foreign` (`recorded_by`),
  KEY `payments_status_index` (`status`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payments_status_created_at_index` (`status`,`created_at`),
  KEY `payments_payment_method_status_index` (`payment_method`,`status`),
  KEY `payments_reference_number_index` (`reference_number`),
  KEY `payments_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `payments_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricing_rule_applications`
--

DROP TABLE IF EXISTS `pricing_rule_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pricing_rule_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pricing_rule_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_amount` decimal(10,2) NOT NULL,
  `calculated_amount` decimal(10,2) NOT NULL,
  `context` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pricing_rule_applications_pricing_rule_id_created_at_index` (`pricing_rule_id`,`created_at`),
  KEY `pricing_rule_applications_applicable_id_applicable_type_index` (`applicable_id`,`applicable_type`),
  KEY `pricing_rule_applications_created_at_index` (`created_at`),
  CONSTRAINT `pricing_rule_applications_pricing_rule_id_foreign` FOREIGN KEY (`pricing_rule_id`) REFERENCES `pricing_rules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricing_rule_applications`
--

LOCK TABLES `pricing_rule_applications` WRITE;
/*!40000 ALTER TABLE `pricing_rule_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `pricing_rule_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricing_rules`
--

DROP TABLE IF EXISTS `pricing_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pricing_rules` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `rule_type` enum('commission','late_fee','deposit','renewal_fee','maintenance_fee','processing_fee','utility_fee','parking_fee','pet_fee','cleaning_fee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `conditions` json DEFAULT NULL,
  `calculation_method` enum('percentage','fixed_amount','sliding_scale','per_square_foot','per_unit','per_room') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pricing_rules_name_unique` (`name`),
  KEY `pricing_rules_rule_type_is_active_index` (`rule_type`,`is_active`),
  KEY `pricing_rules_calculation_method_is_active_index` (`calculation_method`,`is_active`),
  KEY `pricing_rules_rule_type_index` (`rule_type`),
  KEY `pricing_rules_calculation_method_index` (`calculation_method`),
  KEY `pricing_rules_is_active_index` (`is_active`),
  KEY `pricing_rules_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricing_rules`
--

LOCK TABLES `pricing_rules` WRITE;
/*!40000 ALTER TABLE `pricing_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `pricing_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `properties` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_multi_unit` tinyint(1) NOT NULL DEFAULT '0',
  `rent` decimal(16,2) DEFAULT NULL,
  `deposit` decimal(16,2) DEFAULT NULL,
  `is_vacant` tinyint(1) NOT NULL DEFAULT '1',
  `status` smallint NOT NULL DEFAULT '0',
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commission` decimal(16,2) NOT NULL,
  `electricity_id` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `properties_landlord_id_foreign` (`landlord_id`),
  KEY `properties_status_index` (`status`),
  KEY `properties_status_landlord_id_index` (`status`,`landlord_id`),
  KEY `properties_is_vacant_status_index` (`is_vacant`,`status`),
  KEY `properties_rent_index` (`rent`),
  KEY `properties_created_at_index` (`created_at`),
  CONSTRAINT `properties_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties_consolidated`
--

DROP TABLE IF EXISTS `properties_consolidated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `properties_consolidated` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `property_type_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_subtype` enum('rental','sale','lease') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rental',
  `base_amount` decimal(16,2) NOT NULL,
  `deposit_amount` decimal(16,2) DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','maintenance','sold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_vacant` tinyint(1) NOT NULL DEFAULT '1',
  `is_multi_unit` tinyint(1) NOT NULL DEFAULT '0',
  `total_units` int NOT NULL DEFAULT '1',
  `available_units` int NOT NULL DEFAULT '1',
  `electricity_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `furnished` tinyint(1) NOT NULL DEFAULT '0',
  `pet_friendly` tinyint(1) NOT NULL DEFAULT '0',
  `smoking_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `parking_spaces` int NOT NULL DEFAULT '0',
  `balcony` tinyint(1) NOT NULL DEFAULT '0',
  `garden` tinyint(1) NOT NULL DEFAULT '0',
  `swimming_pool` tinyint(1) NOT NULL DEFAULT '0',
  `gym` tinyint(1) NOT NULL DEFAULT '0',
  `security` tinyint(1) NOT NULL DEFAULT '0',
  `elevator` tinyint(1) NOT NULL DEFAULT '0',
  `air_conditioning` tinyint(1) NOT NULL DEFAULT '0',
  `heating` tinyint(1) NOT NULL DEFAULT '0',
  `internet` tinyint(1) NOT NULL DEFAULT '0',
  `cable_tv` tinyint(1) NOT NULL DEFAULT '0',
  `laundry` tinyint(1) NOT NULL DEFAULT '0',
  `dishwasher` tinyint(1) NOT NULL DEFAULT '0',
  `microwave` tinyint(1) NOT NULL DEFAULT '0',
  `refrigerator` tinyint(1) NOT NULL DEFAULT '0',
  `stove` tinyint(1) NOT NULL DEFAULT '0',
  `oven` tinyint(1) NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `floor_plan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `virtual_tour` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `year_built` int DEFAULT NULL,
  `last_renovated` int DEFAULT NULL,
  `property_size` decimal(10,2) DEFAULT NULL,
  `lot_size` decimal(10,2) DEFAULT NULL,
  `bedrooms` int NOT NULL DEFAULT '0',
  `bathrooms` int NOT NULL DEFAULT '0',
  `living_rooms` int NOT NULL DEFAULT '0',
  `kitchens` int NOT NULL DEFAULT '0',
  `dining_rooms` int NOT NULL DEFAULT '0',
  `storage_rooms` int NOT NULL DEFAULT '0',
  `garage_spaces` int NOT NULL DEFAULT '0',
  `outdoor_spaces` int NOT NULL DEFAULT '0',
  `utilities_included` json DEFAULT NULL,
  `maintenance_responsibility` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lease_terms` json DEFAULT NULL,
  `minimum_lease_period` int DEFAULT NULL,
  `maximum_lease_period` int DEFAULT NULL,
  `notice_period` int NOT NULL DEFAULT '30',
  `late_fee_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `late_fee_fixed` decimal(10,2) NOT NULL DEFAULT '0.00',
  `returned_check_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `early_termination_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `renewal_terms` json DEFAULT NULL,
  `special_conditions` json DEFAULT NULL,
  `marketing_description` text COLLATE utf8mb4_unicode_ci,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `seo_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `applications_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `properties_consolidated_property_subtype_status_index` (`property_subtype`,`status`),
  KEY `properties_consolidated_property_type_id_index` (`property_type_id`),
  KEY `properties_consolidated_landlord_id_index` (`landlord_id`),
  KEY `properties_consolidated_is_featured_is_published_index` (`is_featured`,`is_published`),
  KEY `properties_consolidated_base_amount_index` (`base_amount`),
  KEY `properties_consolidated_bedrooms_bathrooms_index` (`bedrooms`,`bathrooms`),
  KEY `properties_consolidated_is_vacant_status_index` (`is_vacant`,`status`),
  KEY `properties_consolidated_latitude_longitude_index` (`latitude`,`longitude`),
  CONSTRAINT `properties_consolidated_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `properties_consolidated_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties_consolidated`
--

LOCK TABLES `properties_consolidated` WRITE;
/*!40000 ALTER TABLE `properties_consolidated` DISABLE KEYS */;
/*!40000 ALTER TABLE `properties_consolidated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_amenities`
--

DROP TABLE IF EXISTS `property_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_amenities` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_chargeable` tinyint(1) NOT NULL DEFAULT '0',
  `default_cost` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_amenities_name_unique` (`name`),
  KEY `property_amenities_category_sort_order_index` (`category`,`sort_order`),
  KEY `property_amenities_is_active_is_chargeable_index` (`is_active`,`is_chargeable`),
  KEY `property_amenities_category_index` (`category`),
  KEY `property_amenities_is_active_index` (`is_active`),
  KEY `property_amenities_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_amenities`
--

LOCK TABLES `property_amenities` WRITE;
/*!40000 ALTER TABLE `property_amenities` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_details`
--

DROP TABLE IF EXISTS `property_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_details` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_details_property_id_detail_type_index` (`property_id`,`detail_type`),
  CONSTRAINT `property_details_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties_consolidated` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_details`
--

LOCK TABLES `property_details` WRITE;
/*!40000 ALTER TABLE `property_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_inquiries`
--

DROP TABLE IF EXISTS `property_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_inquiries` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inquirer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inquirer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inquirer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','in_progress','qualified','unqualified','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_to` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response` text COLLATE utf8mb4_unicode_ci,
  `response_date` timestamp NULL DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_qualified` tinyint(1) NOT NULL DEFAULT '0',
  `budget_range` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `special_requirements` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_inquiries_status_priority_index` (`status`,`priority`),
  KEY `property_inquiries_property_id_property_type_index` (`property_id`,`property_type`),
  KEY `property_inquiries_assigned_to_index` (`assigned_to`),
  KEY `property_inquiries_is_qualified_index` (`is_qualified`),
  CONSTRAINT `property_inquiries_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_inquiries`
--

LOCK TABLES `property_inquiries` WRITE;
/*!40000 ALTER TABLE `property_inquiries` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_pricing_rules`
--

DROP TABLE IF EXISTS `property_pricing_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_pricing_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_property_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pricing_rule_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied_value` decimal(10,2) NOT NULL,
  `applied_at` timestamp NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_pricing_rules_pricing_rule_id_applied_at_index` (`pricing_rule_id`,`applied_at`),
  KEY `property_pricing_rules_rental_property_id_pricing_rule_id_index` (`rental_property_id`,`pricing_rule_id`),
  CONSTRAINT `property_pricing_rules_pricing_rule_id_foreign` FOREIGN KEY (`pricing_rule_id`) REFERENCES `pricing_rules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_pricing_rules_rental_property_id_foreign` FOREIGN KEY (`rental_property_id`) REFERENCES `rental_properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_pricing_rules`
--

LOCK TABLES `property_pricing_rules` WRITE;
/*!40000 ALTER TABLE `property_pricing_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_pricing_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_types`
--

DROP TABLE IF EXISTS `property_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_types` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` enum('residential','commercial','industrial','land') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_types`
--

LOCK TABLES `property_types` WRITE;
/*!40000 ALTER TABLE `property_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_properties`
--

DROP TABLE IF EXISTS `rental_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_properties` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `property_type_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rent_amount` decimal(16,2) NOT NULL,
  `deposit_amount` decimal(16,2) DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `electricity_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_vacant` tinyint(1) NOT NULL DEFAULT '1',
  `is_multi_unit` tinyint(1) NOT NULL DEFAULT '0',
  `total_units` int NOT NULL DEFAULT '1',
  `available_units` int NOT NULL DEFAULT '1',
  `furnished` tinyint(1) NOT NULL DEFAULT '0',
  `pet_friendly` tinyint(1) NOT NULL DEFAULT '0',
  `smoking_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `parking_spaces` int NOT NULL DEFAULT '0',
  `balcony` tinyint(1) NOT NULL DEFAULT '0',
  `garden` tinyint(1) NOT NULL DEFAULT '0',
  `swimming_pool` tinyint(1) NOT NULL DEFAULT '0',
  `gym` tinyint(1) NOT NULL DEFAULT '0',
  `security` tinyint(1) NOT NULL DEFAULT '0',
  `elevator` tinyint(1) NOT NULL DEFAULT '0',
  `air_conditioning` tinyint(1) NOT NULL DEFAULT '0',
  `heating` tinyint(1) NOT NULL DEFAULT '0',
  `internet` tinyint(1) NOT NULL DEFAULT '0',
  `cable_tv` tinyint(1) NOT NULL DEFAULT '0',
  `laundry` tinyint(1) NOT NULL DEFAULT '0',
  `dishwasher` tinyint(1) NOT NULL DEFAULT '0',
  `microwave` tinyint(1) NOT NULL DEFAULT '0',
  `refrigerator` tinyint(1) NOT NULL DEFAULT '0',
  `stove` tinyint(1) NOT NULL DEFAULT '0',
  `oven` tinyint(1) NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `floor_plan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `virtual_tour` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `year_built` int DEFAULT NULL,
  `last_renovated` int DEFAULT NULL,
  `property_size` decimal(10,2) DEFAULT NULL,
  `lot_size` decimal(10,2) DEFAULT NULL,
  `bedrooms` int NOT NULL DEFAULT '0',
  `bathrooms` int NOT NULL DEFAULT '0',
  `living_rooms` int NOT NULL DEFAULT '0',
  `kitchens` int NOT NULL DEFAULT '0',
  `dining_rooms` int NOT NULL DEFAULT '0',
  `storage_rooms` int NOT NULL DEFAULT '0',
  `garage_spaces` int NOT NULL DEFAULT '0',
  `outdoor_spaces` int NOT NULL DEFAULT '0',
  `utilities_included` json DEFAULT NULL,
  `maintenance_responsibility` json DEFAULT NULL,
  `lease_terms` json DEFAULT NULL,
  `minimum_lease_period` int NOT NULL DEFAULT '1',
  `maximum_lease_period` int DEFAULT NULL,
  `notice_period` int NOT NULL DEFAULT '30',
  `late_fee_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `late_fee_fixed` decimal(10,2) NOT NULL DEFAULT '0.00',
  `returned_check_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `early_termination_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `renewal_terms` json DEFAULT NULL,
  `special_conditions` json DEFAULT NULL,
  `marketing_description` text COLLATE utf8mb4_unicode_ci,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `seo_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `applications_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_properties_status_is_vacant_index` (`status`,`is_vacant`),
  KEY `rental_properties_property_type_id_index` (`property_type_id`),
  KEY `rental_properties_landlord_id_index` (`landlord_id`),
  KEY `rental_properties_is_featured_is_published_index` (`is_featured`,`is_published`),
  KEY `rental_properties_rent_amount_index` (`rent_amount`),
  KEY `rental_properties_bedrooms_bathrooms_index` (`bedrooms`,`bathrooms`),
  CONSTRAINT `rental_properties_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rental_properties_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_properties`
--

LOCK TABLES `rental_properties` WRITE;
/*!40000 ALTER TABLE `rental_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `rental_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_property_amenities`
--

DROP TABLE IF EXISTS `rental_property_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_property_amenities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_property_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amenity_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `is_included` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rental_property_amenities_rental_property_id_amenity_id_unique` (`rental_property_id`,`amenity_id`),
  KEY `rental_property_amenities_amenity_id_is_included_index` (`amenity_id`,`is_included`),
  CONSTRAINT `rental_property_amenities_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `property_amenities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rental_property_amenities_rental_property_id_foreign` FOREIGN KEY (`rental_property_id`) REFERENCES `rental_properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_property_amenities`
--

LOCK TABLES `rental_property_amenities` WRITE;
/*!40000 ALTER TABLE `rental_property_amenities` DISABLE KEYS */;
/*!40000 ALTER TABLE `rental_property_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_units`
--

DROP TABLE IF EXISTS `rental_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_units` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rental_property_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `floor_number` int DEFAULT NULL,
  `rent_amount` decimal(16,2) NOT NULL,
  `deposit_amount` decimal(16,2) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_vacant` tinyint(1) NOT NULL DEFAULT '1',
  `bedrooms` int NOT NULL DEFAULT '0',
  `bathrooms` int NOT NULL DEFAULT '0',
  `square_footage` decimal(10,2) DEFAULT NULL,
  `balcony` tinyint(1) NOT NULL DEFAULT '0',
  `parking_space` tinyint(1) NOT NULL DEFAULT '0',
  `storage_unit` tinyint(1) NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `maintenance_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_units_rental_property_id_unit_number_index` (`rental_property_id`,`unit_number`),
  KEY `rental_units_status_is_vacant_index` (`status`,`is_vacant`),
  CONSTRAINT `rental_units_rental_property_id_foreign` FOREIGN KEY (`rental_property_id`) REFERENCES `rental_properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_units`
--

LOCK TABLES `rental_units` WRITE;
/*!40000 ALTER TABLE `rental_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `rental_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_templates` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sections` json DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `layout` json DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_templates_category_is_active_index` (`category`,`is_active`),
  KEY `report_templates_report_type_is_active_index` (`report_type`,`is_active`),
  KEY `report_templates_is_public_is_active_index` (`is_public`,`is_active`),
  KEY `report_templates_created_by_is_active_index` (`created_by`,`is_active`),
  KEY `report_templates_category_index` (`category`),
  KEY `report_templates_report_type_index` (`report_type`),
  KEY `report_templates_is_public_index` (`is_public`),
  KEY `report_templates_is_active_index` (`is_active`),
  CONSTRAINT `report_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_templates`
--

LOCK TABLES `report_templates` WRITE;
/*!40000 ALTER TABLE `report_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'tenant','web','2025-09-18 12:03:29','2025-09-18 12:03:29'),(2,'landlord','web','2025-09-18 12:03:29','2025-09-18 12:03:29'),(3,'agent','web','2025-09-18 12:03:29','2025-09-18 12:03:29'),(4,'admin','web','2025-09-18 12:03:29','2025-09-18 12:03:29');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_properties`
--

DROP TABLE IF EXISTS `sale_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_properties` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `property_type_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price` decimal(16,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','sold','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `furnished` tinyint(1) NOT NULL DEFAULT '0',
  `pet_friendly` tinyint(1) NOT NULL DEFAULT '0',
  `parking_spaces` int NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `year_built` int DEFAULT NULL,
  `property_size` decimal(10,2) DEFAULT NULL,
  `lot_size` decimal(10,2) DEFAULT NULL,
  `bedrooms` int NOT NULL DEFAULT '0',
  `bathrooms` int NOT NULL DEFAULT '0',
  `garage_spaces` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `offers_count` int NOT NULL DEFAULT '0',
  `sale_terms` json DEFAULT NULL,
  `special_conditions` json DEFAULT NULL,
  `marketing_description` text COLLATE utf8mb4_unicode_ci,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_properties_status_is_available_index` (`status`,`is_available`),
  KEY `sale_properties_property_type_id_index` (`property_type_id`),
  KEY `sale_properties_landlord_id_index` (`landlord_id`),
  KEY `sale_properties_is_featured_is_published_index` (`is_featured`,`is_published`),
  KEY `sale_properties_sale_price_index` (`sale_price`),
  KEY `sale_properties_bedrooms_bathrooms_index` (`bedrooms`,`bathrooms`),
  CONSTRAINT `sale_properties_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_properties_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_properties`
--

LOCK TABLES `sale_properties` WRITE;
/*!40000 ALTER TABLE `sale_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_reports`
--

DROP TABLE IF EXISTS `scheduled_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheduled_reports` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `schedule_frequency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` time NOT NULL,
  `recipients` json DEFAULT NULL,
  `export_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_run_at` timestamp NULL DEFAULT NULL,
  `next_run_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_reports_template_id_foreign` (`template_id`),
  KEY `scheduled_reports_is_active_next_run_at_index` (`is_active`,`next_run_at`),
  KEY `scheduled_reports_schedule_frequency_is_active_index` (`schedule_frequency`,`is_active`),
  KEY `scheduled_reports_export_format_is_active_index` (`export_format`,`is_active`),
  KEY `scheduled_reports_created_by_is_active_index` (`created_by`,`is_active`),
  KEY `scheduled_reports_report_type_index` (`report_type`),
  KEY `scheduled_reports_schedule_frequency_index` (`schedule_frequency`),
  KEY `scheduled_reports_export_format_index` (`export_format`),
  KEY `scheduled_reports_is_active_index` (`is_active`),
  KEY `scheduled_reports_next_run_at_index` (`next_run_at`),
  CONSTRAINT `scheduled_reports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_reports_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `report_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_reports`
--

LOCK TABLES `scheduled_reports` WRITE;
/*!40000 ALTER TABLE `scheduled_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'color_mode_style','dark-mode');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_categories`
--

DROP TABLE IF EXISTS `settings_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_categories`
--

LOCK TABLES `settings_categories` WRITE;
/*!40000 ALTER TABLE `settings_categories` DISABLE KEYS */;
INSERT INTO `settings_categories` VALUES ('9fe8387d-90bf-4ba6-8fcf-3f9cd1767844','Security & Authentication','security-authentication','Security settings, authentication policies, and access control','ni-shield-check',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a2da-48ba-bacb-87129cf05250','Business Configuration','business-configuration','Financial settings, business rules, and operational policies','ni-building',2,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-abdc-4544-9311-01db327ba39a','Notification Management','notification-management','Email, SMS, and push notification settings','ni-notification',3,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-b3c6-43a9-8160-807257b468dc','System Performance','system-performance','Cache, optimization, and monitoring settings','ni-speedometer',4,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-bca5-4d33-becb-ba1a8e8ca747','User Management','user-management','User roles, permissions, profiles, and account management settings','ni-users',5,1,'2025-09-18 12:08:09','2025-09-18 12:08:09');
/*!40000 ALTER TABLE `settings_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_groups`
--

DROP TABLE IF EXISTS `settings_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_groups` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order_index` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_groups_category_id_foreign` (`category_id`),
  CONSTRAINT `settings_groups_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `settings_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_groups`
--

LOCK TABLES `settings_groups` WRITE;
/*!40000 ALTER TABLE `settings_groups` DISABLE KEYS */;
INSERT INTO `settings_groups` VALUES ('9fe8387d-985c-436f-be66-64dce335e78a','9fe8387d-90bf-4ba6-8fcf-3f9cd1767844','Authentication','authentication','User authentication and login settings',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a467-4353-af7d-867cb8d8ab71','9fe8387d-a2da-48ba-bacb-87129cf05250','Financial Settings','financial-settings','Tax rates, fees, and financial calculations',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-add8-42d6-97a4-fc7fe9a43067','9fe8387d-abdc-4544-9311-01db327ba39a','Email Settings','email-settings','Email notification preferences and templates',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-b580-41c2-9818-76b27864d704','9fe8387d-b3c6-43a9-8160-807257b468dc','Cache Settings','cache-settings','Cache configuration and optimization',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-bee0-45f5-ad4a-446a7e81d2e1','9fe8387d-bca5-4d33-becb-ba1a8e8ca747','Role Management','role-management','Configure user roles and their default permissions',1,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c7ea-439b-9a2a-f29e3de7473d','9fe8387d-bca5-4d33-becb-ba1a8e8ca747','Permission Management','permission-management','Configure system permissions and access controls',2,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-d0b8-460f-9221-d3142dc624f8','9fe8387d-bca5-4d33-becb-ba1a8e8ca747','User Profile Settings','user-profile-settings','Configure user profile fields and requirements',3,1,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','9fe8387d-bca5-4d33-becb-ba1a8e8ca747','Account Security','account-security','User account security and password policies',4,1,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e7c2-4463-8dac-b967d29413af','9fe8387d-bca5-4d33-becb-ba1a8e8ca747','User Registration','user-registration','Configure user registration process and requirements',5,1,'2025-09-18 12:08:10','2025-09-18 12:08:10');
/*!40000 ALTER TABLE `settings_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_history`
--

DROP TABLE IF EXISTS `settings_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_history` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `changed_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_at` timestamp NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_history_setting_id_foreign` (`setting_id`),
  KEY `settings_history_changed_by_foreign` (`changed_by`),
  CONSTRAINT `settings_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `settings_history_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_history`
--

LOCK TABLES `settings_history` WRITE;
/*!40000 ALTER TABLE `settings_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_items`
--

DROP TABLE IF EXISTS `settings_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` enum('text','number','boolean','select','multiselect','file','json','email','url','password') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `validation_rules` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `default_value` text COLLATE utf8mb4_unicode_ci,
  `options` json DEFAULT NULL,
  `placeholder` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order_index` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_items_key_unique` (`key`),
  KEY `settings_items_group_id_foreign` (`group_id`),
  CONSTRAINT `settings_items_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `settings_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_items`
--

LOCK TABLES `settings_items` WRITE;
/*!40000 ALTER TABLE `settings_items` DISABLE KEYS */;
INSERT INTO `settings_items` VALUES ('9fe8387d-9c3f-49a4-a87d-8cc7d6a96842','9fe8387d-985c-436f-be66-64dce335e78a','two_factor_authentication','false','boolean',NULL,'Enable two-factor authentication for all users',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-9f0a-419d-ac1f-ac64841961d8','9fe8387d-985c-436f-be66-64dce335e78a','failed_login_attempts','5','number','[\"min:3\", \"max:10\"]','Maximum failed login attempts before account lockout',0,0,'5',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a101-4b63-b8d8-bc424f5a0113','9fe8387d-985c-436f-be66-64dce335e78a','session_timeout','120','select',NULL,'Session timeout in minutes',0,0,'120','{\"15\": \"15 minutes\", \"30\": \"30 minutes\", \"60\": \"1 hour\", \"120\": \"2 hours\", \"240\": \"4 hours\", \"480\": \"8 hours\", \"1440\": \"24 hours\"}',NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a6dc-43c1-a748-9097e8a91fca','9fe8387d-a467-4353-af7d-867cb8d8ab71','default_tax_rate','16.0','number','[\"min:0\", \"max:100\"]','Default tax rate percentage',0,0,'16.0',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a861-4f4b-a5e9-ba990e6f621c','9fe8387d-a467-4353-af7d-867cb8d8ab71','late_payment_penalty_rate','2.0','number','[\"min:0\", \"max:20\"]','Late payment penalty rate percentage per month',0,0,'2.0',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-a9f7-4711-ac4b-2ba4b89244be','9fe8387d-a467-4353-af7d-867cb8d8ab71','commission_rate_landlord','8.0','number','[\"min:0\", \"max:50\"]','Commission rate for landlords (%)',0,0,'8.0',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-af8f-44db-9e72-760ced059b66','9fe8387d-add8-42d6-97a4-fc7fe9a43067','email_notifications_enabled','true','boolean',NULL,'Enable email notifications',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-b1b1-404e-a520-3569e96bf5c9','9fe8387d-add8-42d6-97a4-fc7fe9a43067','invoice_reminder_days','7','number','[\"min:1\", \"max:30\"]','Days before invoice due date to send reminder',0,0,'7',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-b75f-414d-8d29-c791fc3f297c','9fe8387d-b580-41c2-9818-76b27864d704','cache_enabled','true','boolean',NULL,'Enable application caching',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-b8ec-4a7c-ba68-be1d237a8877','9fe8387d-b580-41c2-9818-76b27864d704','cache_ttl','3600','select',NULL,'Cache time-to-live in seconds',0,0,'3600','{\"900\": \"15 minutes\", \"1800\": \"30 minutes\", \"3600\": \"1 hour\", \"7200\": \"2 hours\", \"14400\": \"4 hours\", \"86400\": \"24 hours\"}',NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c136-4585-bca3-9375f7a76afc','9fe8387d-bee0-45f5-ad4a-446a7e81d2e1','default_user_role','tenant','select',NULL,'Default role assigned to new users',0,0,'tenant','{\"admin\": \"Administrator\", \"tenant\": \"Tenant\", \"viewer\": \"Viewer Only\", \"manager\": \"Property Manager\", \"landlord\": \"Landlord\"}',NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c2ed-458f-80c0-7b902a36be29','9fe8387d-bee0-45f5-ad4a-446a7e81d2e1','role_hierarchy_enabled','true','boolean',NULL,'Enable role hierarchy (higher roles can manage lower roles)',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c480-4dd3-8bed-26ce9006e6d1','9fe8387d-bee0-45f5-ad4a-446a7e81d2e1','max_roles_per_user','3','number','[\"min:1\", \"max:10\"]','Maximum number of roles a user can have',0,0,'3',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c611-4159-8816-65ab8bae1164','9fe8387d-bee0-45f5-ad4a-446a7e81d2e1','auto_assign_roles','false','boolean',NULL,'Automatically assign roles based on user registration data',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-c9c5-46d5-9b16-dffd86569da3','9fe8387d-c7ea-439b-9a2a-f29e3de7473d','permission_caching_enabled','true','boolean',NULL,'Enable permission caching for better performance',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-cb3a-4e5b-bbf8-16ba1d0d24dc','9fe8387d-c7ea-439b-9a2a-f29e3de7473d','permission_cache_ttl','3600','select',NULL,'Permission cache time-to-live in seconds',0,0,'3600','{\"900\": \"15 minutes\", \"1800\": \"30 minutes\", \"3600\": \"1 hour\", \"7200\": \"2 hours\", \"14400\": \"4 hours\", \"86400\": \"24 hours\"}',NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-ccb6-4850-a0c9-cdf1085ea98f','9fe8387d-c7ea-439b-9a2a-f29e3de7473d','require_explicit_permissions','false','boolean',NULL,'Require explicit permission grants (no inheritance)',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-cf13-4160-8d53-2f55d9526f91','9fe8387d-c7ea-439b-9a2a-f29e3de7473d','permission_audit_logging','true','boolean',NULL,'Log all permission checks and access attempts',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-d255-4993-bf9a-dc67ddfd950f','9fe8387d-d0b8-460f-9221-d3142dc624f8','require_profile_completion','true','boolean',NULL,'Require users to complete their profile before accessing the system',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-d3fe-4dbc-b27a-340f34681443','9fe8387d-d0b8-460f-9221-d3142dc624f8','required_profile_fields','name,email,phone','multiselect',NULL,'Required fields for user profiles',0,0,'name,email,phone','{\"bio\": \"Biography\", \"name\": \"Full Name\", \"email\": \"Email Address\", \"phone\": \"Phone Number\", \"address\": \"Address\", \"id_number\": \"ID Number\", \"profile_picture\": \"Profile Picture\", \"emergency_contact\": \"Emergency Contact\"}',NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-d5b2-455e-82f2-1939af1a642c','9fe8387d-d0b8-460f-9221-d3142dc624f8','allow_profile_picture_upload','true','boolean',NULL,'Allow users to upload profile pictures',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:09','2025-09-18 12:08:09'),('9fe8387d-d739-4276-96f5-da6591314786','9fe8387d-d0b8-460f-9221-d3142dc624f8','max_profile_picture_size','2048','number','[\"min:512\", \"max:10240\"]','Maximum profile picture file size in KB',0,0,'2048',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-d8f6-4475-b739-324b2580fabe','9fe8387d-d0b8-460f-9221-d3142dc624f8','profile_picture_dimensions','200x200','select',NULL,'Standard profile picture dimensions',0,0,'200x200','{\"150x150\": \"150x150 pixels\", \"200x200\": \"200x200 pixels\", \"300x300\": \"300x300 pixels\", \"400x400\": \"400x400 pixels\"}',NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-dc51-4bc8-ae72-ad70f0b77b34','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_min_length','8','number','[\"min:6\", \"max:32\"]','Minimum password length',0,0,'8',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-dde1-4398-97c9-2ee3ec4a7558','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_require_uppercase','true','boolean',NULL,'Require uppercase letters in passwords',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-df54-46c5-a88d-7da9f329ccb1','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_require_lowercase','true','boolean',NULL,'Require lowercase letters in passwords',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e15a-440b-949a-f062c9c731c9','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_require_numbers','true','boolean',NULL,'Require numbers in passwords',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e2f7-4803-b939-6351ccd1ffe8','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_require_symbols','false','boolean',NULL,'Require special symbols in passwords',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e47f-4182-b6cd-e877e044b3b0','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','password_expiry_days','90','select',NULL,'Password expiry period in days (0 = never expire)',0,0,'90','{\"0\": \"Never expire\", \"30\": \"30 days\", \"60\": \"60 days\", \"90\": \"90 days\", \"180\": \"180 days\", \"365\": \"1 year\"}',NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e5fb-4aff-b4f9-305e734d6d18','9fe8387d-daaf-4b2f-b459-4b132e3ba9b4','account_lockout_duration','15','select',NULL,'Account lockout duration in minutes after failed attempts',0,0,'15','{\"5\": \"5 minutes\", \"15\": \"15 minutes\", \"30\": \"30 minutes\", \"60\": \"1 hour\", \"120\": \"2 hours\", \"240\": \"4 hours\"}',NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-e9a2-4e95-abe1-d675446c4a36','9fe8387d-e7c2-4463-8dac-b967d29413af','registration_enabled','true','boolean',NULL,'Allow new user registrations',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-eb56-4bff-97f4-a86a093b3390','9fe8387d-e7c2-4463-8dac-b967d29413af','email_verification_required','true','boolean',NULL,'Require email verification for new accounts',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-ecd0-49b8-8af7-6f588897dff2','9fe8387d-e7c2-4463-8dac-b967d29413af','phone_verification_required','false','boolean',NULL,'Require phone number verification for new accounts',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-eea0-4b83-be36-8339272849fa','9fe8387d-e7c2-4463-8dac-b967d29413af','admin_approval_required','false','boolean',NULL,'Require admin approval for new user accounts',0,0,'false',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-f046-46ef-9c08-fbf5209e6f31','9fe8387d-e7c2-4463-8dac-b967d29413af','registration_terms_required','true','boolean',NULL,'Require users to accept terms and conditions during registration',0,0,'true',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10'),('9fe8387d-f1d3-43c8-b723-7ae64c6c80a1','9fe8387d-e7c2-4463-8dac-b967d29413af','max_registrations_per_ip','5','number','[\"min:1\", \"max:50\"]','Maximum registrations allowed per IP address per day',0,0,'5',NULL,NULL,1,0,'2025-09-18 12:08:10','2025-09-18 12:08:10');
/*!40000 ALTER TABLE `settings_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stk_requests`
--

DROP TABLE IF EXISTS `stk_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stk_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MerchantRequestID` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CheckoutRequestID` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detailed_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result_description` text COLLATE utf8mb4_unicode_ci,
  `callback_metadata` json DEFAULT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `failure_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MpesaReceiptNumber` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ResultDesc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TransactionDate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stk_requests_merchantrequestid_unique` (`MerchantRequestID`),
  UNIQUE KEY `stk_requests_checkoutrequestid_unique` (`CheckoutRequestID`),
  KEY `stk_requests_user_id_foreign` (`user_id`),
  KEY `stk_requests_invoice_id_foreign` (`invoice_id`),
  KEY `stk_requests_detailed_status_created_at_index` (`detailed_status`,`created_at`),
  KEY `stk_requests_result_code_index` (`result_code`),
  CONSTRAINT `stk_requests_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stk_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stk_requests`
--

LOCK TABLES `stk_requests` WRITE;
/*!40000 ALTER TABLE `stk_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `stk_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_id` bigint unsigned NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  KEY `support_tickets_property_id_foreign` (`property_id`),
  KEY `support_tickets_house_id_foreign` (`house_id`),
  CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_alerts`
--

DROP TABLE IF EXISTS `system_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_alerts` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alert_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `threshold_value` decimal(10,4) DEFAULT NULL,
  `actual_value` decimal(10,4) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `acknowledged_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_alerts_acknowledged_by_foreign` (`acknowledged_by`),
  KEY `system_alerts_resolved_by_foreign` (`resolved_by`),
  KEY `system_alerts_created_by_foreign` (`created_by`),
  KEY `system_alerts_alert_type_status_index` (`alert_type`,`status`),
  KEY `system_alerts_severity_status_index` (`severity`,`status`),
  KEY `system_alerts_status_created_at_index` (`status`,`created_at`),
  KEY `system_alerts_source_status_index` (`source`,`status`),
  KEY `system_alerts_created_at_index` (`created_at`),
  KEY `system_alerts_acknowledged_at_index` (`acknowledged_at`),
  KEY `system_alerts_resolved_at_index` (`resolved_at`),
  KEY `system_alerts_alert_type_index` (`alert_type`),
  KEY `system_alerts_severity_index` (`severity`),
  KEY `system_alerts_status_index` (`status`),
  CONSTRAINT `system_alerts_acknowledged_by_foreign` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `system_alerts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `system_alerts_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_alerts`
--

LOCK TABLES `system_alerts` WRITE;
/*!40000 ALTER TABLE `system_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_metrics`
--

DROP TABLE IF EXISTS `system_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_metrics` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,4) NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tags` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `timestamp` timestamp NOT NULL,
  `server_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_metrics_created_by_foreign` (`created_by`),
  KEY `system_metrics_metric_type_timestamp_index` (`metric_type`,`timestamp`),
  KEY `system_metrics_category_timestamp_index` (`category`,`timestamp`),
  KEY `system_metrics_server_id_timestamp_index` (`server_id`,`timestamp`),
  KEY `system_metrics_created_at_index` (`created_at`),
  KEY `system_metrics_metric_type_index` (`metric_type`),
  KEY `system_metrics_category_index` (`category`),
  KEY `system_metrics_timestamp_index` (`timestamp`),
  KEY `system_metrics_server_id_index` (`server_id`),
  CONSTRAINT `system_metrics_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_metrics`
--

LOCK TABLES `system_metrics` WRITE;
/*!40000 ALTER TABLE `system_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_attachments`
--

DROP TABLE IF EXISTS `ticket_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_attachments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_attachments_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_attachments_reply_id_foreign` (`reply_id`),
  CONSTRAINT `ticket_attachments_reply_id_foreign` FOREIGN KEY (`reply_id`) REFERENCES `ticket_replies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_attachments`
--

LOCK TABLES `ticket_attachments` WRITE;
/*!40000 ALTER TABLE `ticket_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_counts`
--

DROP TABLE IF EXISTS `ticket_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_counts` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day` date NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_counts_day_unique` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_counts`
--

LOCK TABLES `ticket_counts` WRITE;
/*!40000 ALTER TABLE `ticket_counts` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_counts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_replies` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_replies_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_replies_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_replies_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_replies`
--

LOCK TABLES `ticket_replies` WRITE;
/*!40000 ALTER TABLE `ticket_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activities` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `user_activities_activity_type_created_at_index` (`activity_type`,`created_at`),
  KEY `user_activities_activity_name_created_at_index` (`activity_name`,`created_at`),
  KEY `user_activities_ip_address_created_at_index` (`ip_address`,`created_at`),
  KEY `user_activities_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `user_activities_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  KEY `user_activities_created_at_index` (`created_at`),
  KEY `user_activities_activity_type_index` (`activity_type`),
  KEY `user_activities_activity_name_index` (`activity_name`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activities`
--

LOCK TABLES `user_activities` WRITE;
/*!40000 ALTER TABLE `user_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_invitations`
--

DROP TABLE IF EXISTS `user_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_invitations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tenant',
  `invited_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `accepted_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_invitations_token_unique` (`token`),
  KEY `user_invitations_invited_by_foreign` (`invited_by`),
  KEY `user_invitations_accepted_by_foreign` (`accepted_by`),
  KEY `user_invitations_cancelled_by_foreign` (`cancelled_by`),
  KEY `user_invitations_email_status_index` (`email`,`status`),
  KEY `user_invitations_token_index` (`token`),
  KEY `user_invitations_expires_at_index` (`expires_at`),
  KEY `user_invitations_status_expires_at_index` (`status`,`expires_at`),
  CONSTRAINT `user_invitations_accepted_by_foreign` FOREIGN KEY (`accepted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_invitations_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_invitations`
--

LOCK TABLES `user_invitations` WRITE;
/*!40000 ALTER TABLE `user_invitations` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_invitations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preference_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preference_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_preferences_user_id_preference_key_unique` (`user_id`,`preference_key`),
  CONSTRAINT `user_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_secondary` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `profile_picture` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_license` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Africa/Nairobi',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_profiles_user_id_index` (`user_id`),
  KEY `user_profiles_gender_index` (`gender`),
  KEY `user_profiles_city_index` (`city`),
  KEY `user_profiles_state_index` (`state`),
  KEY `user_profiles_country_index` (`country`),
  KEY `user_profiles_created_by_index` (`created_by`),
  CONSTRAINT `user_profiles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation_place` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_identity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `welcome_valid_until` timestamp NULL DEFAULT NULL,
  `preferred_locale` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_email_index` (`email`),
  KEY `users_phone_index` (`phone`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_is_active_created_at_index` (`is_active`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('9fe836c4-0572-4911-a5ac-d7d1a3477e40','Super Administrator','superadmin@foxesrentals.com','2025-09-18 12:03:50','$2y$10$8am9NIA5Mp.Htqge7Bi0ruY6gXmmUD91m0w08PBDfWYUoun32zGIm',NULL,'+254700000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2025-09-18 12:03:20','2025-09-18 12:03:50',NULL,'en'),('9fe836d2-467b-4cb0-8ac5-c1ba0f90cfb5','Administrator','admin@admin.com','2025-09-18 12:03:29','$2y$10$byVRaEKIv0EM8m.CXEUfnu9SZxNQ1OCTO2N1WRcS137cJTECknvua',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2025-09-18 12:03:29','2025-09-18 12:03:29',NULL,'en'),('9fe836f2-af66-49c7-9fde-97f5c51a8ab2','System Administrator','admin@foxesrentals.com','2025-09-18 12:03:51','$2y$10$5wj6KcdzZjsqRxkVbIg22./oABB2jmkQumUiJ5Q0dxtSMh9sdy4a2',NULL,'+254700000001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2025-09-18 12:03:51','2025-09-18 12:03:51',NULL,'en'),('9fe836f2-dbb8-4d38-bf6f-205c04e8cf3f','Property Manager','manager@foxesrentals.com','2025-09-18 12:03:51','$2y$10$G4PPQoCZifxbgf4Myd8/kuT3UpQr2x9FscgHERj7wFlwvUeQlw.Sa',NULL,'+254700000002',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'2025-09-18 12:03:51','2025-09-18 12:03:51',NULL,'en');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher_documents`
--

DROP TABLE IF EXISTS `voucher_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voucher_documents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_documents_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `voucher_documents_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher_documents`
--

LOCK TABLES `voucher_documents` WRITE;
/*!40000 ALTER TABLE `voucher_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `voucher_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher_items`
--

DROP TABLE IF EXISTS `voucher_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voucher_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` bigint unsigned NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `voucher_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_items_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `voucher_items_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher_items`
--

LOCK TABLES `voucher_items` WRITE;
/*!40000 ALTER TABLE `voucher_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `voucher_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_id` bigint unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `property_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landlord_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vouchers_property_id_foreign` (`property_id`),
  KEY `vouchers_house_id_foreign` (`house_id`),
  KEY `vouchers_landlord_id_foreign` (`landlord_id`),
  CONSTRAINT `vouchers_house_id_foreign` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vouchers_landlord_id_foreign` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vouchers_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'foxes_rentals'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-20 16:27:45

-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: url_shortener
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

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
-- Table structure for table `click_logs`
--

DROP TABLE IF EXISTS `click_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `click_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `short_url_id` bigint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `click_logs_short_url_id_created_at_index` (`short_url_id`,`created_at`),
  KEY `click_logs_country_index` (`country`),
  KEY `click_logs_created_at_index` (`created_at`),
  CONSTRAINT `click_logs_short_url_id_foreign` FOREIGN KEY (`short_url_id`) REFERENCES `short_urls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `click_logs`
--

LOCK TABLES `click_logs` WRITE;
/*!40000 ALTER TABLE `click_logs` DISABLE KEYS */;
INSERT INTO `click_logs` VALUES (98,9,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','Unknown','Chrome','Windows','2025-06-05 11:52:25','2025-06-05 11:52:25'),(100,9,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','Unknown','Chrome','Linux','2025-06-05 18:17:37','2025-06-05 18:17:37'),(101,9,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','Unknown','Chrome','Linux','2025-06-05 18:17:40','2025-06-05 18:17:40');
/*!40000 ALTER TABLE `click_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invite_codes`
--

DROP TABLE IF EXISTS `invite_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invite_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `used_by` bigint unsigned DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_single_use` tinyint(1) NOT NULL DEFAULT '1',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invite_codes_code_unique` (`code`),
  KEY `invite_codes_code_is_active_index` (`code`,`is_active`),
  KEY `invite_codes_created_by_index` (`created_by`),
  KEY `invite_codes_used_by_index` (`used_by`),
  CONSTRAINT `invite_codes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invite_codes_used_by_foreign` FOREIGN KEY (`used_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invite_codes`
--

LOCK TABLES `invite_codes` WRITE;
/*!40000 ALTER TABLE `invite_codes` DISABLE KEYS */;
INSERT INTO `invite_codes` VALUES (1,'WELCOME2024',6,NULL,NULL,1,1,'Welcome code for 2024','2025-06-05 14:32:07','2025-06-05 14:32:07'),(2,'BETA_TEST',6,NULL,NULL,1,1,'Beta tester invite','2025-06-05 14:32:07','2025-06-05 14:32:07'),(3,'FRIEND_INVITE',6,NULL,NULL,1,1,'Friend invitation','2025-06-05 14:32:07','2025-06-05 14:32:07'),(4,'NODKDQ1I',6,NULL,NULL,1,1,'sky (Bulk #1)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(5,'JGG4B7GN',6,NULL,NULL,1,1,'sky (Bulk #2)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(6,'MYNDYSRB',6,NULL,NULL,1,1,'sky (Bulk #3)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(7,'3NFNTMXP',6,NULL,NULL,1,1,'sky (Bulk #4)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(8,'QPOQH80X',6,NULL,NULL,1,1,'sky (Bulk #5)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(9,'CU6GWPJ6',6,NULL,NULL,1,1,'sky (Bulk #6)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(10,'F6RRA0IV',6,NULL,NULL,1,1,'sky (Bulk #7)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(11,'GDSL9FRZ',6,NULL,NULL,1,1,'sky (Bulk #8)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(12,'8ZLAB5D4',6,NULL,NULL,1,1,'sky (Bulk #9)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(13,'BGTI9ION',6,NULL,NULL,1,1,'sky (Bulk #10)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(14,'TUXXJK2J',6,NULL,NULL,1,1,'sky (Bulk #11)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(15,'TNL5PDNC',6,NULL,NULL,1,1,'sky (Bulk #12)','2025-06-05 18:13:48','2025-06-05 18:13:48'),(16,'EQNSHGHG',6,NULL,NULL,1,1,'Bulk generated #1','2025-06-05 18:14:04','2025-06-05 18:14:04'),(17,'WCSXSCPC',6,NULL,NULL,1,1,'Bulk generated #2','2025-06-05 18:14:04','2025-06-05 18:14:04'),(18,'MP1JNPGB',6,NULL,NULL,1,1,'Bulk generated #3','2025-06-05 18:14:04','2025-06-05 18:14:04'),(19,'E7GE41ZI',6,NULL,NULL,1,1,'Bulk generated #4','2025-06-05 18:14:04','2025-06-05 18:14:04'),(20,'3YQLXDIQ',6,NULL,NULL,1,1,'Bulk generated #5','2025-06-05 18:14:04','2025-06-05 18:14:04');
/*!40000 ALTER TABLE `invite_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2024_01_01_000000_create_short_urls_table',1),(3,'2024_01_01_000001_create_click_logs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',2),(7,'2025_06_05_152707_create_invite_codes_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `short_urls`
--

DROP TABLE IF EXISTS `short_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `short_urls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `original_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clicks` bigint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_urls_short_code_unique` (`short_code`),
  KEY `short_urls_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `short_urls_short_code_index` (`short_code`),
  CONSTRAINT `short_urls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `short_urls`
--

LOCK TABLES `short_urls` WRITE;
/*!40000 ALTER TABLE `short_urls` DISABLE KEYS */;
INSERT INTO `short_urls` VALUES (9,5,'https://loghomevibes.com/discover-this-stunning-log-shell-cabin-for-just-37000/','oPDRvY',3,NULL,'2025-06-05 11:51:57','2025-06-05 18:17:40');
/*!40000 ALTER TABLE `short_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','collaborator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'collaborator',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'Dardan Dermaku','dardidermaku@gmail.com',NULL,'$2y$12$oyMmY6Egu7fvFAvXFWSdZuhjyLNhq6KD4WNXXy0JzC9VDvegBynyq','collaborator',NULL,'2025-06-05 11:38:03','2025-06-05 11:38:03'),(6,'Vaca.Sh Admin','admin@vaca.sh',NULL,'$2y$12$gWxwmAUdrY5fEJyKcspQ2Oq5PgKXjAMmoAV.gZoZRaRNseDk0A8Py','admin','Z15RjHIMLjr9kmXqwN863AeRNldlZMatKLbenIBkOLQpXjgyYt7hAu2sSGOY','2025-06-05 13:59:00','2025-06-05 13:59:00');
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

-- Dump completed on 2025-06-06  0:53:50

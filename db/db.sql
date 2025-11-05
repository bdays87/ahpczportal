-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: practitioner_db
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `accounttypes`
--

DROP TABLE IF EXISTS `accounttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounttypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounttypes`
--

LOCK TABLES `accounttypes` WRITE;
/*!40000 ALTER TABLE `accounttypes` DISABLE KEYS */;
INSERT INTO `accounttypes` VALUES (1,'Administrator','active','2025-08-14 08:13:15','2025-08-14 17:21:39'),(2,'Practitioner','active','2025-08-14 17:23:01','2025-08-14 17:23:01'),(3,'Student','active','2025-08-14 17:23:09','2025-08-14 17:23:09');
/*!40000 ALTER TABLE `accounttypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicationfees`
--

DROP TABLE IF EXISTS `applicationfees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicationfees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` int DEFAULT NULL,
  `tire_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `registertype_id` bigint unsigned NOT NULL,
  `employmentlocation_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generalledger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicationfees_tire_id_foreign` (`tire_id`),
  KEY `applicationfees_currency_id_foreign` (`currency_id`),
  KEY `applicationfees_registertype_id_foreign` (`registertype_id`),
  KEY `applicationfees_qualificationcategory_id_foreign` (`employmentlocation_id`),
  CONSTRAINT `applicationfees_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `applicationfees_registertype_id_foreign` FOREIGN KEY (`registertype_id`) REFERENCES `registertypes` (`id`),
  CONSTRAINT `applicationfees_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicationfees`
--

LOCK TABLES `applicationfees` WRITE;
/*!40000 ALTER TABLE `applicationfees` DISABLE KEYS */;
INSERT INTO `applicationfees` VALUES (1,2025,1,1,1,1,'NEW','2342',200.00,'2025-08-18 15:33:17','2025-08-18 15:33:17'),(3,2025,1,1,1,2,'NEW','2342',250.00,'2025-08-21 14:48:13','2025-08-21 14:48:13'),(4,2025,1,1,1,2,'MAINTANCE','2342',55.00,'2025-09-10 08:23:03','2025-09-10 08:23:03'),(5,2025,1,1,1,2,'RENEWAL','2342',110.00,'2025-09-10 08:23:26','2025-09-10 08:23:26');
/*!40000 ALTER TABLE `applicationfees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicationsessions`
--

DROP TABLE IF EXISTS `applicationsessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicationsessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicationsessions`
--

LOCK TABLES `applicationsessions` WRITE;
/*!40000 ALTER TABLE `applicationsessions` DISABLE KEYS */;
INSERT INTO `applicationsessions` VALUES (1,2025,1,'2025-09-10 13:49:44','2025-09-10 13:51:05'),(3,2026,1,'2025-09-10 13:51:48','2025-09-10 13:51:48');
/*!40000 ALTER TABLE `applicationsessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicationtypes`
--

DROP TABLE IF EXISTS `applicationtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicationtypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicationtypes`
--

LOCK TABLES `applicationtypes` WRITE;
/*!40000 ALTER TABLE `applicationtypes` DISABLE KEYS */;
INSERT INTO `applicationtypes` VALUES (1,'NEW','2025-09-10 11:13:26','2025-09-10 11:13:26'),(2,'RENEWAL','2025-09-10 11:13:34','2025-09-10 11:13:34'),(3,'MAINTANCE','2025-09-10 11:13:47','2025-09-10 11:13:47');
/*!40000 ALTER TABLE `applicationtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bankaccounts`
--

DROP TABLE IF EXISTS `bankaccounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bankaccounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bankaccounts_bank_id_foreign` (`bank_id`),
  KEY `bankaccounts_currency_id_foreign` (`currency_id`),
  CONSTRAINT `bankaccounts_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`),
  CONSTRAINT `bankaccounts_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bankaccounts`
--

LOCK TABLES `bankaccounts` WRITE;
/*!40000 ALTER TABLE `bankaccounts` DISABLE KEYS */;
INSERT INTO `bankaccounts` VALUES (1,1,1,'12313123','2025-08-19 12:51:59','2025-08-19 12:51:59'),(2,1,3,'123123123321','2025-08-19 12:52:05','2025-08-19 12:52:05');
/*!40000 ALTER TABLE `bankaccounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banks`
--

LOCK TABLES `banks` WRITE;
/*!40000 ALTER TABLE `banks` DISABLE KEYS */;
INSERT INTO `banks` VALUES (1,'CBZ','2025-08-19 12:40:34','2025-08-19 12:42:13');
/*!40000 ALTER TABLE `banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banktransactions`
--

DROP TABLE IF EXISTS `banktransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banktransactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `bank_id` int DEFAULT NULL,
  `proofofpayment_id` int DEFAULT NULL,
  `statement_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banktransactions_currency_id_foreign` (`currency_id`),
  KEY `banktransactions_customer_id_foreign` (`customer_id`),
  CONSTRAINT `banktransactions_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `banktransactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banktransactions`
--

LOCK TABLES `banktransactions` WRITE;
/*!40000 ALTER TABLE `banktransactions` DISABLE KEYS */;
INSERT INTO `banktransactions` VALUES (2,1,2,1,NULL,'2142343242','12313123','2142343242',NULL,'Purchase requisitions ','2025-08-12','150.00','CLAIMED','2025-08-19 16:31:13','2025-08-22 07:13:06'),(3,1,3,1,NULL,'test2','12313123','2142343242',NULL,'test payment','2025-08-21','200','CLAIMED','2025-08-22 10:28:44','2025-08-22 11:04:14'),(4,1,6,1,NULL,'test3','12313123','2142343242',NULL,'assdasdas','2025-08-22','150.00','CLAIMED','2025-08-22 10:30:11','2025-08-29 14:48:09'),(5,1,7,1,NULL,'234324324','12313123','21423432424444',NULL,'Testing again','2025-08-27','450.00','CLAIMED','2025-08-31 16:56:54','2025-08-31 16:57:12');
/*!40000 ALTER TABLE `banktransactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('mlcscz-cache-356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1757527152),('mlcscz-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1757527152;',1757527152),('mlcscz-cache-902ba3cda1883801594b6e1b452790cc53948fda','i:2;',1757586977),('mlcscz-cache-902ba3cda1883801594b6e1b452790cc53948fda:timer','i:1757586977;',1757586977),('mlcscz-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:6:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:12:\"submodule_id\";s:1:\"c\";s:4:\"name\";s:1:\"d\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";s:1:\"j\";s:14:\"accounttype_id\";}s:11:\"permissions\";a:59:{i:0;a:5:{s:1:\"a\";i:3;s:1:\"b\";i:1;s:1:\"c\";s:19:\"accounttypes.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:5:{s:1:\"a\";i:4;s:1:\"b\";i:1;s:1:\"c\";s:15:\"settings.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:5:{s:1:\"a\";i:5;s:1:\"b\";i:2;s:1:\"c\";s:19:\"systemmodule.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:5:{s:1:\"a\";i:6;s:1:\"b\";i:3;s:1:\"c\";s:12:\"roles.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:5:{s:1:\"a\";i:7;s:1:\"b\";i:2;s:1:\"c\";s:19:\"systemmodule.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:5:{s:1:\"a\";i:8;s:1:\"b\";i:3;s:1:\"c\";s:11:\"role.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:5:{s:1:\"a\";i:10;s:1:\"b\";i:3;s:1:\"c\";s:11:\"role.assign\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:5:{s:1:\"a\";i:11;s:1:\"b\";i:4;s:1:\"c\";s:12:\"users.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:5:{s:1:\"a\";i:12;s:1:\"b\";i:4;s:1:\"c\";s:12:\"users.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:5:{s:1:\"a\";i:13;s:1:\"b\";i:1;s:1:\"c\";s:19:\"accounttypes.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:5:{s:1:\"a\";i:14;s:1:\"b\";i:4;s:1:\"c\";s:12:\"users.delete\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:5:{s:1:\"a\";i:15;s:1:\"b\";i:4;s:1:\"c\";s:10:\"users.view\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:5:{s:1:\"a\";i:16;s:1:\"b\";i:4;s:1:\"c\";s:12:\"users.assign\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:5:{s:1:\"a\";i:17;s:1:\"b\";i:5;s:1:\"c\";s:21:\"configurations.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:5:{s:1:\"a\";i:18;s:1:\"b\";i:5;s:1:\"c\";s:21:\"configurations.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:5:{s:1:\"a\";i:19;s:1:\"b\";i:6;s:1:\"c\";s:11:\"tire.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:5:{s:1:\"a\";i:21;s:1:\"b\";i:6;s:1:\"c\";s:11:\"tire.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:5:{s:1:\"a\";i:24;s:1:\"b\";i:6;s:1:\"c\";s:28:\"professionmanagements.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:5:{s:1:\"a\";i:25;s:1:\"b\";i:7;s:1:\"c\";s:18:\"professions.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:5:{s:1:\"a\";i:26;s:1:\"b\";i:7;s:1:\"c\";s:18:\"professions.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:5:{s:1:\"a\";i:27;s:1:\"b\";i:8;s:1:\"c\";s:15:\"currency.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:5:{s:1:\"a\";i:28;s:1:\"b\";i:8;s:1:\"c\";s:14:\"finance.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:5:{s:1:\"a\";i:29;s:1:\"b\";i:8;s:1:\"c\";s:15:\"currency.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:5:{s:1:\"a\";i:30;s:1:\"b\";i:9;s:1:\"c\";s:19:\"exchangerate.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:5:{s:1:\"a\";i:31;s:1:\"b\";i:9;s:1:\"c\";s:19:\"exchangerate.create\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:5:{s:1:\"a\";i:32;s:1:\"b\";i:9;s:1:\"c\";s:19:\"exchangerate.update\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:5:{s:1:\"a\";i:33;s:1:\"b\";i:9;s:1:\"c\";s:19:\"exchangerate.delete\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:27;a:5:{s:1:\"a\";i:34;s:1:\"b\";i:10;s:1:\"c\";s:23:\"settlementsplits.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:5:{s:1:\"a\";i:35;s:1:\"b\";i:10;s:1:\"c\";s:23:\"settlementsplits.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:5:{s:1:\"a\";i:36;s:1:\"b\";i:11;s:1:\"c\";s:22:\"paymentchannels.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:30;a:5:{s:1:\"a\";i:37;s:1:\"b\";i:11;s:1:\"c\";s:22:\"paymentchannels.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:5:{s:1:\"a\";i:38;s:1:\"b\";i:12;s:1:\"c\";s:23:\"registrationfees.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:5:{s:1:\"a\";i:39;s:1:\"b\";i:12;s:1:\"c\";s:23:\"registrationfees.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:5:{s:1:\"a\";i:40;s:1:\"b\";i:13;s:1:\"c\";s:22:\"applicationfees.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:5:{s:1:\"a\";i:41;s:1:\"b\";i:13;s:1:\"c\";s:22:\"applicationfees.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:5:{s:1:\"a\";i:42;s:1:\"b\";i:14;s:1:\"c\";s:16:\"penalties.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:5:{s:1:\"a\";i:43;s:1:\"b\";i:14;s:1:\"c\";s:16:\"penalties.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:5:{s:1:\"a\";i:44;s:1:\"b\";i:15;s:1:\"c\";s:16:\"discounts.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:38;a:5:{s:1:\"a\";i:45;s:1:\"b\";i:15;s:1:\"c\";s:16:\"discounts.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:39;a:5:{s:1:\"a\";i:46;s:1:\"b\";i:16;s:1:\"c\";s:20:\"otherservices.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:40;a:5:{s:1:\"a\";i:47;s:1:\"b\";i:16;s:1:\"c\";s:20:\"otherservices.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:41;a:5:{s:1:\"a\";i:48;s:1:\"b\";i:17;s:1:\"c\";s:12:\"banks.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:42;a:5:{s:1:\"a\";i:49;s:1:\"b\";i:17;s:1:\"c\";s:12:\"banks.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:43;a:5:{s:1:\"a\";i:50;s:1:\"b\";i:18;s:1:\"c\";s:23:\"banktransactions.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:44;a:5:{s:1:\"a\";i:51;s:1:\"b\";i:18;s:1:\"c\";s:23:\"banktransactions.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:5:{s:1:\"a\";i:52;s:1:\"b\";i:19;s:1:\"c\";s:16:\"customers.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:5:{s:1:\"a\";i:53;s:1:\"b\";i:19;s:1:\"c\";s:16:\"customers.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:5:{s:1:\"a\";i:54;s:1:\"b\";i:20;s:1:\"c\";s:15:\"invoices.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:5:{s:1:\"a\";i:55;s:1:\"b\";i:20;s:1:\"c\";s:16:\"invoices.receipt\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:5:{s:1:\"a\";i:56;s:1:\"b\";i:18;s:1:\"c\";s:22:\"banktransactions.claim\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:5:{s:1:\"a\";i:57;s:1:\"b\";i:21;s:1:\"c\";s:18:\"assessments.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:5:{s:1:\"a\";i:59;s:1:\"b\";i:21;s:1:\"c\";s:19:\"assessments.process\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:5:{s:1:\"a\";i:60;s:1:\"b\";i:21;s:1:\"c\";s:15:\"approval.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:5:{s:1:\"a\";i:61;s:1:\"b\";i:22;s:1:\"c\";s:20:\"registrations.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:5:{s:1:\"a\";i:62;s:1:\"b\";i:22;s:1:\"c\";s:21:\"registrations.approve\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:5:{s:1:\"a\";i:63;s:1:\"b\";i:23;s:1:\"c\";s:19:\"applications.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:5:{s:1:\"a\";i:64;s:1:\"b\";i:23;s:1:\"c\";s:20:\"applications.approve\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:5:{s:1:\"a\";i:65;s:1:\"b\";i:24;s:1:\"c\";s:14:\"session.access\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:58;a:5:{s:1:\"a\";i:66;s:1:\"b\";i:24;s:1:\"c\";s:14:\"session.modify\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:1:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"j\";i:1;s:1:\"c\";s:10:\"SuperAdmin\";s:1:\"d\";s:3:\"web\";}}}',1757618269);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificatetypes`
--

DROP TABLE IF EXISTS `certificatetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificatetypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificatetypes`
--

LOCK TABLES `certificatetypes` WRITE;
/*!40000 ALTER TABLE `certificatetypes` DISABLE KEYS */;
INSERT INTO `certificatetypes` VALUES (1,'REGISTRATION','2025-09-09 09:54:57','2025-09-09 09:54:57'),(2,'NEWAPPLICATION','2025-09-09 09:55:47','2025-09-09 09:55:47'),(3,'RENEWAL','2025-09-09 09:56:02','2025-09-09 09:56:02'),(5,'STUDENTREGISTRATION','2025-09-09 09:58:16','2025-09-09 09:58:16');
/*!40000 ALTER TABLE `certificatetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cities_province_id_foreign` (`province_id`),
  CONSTRAINT `cities_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'Gweru',1,'2025-08-17 14:35:40','2025-08-17 14:35:40');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'USD','active','2025-08-17 17:08:01','2025-08-17 17:08:01'),(2,'ZiG','inactive','2025-08-17 17:08:07','2025-08-17 17:08:07'),(3,'ZWG','active','2025-08-17 17:08:15','2025-08-17 17:08:15');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerapplications`
--

DROP TABLE IF EXISTS `customerapplications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerapplications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `customerprofession_id` bigint unsigned NOT NULL,
  `registertype_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_expiry_date` date DEFAULT NULL,
  `year` int DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `approvedby` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `applicationtype_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerapplications_customer_id_foreign` (`customer_id`),
  KEY `customerapplications_customerprofession_id_foreign` (`customerprofession_id`),
  KEY `customerapplications_registertype_id_foreign` (`registertype_id`),
  KEY `customerapplications_approvedby_foreign` (`approvedby`),
  CONSTRAINT `customerapplications_approvedby_foreign` FOREIGN KEY (`approvedby`) REFERENCES `users` (`id`),
  CONSTRAINT `customerapplications_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `customerapplications_registertype_id_foreign` FOREIGN KEY (`registertype_id`) REFERENCES `registertypes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerapplications`
--

LOCK TABLES `customerapplications` WRITE;
/*!40000 ALTER TABLE `customerapplications` DISABLE KEYS */;
INSERT INTO `customerapplications` VALUES (2,2,26,1,'REJECTED',NULL,NULL,2025,NULL,NULL,'2025-08-21 16:35:29','2025-08-29 14:10:10',1),(3,3,27,1,'PENDING',NULL,NULL,2025,NULL,NULL,'2025-08-22 10:34:44','2025-08-22 10:34:44',1),(4,6,28,1,'REJECTED',NULL,NULL,2025,NULL,NULL,'2025-08-29 14:30:01','2025-08-29 14:43:30',1),(5,6,29,1,'PENDING',NULL,NULL,2025,NULL,NULL,'2025-08-29 14:46:40','2025-08-29 14:48:40',1),(6,7,30,1,'APPROVED','CA-2025-4095-6','2024-12-31',2025,'2025-08-31',1,'2025-08-31 16:01:01','2025-08-31 18:23:29',1),(7,8,31,3,'PENDING',NULL,NULL,2025,NULL,NULL,'2025-09-09 10:10:41','2025-09-09 10:10:41',1);
/*!40000 ALTER TABLE `customerapplications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customercontacts`
--

DROP TABLE IF EXISTS `customercontacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customercontacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `primaryphone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secondaryphone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customercontacts_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customercontacts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customercontacts`
--

LOCK TABLES `customercontacts` WRITE;
/*!40000 ALTER TABLE `customercontacts` DISABLE KEYS */;
INSERT INTO `customercontacts` VALUES (1,2,'Benson ','sister','231232','23423443','benson.misi@gmail.com','2025-08-20 09:21:56','2025-08-20 09:22:55');
/*!40000 ALTER TABLE `customercontacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customeremployments`
--

DROP TABLE IF EXISTS `customeremployments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customeremployments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `companyname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contactperson` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customeremployments_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customeremployments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customeremployments`
--

LOCK TABLES `customeremployments` WRITE;
/*!40000 ALTER TABLE `customeremployments` DISABLE KEYS */;
INSERT INTO `customeremployments` VALUES (2,2,'PR2019192','ICT OFFICER  SOFTWARE DEVELOPMENT & INTERGRATION','2025-07-30','2025-08-23','0773454949','benson.misi@gmail.com','16832 stoneridge water falls','Benson Misi','2025-08-20 09:11:45','2025-08-20 09:11:45');
/*!40000 ALTER TABLE `customeremployments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessioncomments`
--

DROP TABLE IF EXISTS `customerprofessioncomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessioncomments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `commenttype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerprofessioncomments_customerprofession_id_foreign` (`customerprofession_id`),
  KEY `customerprofessioncomments_user_id_foreign` (`user_id`),
  CONSTRAINT `customerprofessioncomments_customerprofession_id_foreign` FOREIGN KEY (`customerprofession_id`) REFERENCES `customerprofessions` (`id`),
  CONSTRAINT `customerprofessioncomments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessioncomments`
--

LOCK TABLES `customerprofessioncomments` WRITE;
/*!40000 ALTER TABLE `customerprofessioncomments` DISABLE KEYS */;
INSERT INTO `customerprofessioncomments` VALUES (1,26,1,'Qualification Assessment','Qualification approved','2025-08-29 13:49:30','2025-08-29 13:49:30'),(2,26,1,'Qualification Assessment','Your qualifications do not meet desired standard','2025-08-29 13:53:42','2025-08-29 13:53:42'),(3,26,1,'Qualification Assessment','Qualifications do not meet standard','2025-08-29 13:58:27','2025-08-29 13:58:27'),(4,26,1,'Qualification Assessment','rejected','2025-08-29 14:10:10','2025-08-29 14:10:10'),(5,26,1,'Qualification Assessment','rejected','2025-08-29 14:11:45','2025-08-29 14:11:45'),(6,28,1,'Qualification Assessment','Qualifications do not meet standard','2025-08-29 14:43:30','2025-08-29 14:43:30'),(7,29,1,'Qualification Assessment','dfgdfgfdgd','2025-08-29 14:48:40','2025-08-29 14:48:40'),(8,29,1,'Qualification Assessment','Approved','2025-08-29 14:59:21','2025-08-29 14:59:21'),(9,29,1,'Qualification Assessment','APPROVED','2025-08-29 15:23:34','2025-08-29 15:23:34'),(10,30,1,'Qualification Assessment','Qualification approved','2025-08-31 17:19:01','2025-08-31 17:19:01'),(11,30,1,'Qualification Assessment','Registration approved','2025-08-31 17:52:44','2025-08-31 17:52:44'),(12,30,1,'Registration','Registration approved','2025-08-31 17:54:46','2025-08-31 17:54:46'),(13,30,1,'Registration','Registration approved','2025-08-31 17:57:38','2025-08-31 17:57:38'),(14,30,1,'Application','Application approved','2025-08-31 18:23:29','2025-08-31 18:23:29'),(15,33,1,'Registration','approved','2025-09-09 19:03:05','2025-09-09 19:03:05');
/*!40000 ALTER TABLE `customerprofessioncomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessiondocuments`
--

DROP TABLE IF EXISTS `customerprofessiondocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessiondocuments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `verifiedby` bigint unsigned NOT NULL,
  `approvedby` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerprofessiondocuments_customerprofession_id_foreign` (`customerprofession_id`),
  KEY `customerprofessiondocuments_document_id_foreign` (`document_id`),
  KEY `customerprofessiondocuments_verifiedby_foreign` (`verifiedby`),
  KEY `customerprofessiondocuments_approvedby_foreign` (`approvedby`),
  CONSTRAINT `customerprofessiondocuments_approvedby_foreign` FOREIGN KEY (`approvedby`) REFERENCES `users` (`id`),
  CONSTRAINT `customerprofessiondocuments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  CONSTRAINT `customerprofessiondocuments_verifiedby_foreign` FOREIGN KEY (`verifiedby`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessiondocuments`
--

LOCK TABLES `customerprofessiondocuments` WRITE;
/*!40000 ALTER TABLE `customerprofessiondocuments` DISABLE KEYS */;
INSERT INTO `customerprofessiondocuments` VALUES (1,1,2,'documents/Exyoa7935Mu6r2QO0inVDGmCBBinMl0RPAX4ZOhc.pdf','VERIFIED',1,NULL,'2025-08-21 13:13:10','2025-08-21 13:13:10'),(2,1,3,'documents/MuVx5KhPqmg8TUFzDTiePyy95jYtYHLjCtCeE7yM.pdf','VERIFIED',1,NULL,'2025-08-21 13:16:47','2025-08-21 13:16:47'),(5,26,2,'documents/AoaWeVGkuu4BjOULCBGmEcDHze7Jn3OxlOv614yl.pdf','VERIFIED',1,NULL,'2025-08-21 16:35:51','2025-08-21 16:35:51'),(6,26,3,'documents/Pmocm4mGv38ahScDx1J6dbM3NVfArri8SOWcjnk0.pdf','VERIFIED',1,NULL,'2025-08-21 16:35:55','2025-08-21 16:35:55'),(9,27,2,'documents/hN6ytHbJaJR0wiBoY7LtiDy3SLVmmjVxhZk4JWZQ.pdf','VERIFIED',1,NULL,'2025-08-22 10:59:42','2025-08-22 10:59:42'),(10,27,3,'documents/TAhsd4X3GlVBnG68o3YlflkTy5jnz7dvWi4mmxxj.pdf','VERIFIED',1,NULL,'2025-08-22 10:59:57','2025-08-22 10:59:57'),(11,28,2,'documents/guxLL0rOR8AwQad5XTjirVeo2QpOSJ8mneJkdBVt.pdf','VERIFIED',1,NULL,'2025-08-29 14:30:52','2025-08-29 14:30:52'),(12,28,3,'documents/tc0bOPWsSkHoo5DNNBorfRoe9IAuMsdabE9h0HGE.pdf','VERIFIED',1,NULL,'2025-08-29 14:30:58','2025-08-29 14:30:58'),(13,29,2,'documents/gN1M2vP2HSykjVh9hP5qzAua18Q7yVrDZdr0sZIS.pdf','VERIFIED',1,NULL,'2025-08-29 14:46:59','2025-08-29 14:46:59'),(14,29,3,'documents/cdsOvdDEhaZUW1qTgKl2uZpFOPpKgsK2iIRt6F0a.pdf','VERIFIED',1,NULL,'2025-08-29 14:47:02','2025-08-29 14:47:02'),(15,30,2,'documents/bGlCakxdJ5GiO8pDIWbT9qav3Y2t3vcGkilSbIpv.pdf','VERIFIED',1,NULL,'2025-08-31 16:01:18','2025-08-31 16:01:18'),(16,30,3,'documents/Hrha6Iy1Me35FoXLh1mVoc6dcirwnNlcS2RWxlS4.pdf','VERIFIED',1,NULL,'2025-08-31 16:01:23','2025-08-31 16:01:23'),(21,33,2,'documents/lgmbVfLf8Je6EPLGHSNRFpOF6KmRRdg1X4pYSvpL.pdf','VERIFIED',1,NULL,'2025-09-09 17:51:18','2025-09-09 17:51:18'),(22,33,3,'documents/bVSoJhQEhFxgt9C5VJ1rwoYUQWGtNzAtqUXWXSAO.pdf','VERIFIED',1,NULL,'2025-09-09 17:51:31','2025-09-09 17:51:31'),(23,34,2,'documents/mO5I5YEBsDlrKeC25m6vbkMLmrtRoJrzcae0psxU.pdf','VERIFIED',7,NULL,'2025-09-11 08:35:29','2025-09-11 08:35:29'),(24,34,3,'documents/xViVGdV9H7ro9KigUf17kH43ZfXdEogr6vumwREQ.pdf','VERIFIED',7,NULL,'2025-09-11 08:36:03','2025-09-11 08:36:03');
/*!40000 ALTER TABLE `customerprofessiondocuments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessioninstitutions`
--

DROP TABLE IF EXISTS `customerprofessioninstitutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessioninstitutions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisorphone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisoremail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_attached` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NO',
  `organization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customerprofession_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerprofessioninstitutions_customerprofession_id_foreign` (`customerprofession_id`),
  CONSTRAINT `customerprofessioninstitutions_customerprofession_id_foreign` FOREIGN KEY (`customerprofession_id`) REFERENCES `customerprofessions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessioninstitutions`
--

LOCK TABLES `customerprofessioninstitutions` WRITE;
/*!40000 ALTER TABLE `customerprofessioninstitutions` DISABLE KEYS */;
/*!40000 ALTER TABLE `customerprofessioninstitutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessionqualificationassessments`
--

DROP TABLE IF EXISTS `customerprofessionqualificationassessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessionqualificationassessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `user_id` bigint unsigned DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessionqualificationassessments`
--

LOCK TABLES `customerprofessionqualificationassessments` WRITE;
/*!40000 ALTER TABLE `customerprofessionqualificationassessments` DISABLE KEYS */;
INSERT INTO `customerprofessionqualificationassessments` VALUES (1,30,'APPROVED',1,'Qualification approved','2025-08-31 16:01:50','2025-08-31 17:19:01');
/*!40000 ALTER TABLE `customerprofessionqualificationassessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessionqualifications`
--

DROP TABLE IF EXISTS `customerprofessionqualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessionqualifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` bigint unsigned NOT NULL,
  `qualificationcategory_id` bigint unsigned NOT NULL,
  `qualificationlevel_id` bigint unsigned NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `verifiedby` bigint unsigned DEFAULT NULL,
  `approvedby` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institution` text COLLATE utf8mb4_unicode_ci,
  `year` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerprofessionqualifications_customerprofessionid_foreign` (`customerprofession_id`),
  KEY `customerprofessionqualifications_qualificationcategoryid_foreign` (`qualificationcategory_id`),
  KEY `customerprofessionqualifications_qualificationlevelid_foreign` (`qualificationlevel_id`),
  KEY `customerprofessionqualifications_verifiedby_foreign` (`verifiedby`),
  KEY `customerprofessionqualifications_approvedby_foreign` (`approvedby`),
  CONSTRAINT `customerprofessionqualifications_approvedby_foreign` FOREIGN KEY (`approvedby`) REFERENCES `users` (`id`),
  CONSTRAINT `customerprofessionqualifications_qualificationcategoryid_foreign` FOREIGN KEY (`qualificationcategory_id`) REFERENCES `qualificationcategories` (`id`),
  CONSTRAINT `customerprofessionqualifications_qualificationlevelid_foreign` FOREIGN KEY (`qualificationlevel_id`) REFERENCES `qualificationlevels` (`id`),
  CONSTRAINT `customerprofessionqualifications_verifiedby_foreign` FOREIGN KEY (`verifiedby`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessionqualifications`
--

LOCK TABLES `customerprofessionqualifications` WRITE;
/*!40000 ALTER TABLE `customerprofessionqualifications` DISABLE KEYS */;
INSERT INTO `customerprofessionqualifications` VALUES (2,1,1,6,'documents/nHNlfsZV7QCn9sAGrZELYjTAhsw7PJY6CdZ2qkHC.pdf','PENDING',NULL,NULL,'2025-08-21 14:02:16','2025-08-21 14:02:16','Clinical Assistants','Emerate Ambulance','2025'),(7,26,1,6,'documents/vNODgF7NY8Pa88ggApZgeQLwuzbtY2vMIuBV24xG.pdf','PENDING',NULL,NULL,'2025-08-21 16:36:21','2025-08-21 16:36:21','Clinical Assistants','Emerate Ambulance','2025'),(8,27,1,6,'documents/weA6dgooUzbJgWnvbbMs173S6f7DWtCyvV5YHmKA.pdf','PENDING',NULL,NULL,'2025-08-22 10:36:22','2025-08-22 10:36:22','Clinical Assistants','Emerate Ambulance','2025'),(9,28,1,5,'documents/ul7hNe3nGMOnEqt0uvvTXVSQKFy06jDRJAFsIJsB.pdf','PENDING',NULL,NULL,'2025-08-29 14:31:48','2025-08-29 14:31:48','Clinical Assistants','Emerate Ambulance','2025'),(10,29,1,5,'documents/0TDS10Pai4fyMm2GxG9EYOWqycU7NVrseGTnaw8q.pdf','PENDING',NULL,NULL,'2025-08-29 14:47:21','2025-08-29 14:47:21','Clinical Assistants','Emerate Ambulance','2025'),(11,30,1,5,'documents/DQWUmgwVyH7oToFGw2OGLCVjbSgcC5GIse3i2Off.pdf','PENDING',NULL,NULL,'2025-08-31 16:01:50','2025-08-31 16:01:50','Clinical Assistants','Emerate Ambulance','2025');
/*!40000 ALTER TABLE `customerprofessionqualifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerprofessions`
--

DROP TABLE IF EXISTS `customerprofessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerprofessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  `customertype_id` bigint unsigned NOT NULL,
  `employmentstatus_id` bigint unsigned NOT NULL,
  `employmentlocation_id` bigint unsigned NOT NULL,
  `uuid` text COLLATE utf8mb4_unicode_ci,
  `registertype_id` bigint unsigned NOT NULL,
  `year` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerprofessions_customer_id_foreign` (`customer_id`),
  KEY `customerprofessions_profession_id_foreign` (`profession_id`),
  KEY `customerprofessions_customertype_id_foreign` (`customertype_id`),
  KEY `customerprofessions_employmentstatus_id_foreign` (`employmentstatus_id`),
  KEY `customerprofessions_employmentlocation_id_foreign` (`employmentlocation_id`),
  KEY `customerprofessions_registertype_id_foreign` (`registertype_id`),
  CONSTRAINT `customerprofessions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `customerprofessions_customertype_id_foreign` FOREIGN KEY (`customertype_id`) REFERENCES `customertypes` (`id`),
  CONSTRAINT `customerprofessions_employmentlocation_id_foreign` FOREIGN KEY (`employmentlocation_id`) REFERENCES `employmentlocations` (`id`),
  CONSTRAINT `customerprofessions_employmentstatus_id_foreign` FOREIGN KEY (`employmentstatus_id`) REFERENCES `employmentstatuses` (`id`),
  CONSTRAINT `customerprofessions_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`),
  CONSTRAINT `customerprofessions_registertype_id_foreign` FOREIGN KEY (`registertype_id`) REFERENCES `registertypes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerprofessions`
--

LOCK TABLES `customerprofessions` WRITE;
/*!40000 ALTER TABLE `customerprofessions` DISABLE KEYS */;
INSERT INTO `customerprofessions` VALUES (26,2,2,1,1,1,'fca3e671-97ce-4fe3-90c8-5a0a3ff1be92',1,2025,'REJECTED',1,'2025-08-21 16:35:29','2025-08-29 14:11:45'),(27,3,2,1,1,2,'e3a93382-3bd2-4edb-9657-dbe78ef18eed',1,NULL,'AWAITING_QA',1,'2025-08-22 10:34:44','2025-08-22 11:04:45'),(28,6,2,1,1,2,'da4f3b90-4104-42a7-bf77-3146eaf9803d',1,2025,'REJECTED',1,'2025-08-29 14:30:01','2025-08-29 14:43:30'),(29,6,2,1,1,2,'0e86364a-d9f7-40e3-9520-12ac62591b02',1,2025,'PENDING',1,'2025-08-29 14:46:40','2025-08-29 15:44:59'),(30,7,2,1,1,2,'440cacf0-9248-43b1-be22-d0acf63e318c',1,2025,'APPROVED',1,'2025-08-31 16:01:01','2025-08-31 18:23:29'),(33,8,2,3,2,2,'4ffa6c5c-5d92-4b4f-8892-c3ec4218bbfe',3,2025,'APPROVED',1,'2025-09-09 16:02:16','2025-09-09 19:03:05'),(34,9,2,3,1,2,'20eba062-cbeb-46bd-a453-df7b249b94f4',3,2025,'PENDING',7,'2025-09-11 08:31:22','2025-09-11 08:31:22');
/*!40000 ALTER TABLE `customerprofessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerregistrations`
--

DROP TABLE IF EXISTS `customerregistrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerregistrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `customerprofession_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `certificatenumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificateexpirydate` date DEFAULT NULL,
  `year` int DEFAULT NULL,
  `registrationdate` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerregistrations_customer_id_foreign` (`customer_id`),
  KEY `customerregistrations_customerprofession_id_foreign` (`customerprofession_id`),
  CONSTRAINT `customerregistrations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerregistrations`
--

LOCK TABLES `customerregistrations` WRITE;
/*!40000 ALTER TABLE `customerregistrations` DISABLE KEYS */;
INSERT INTO `customerregistrations` VALUES (20,2,26,'REJECTED',NULL,NULL,2025,NULL,'2025-08-21 16:35:29','2025-08-29 14:10:10',NULL),(21,3,27,'PENDING',NULL,NULL,2025,NULL,'2025-08-22 10:34:44','2025-08-22 10:34:44',NULL),(22,6,28,'REJECTED',NULL,NULL,2025,NULL,'2025-08-29 14:30:01','2025-08-29 14:43:30',NULL),(23,6,29,'AWAITING',NULL,NULL,2025,NULL,'2025-08-29 14:46:40','2025-08-29 15:44:59',NULL),(24,7,30,'APPROVED','CA-2025-5101-24',NULL,2025,'2025-08-31','2025-08-31 16:01:01','2025-08-31 17:57:38',1),(25,8,31,'PENDING',NULL,NULL,2025,NULL,'2025-09-09 10:10:41','2025-09-09 10:10:41',NULL),(26,8,33,'APPROVED','CA-2025-5739-26',NULL,2025,'2025-09-09','2025-09-09 18:42:37','2025-09-09 19:03:05',1),(27,9,34,'PENDING',NULL,NULL,2025,NULL,'2025-09-11 08:36:45','2025-09-11 08:36:45',NULL);
/*!40000 ALTER TABLE `customerregistrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'placeholder.jpg',
  `uuid` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identificationtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificationnumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maritalstatus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `nationality_id` bigint unsigned NOT NULL,
  `province_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `employmentlocation_id` bigint unsigned DEFAULT NULL,
  `employmentstatus_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_nationality_id_foreign` (`nationality_id`),
  KEY `customers_province_id_foreign` (`province_id`),
  KEY `customers_city_id_foreign` (`city_id`),
  KEY `customers_employmentlocation_id_foreign` (`employmentlocation_id`),
  KEY `customers_employmentstatus_id_foreign` (`employmentstatus_id`),
  CONSTRAINT `customers_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `customers_employmentlocation_id_foreign` FOREIGN KEY (`employmentlocation_id`) REFERENCES `employmentlocations` (`id`),
  CONSTRAINT `customers_employmentstatus_id_foreign` FOREIGN KEY (`employmentstatus_id`) REFERENCES `employmentstatuses` (`id`),
  CONSTRAINT `customers_nationality_id_foreign` FOREIGN KEY (`nationality_id`) REFERENCES `nationalities` (`id`),
  CONSTRAINT `customers_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (2,'customers/hmokpWIHv9qzVgFaql3mK1AJJtPXDdm5iC3ovZ5O.jpg','4e4bd9c0-9e58-420c-8ef9-2dadb3e150dd','Benson ','Misi',NULL,'MLCSCZ20252','NATIONAL_ID','29249778L26','M','MARRIED','benson.misi@gmail.com','0773454949','16832 stoneridge water falls','Gweru','2025-07-30',1,1,1,2,1,'2025-08-19 18:18:00','2025-08-20 16:59:23'),(3,'customers/DabwmQ9lVWIqcUFa91zB1Rkn8h3snjFp7rQBd47Y.jpg','c62e4f86-1201-4673-9a7b-5d909bce95c6','Tendai ','Moyo',NULL,'MLCSCZ20254','NATIONAL_ID','29249778L27','M','MARRIED','vimbainashe.misi@gmail.com','999999999','16832 stoneridge water falls','Gweru','2025-08-21',1,1,1,2,1,'2025-08-22 10:33:30','2025-08-22 10:33:30'),(6,'customers/8INNX5j7iAIxRfm0d8KPrRKvAWCC4hcyEWT9LjfU.jpg','95afaa32-42e8-493c-ba22-7e9d7fb21375','John','Doe',NULL,'MLCSCZ20257','NATIONAL_ID','29249778L30','MALE','SINGLE','benson.misi@outlook.com','0773454949','16832 stoneridge water falls','Gweru','2025-08-14',1,1,1,2,1,'2025-08-29 14:29:00','2025-08-29 14:29:00'),(7,NULL,'57e6ae8b-2f12-46ed-819e-c5f693c45ee7','Cloe ','Sibanda',NULL,'MLCSCZ20258','NATIONAL_ID','29249778L90','FEMALE','MARRIED','cleo.sibanda@gmail.com','0775474661','16832 stoneridge water falls','Gweru','1987-01-07',1,1,1,2,1,'2025-08-31 16:00:23','2025-08-31 16:00:23'),(8,'customers/WpcfRB9XF8ZQAxNPx8cLwJ8PW8FxiuPb1jlV8WVO.jpg','0846a66a-261c-4756-8469-be7427817856','Tanisha','Misi',NULL,'MLCSCZ20259','NATIONAL_ID','29249778L99','FEMALE','SINGLE','tanisha.misi@gmail.com','0773454949','16832 stoneridge water falls','Gweru','2025-09-08',1,1,1,2,1,'2025-09-08 16:17:22','2025-09-08 16:17:22'),(9,'placeholder.jpg','bd6d0202-70bd-4f26-a040-a49a0f9fa069','Tendai','Towo',NULL,'MLCSCZ202510','NATIONAL_ID','29249778L98','FEMALE','DIVORCED','tendai.towo@gmail.com','0773454949','16832 stoneridge water falls','Gweru','1987-01-07',1,1,1,2,1,'2025-09-11 07:38:14','2025-09-11 07:38:14');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customertype_registertypes`
--

DROP TABLE IF EXISTS `customertype_registertypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customertype_registertypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customertype_id` bigint unsigned NOT NULL,
  `registertype_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customertype_registertypes_customertype_id_foreign` (`customertype_id`),
  KEY `customertype_registertypes_registertype_id_foreign` (`registertype_id`),
  CONSTRAINT `customertype_registertypes_customertype_id_foreign` FOREIGN KEY (`customertype_id`) REFERENCES `customertypes` (`id`),
  CONSTRAINT `customertype_registertypes_registertype_id_foreign` FOREIGN KEY (`registertype_id`) REFERENCES `registertypes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customertype_registertypes`
--

LOCK TABLES `customertype_registertypes` WRITE;
/*!40000 ALTER TABLE `customertype_registertypes` DISABLE KEYS */;
INSERT INTO `customertype_registertypes` VALUES (2,1,1,'2025-08-17 09:04:56','2025-08-17 09:04:56'),(3,1,2,'2025-08-17 09:05:01','2025-08-17 09:05:01'),(5,3,3,'2025-08-17 09:07:20','2025-08-17 09:07:20'),(6,4,2,'2025-08-21 08:23:27','2025-08-21 08:23:27');
/*!40000 ALTER TABLE `customertype_registertypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customertypes`
--

DROP TABLE IF EXISTS `customertypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customertypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customertypes`
--

LOCK TABLES `customertypes` WRITE;
/*!40000 ALTER TABLE `customertypes` DISABLE KEYS */;
INSERT INTO `customertypes` VALUES (1,'Practitioner','2025-08-16 20:21:12','2025-08-16 20:21:12'),(3,'Student','2025-08-16 20:22:59','2025-08-16 20:22:59'),(4,'Intern','2025-08-21 08:23:18','2025-08-21 08:23:18');
/*!40000 ALTER TABLE `customertypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customerusers`
--

DROP TABLE IF EXISTS `customerusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customerusers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customerusers_customer_id_foreign` (`customer_id`),
  KEY `customerusers_user_id_foreign` (`user_id`),
  CONSTRAINT `customerusers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `customerusers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customerusers`
--

LOCK TABLES `customerusers` WRITE;
/*!40000 ALTER TABLE `customerusers` DISABLE KEYS */;
INSERT INTO `customerusers` VALUES (2,6,4,'2025-08-29 14:29:02','2025-08-29 14:29:02'),(3,7,5,'2025-08-31 16:00:25','2025-08-31 16:00:25'),(4,8,6,'2025-09-08 16:17:23','2025-09-08 16:17:23'),(5,9,7,'2025-09-11 07:38:14','2025-09-11 07:38:14');
/*!40000 ALTER TABLE `customerusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customrtypecertificatetypes`
--

DROP TABLE IF EXISTS `customrtypecertificatetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customrtypecertificatetypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customrtype_id` int NOT NULL,
  `certificatetype_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customrtypecertificatetypes`
--

LOCK TABLES `customrtypecertificatetypes` WRITE;
/*!40000 ALTER TABLE `customrtypecertificatetypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `customrtypecertificatetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tire_id` bigint unsigned NOT NULL,
  `lowerlimit` int NOT NULL,
  `upperlimit` int NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discounts_tire_id_foreign` (`tire_id`),
  CONSTRAINT `discounts_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
INSERT INTO `discounts` VALUES (1,1,50,64,50.00,'2025-08-18 16:26:43','2025-08-18 16:26:43');
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentrequirements`
--

DROP TABLE IF EXISTS `documentrequirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentrequirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `tire_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customertype_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentrequirements_document_id_foreign` (`document_id`),
  KEY `documentrequirements_tire_id_foreign` (`tire_id`),
  CONSTRAINT `documentrequirements_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  CONSTRAINT `documentrequirements_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentrequirements`
--

LOCK TABLES `documentrequirements` WRITE;
/*!40000 ALTER TABLE `documentrequirements` DISABLE KEYS */;
INSERT INTO `documentrequirements` VALUES (4,2,2,'2025-08-17 16:46:04','2025-08-17 16:46:04',NULL),(5,3,2,'2025-08-17 16:46:07','2025-08-17 16:46:07',NULL),(6,2,3,'2025-08-17 16:46:17','2025-08-17 16:46:17',NULL),(7,3,3,'2025-08-17 16:46:19','2025-08-17 16:46:19',NULL),(8,2,1,'2025-09-08 17:12:48','2025-09-08 17:12:48',1),(9,2,1,'2025-09-08 17:12:52','2025-09-08 17:12:52',3),(10,2,1,'2025-09-08 17:12:57','2025-09-08 17:12:57',4),(11,3,1,'2025-09-08 17:13:05','2025-09-08 17:13:05',4),(12,3,1,'2025-09-08 17:13:09','2025-09-08 17:13:09',3);
/*!40000 ALTER TABLE `documentrequirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (2,'National ID','2025-08-17 07:27:44','2025-08-17 07:27:44'),(3,'Birth certificate','2025-08-17 07:28:00','2025-08-17 07:28:00'),(4,'Programme recommendation letter','2025-08-21 12:46:57','2025-08-21 12:46:57');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `electioncandidates`
--

DROP TABLE IF EXISTS `electioncandidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electioncandidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `electionposition_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `createdby` bigint unsigned NOT NULL,
  `updatedby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `electioncandidates_electionposition_id_foreign` (`electionposition_id`),
  KEY `electioncandidates_customer_id_foreign` (`customer_id`),
  KEY `electioncandidates_createdby_foreign` (`createdby`),
  KEY `electioncandidates_updatedby_foreign` (`updatedby`),
  CONSTRAINT `electioncandidates_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `electioncandidates_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `electioncandidates_electionposition_id_foreign` FOREIGN KEY (`electionposition_id`) REFERENCES `electionpositions` (`id`),
  CONSTRAINT `electioncandidates_updatedby_foreign` FOREIGN KEY (`updatedby`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `electioncandidates`
--

LOCK TABLES `electioncandidates` WRITE;
/*!40000 ALTER TABLE `electioncandidates` DISABLE KEYS */;
/*!40000 ALTER TABLE `electioncandidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `electionpositions`
--

DROP TABLE IF EXISTS `electionpositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electionpositions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `election_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `createdby` bigint unsigned NOT NULL,
  `updatedby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `electionpositions_election_id_foreign` (`election_id`),
  KEY `electionpositions_createdby_foreign` (`createdby`),
  KEY `electionpositions_updatedby_foreign` (`updatedby`),
  CONSTRAINT `electionpositions_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `electionpositions_election_id_foreign` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`),
  CONSTRAINT `electionpositions_updatedby_foreign` FOREIGN KEY (`updatedby`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `electionpositions`
--

LOCK TABLES `electionpositions` WRITE;
/*!40000 ALTER TABLE `electionpositions` DISABLE KEYS */;
/*!40000 ALTER TABLE `electionpositions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elections`
--

DROP TABLE IF EXISTS `elections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `createdby` bigint unsigned NOT NULL,
  `updatedby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `elections_createdby_foreign` (`createdby`),
  KEY `elections_updatedby_foreign` (`updatedby`),
  CONSTRAINT `elections_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `elections_updatedby_foreign` FOREIGN KEY (`updatedby`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elections`
--

LOCK TABLES `elections` WRITE;
/*!40000 ALTER TABLE `elections` DISABLE KEYS */;
/*!40000 ALTER TABLE `elections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `electionvotes`
--

DROP TABLE IF EXISTS `electionvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electionvotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `election_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `electionposition_id` bigint unsigned NOT NULL,
  `electioncandidate_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `electionvotes_election_id_foreign` (`election_id`),
  KEY `electionvotes_customer_id_foreign` (`customer_id`),
  KEY `electionvotes_electionposition_id_foreign` (`electionposition_id`),
  KEY `electionvotes_electioncandidate_id_foreign` (`electioncandidate_id`),
  CONSTRAINT `electionvotes_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `electionvotes_election_id_foreign` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`),
  CONSTRAINT `electionvotes_electioncandidate_id_foreign` FOREIGN KEY (`electioncandidate_id`) REFERENCES `electioncandidates` (`id`),
  CONSTRAINT `electionvotes_electionposition_id_foreign` FOREIGN KEY (`electionposition_id`) REFERENCES `electionpositions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `electionvotes`
--

LOCK TABLES `electionvotes` WRITE;
/*!40000 ALTER TABLE `electionvotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `electionvotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employmentlocations`
--

DROP TABLE IF EXISTS `employmentlocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employmentlocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employmentlocations`
--

LOCK TABLES `employmentlocations` WRITE;
/*!40000 ALTER TABLE `employmentlocations` DISABLE KEYS */;
INSERT INTO `employmentlocations` VALUES (1,'FOREIGN','2025-08-17 07:35:58','2025-08-17 07:35:58'),(2,'LOCAL','2025-08-17 07:36:05','2025-08-17 07:36:05');
/*!40000 ALTER TABLE `employmentlocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employmentstatuses`
--

DROP TABLE IF EXISTS `employmentstatuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employmentstatuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employmentstatuses`
--

LOCK TABLES `employmentstatuses` WRITE;
/*!40000 ALTER TABLE `employmentstatuses` DISABLE KEYS */;
INSERT INTO `employmentstatuses` VALUES (1,'Practising','2025-08-17 07:41:25','2025-08-21 08:21:31'),(2,'Not-Practising','2025-08-17 07:41:32','2025-08-21 08:21:46');
/*!40000 ALTER TABLE `employmentstatuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exchangerates`
--

DROP TABLE IF EXISTS `exchangerates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exchangerates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `base_currency_id` bigint unsigned NOT NULL,
  `secondary_currency_id` bigint unsigned NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exchangerates_base_currency_id_foreign` (`base_currency_id`),
  KEY `exchangerates_secondary_currency_id_foreign` (`secondary_currency_id`),
  CONSTRAINT `exchangerates_base_currency_id_foreign` FOREIGN KEY (`base_currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `exchangerates_secondary_currency_id_foreign` FOREIGN KEY (`secondary_currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exchangerates`
--

LOCK TABLES `exchangerates` WRITE;
/*!40000 ALTER TABLE `exchangerates` DISABLE KEYS */;
INSERT INTO `exchangerates` VALUES (3,1,1,1.00,'2025-08-17 17:50:26','2025-08-17 17:50:36','2025-01-01','2033-12-31',1,'2025-08-17 17:50:36'),(4,1,1,1.00,'2025-08-17 17:51:19','2025-08-17 17:51:19','2025-01-01','2045-12-31',1,NULL),(5,1,3,26.50,'2025-08-21 18:08:26','2025-08-21 18:08:26','2025-08-01','2025-08-31',1,NULL);
/*!40000 ALTER TABLE `exchangerates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settlementsplit_id` int DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` int NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `createdby` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `year` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_currency_id_foreign` (`currency_id`),
  KEY `invoices_createdby_foreign` (`createdby`),
  CONSTRAINT `invoices_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (16,2,1,'New Application','INV-2025-4561-26',NULL,'customerprofession',26,'7c9e7b79-000b-4139-b9c2-dd5a8baecba3',200.00,1,'CANCELLED',2025,'2025-08-21 16:35:29','2025-08-29 14:10:10'),(17,2,1,'Qualification Assessment','INV-2025-3346-26',NULL,'customerprofession',26,'47b55738-4c33-43f3-bdce-27d110bf4739',12.00,1,'PAID',2025,'2025-08-21 16:36:21','2025-08-22 09:06:05'),(18,3,1,'Registration','INV-2025-9915-27',5,'customerprofession',27,'609d7e39-e397-41ad-ad3a-e6193664340d',12.00,1,'PENDING',2025,'2025-08-22 10:34:44','2025-08-22 10:34:44'),(19,3,1,'New Application','INV-2025-3273-27',NULL,'customerprofession',27,'2af09262-3bb3-4068-a3cb-e0a593d320a6',250.00,1,'PENDING',2025,'2025-08-22 10:34:44','2025-08-22 10:34:44'),(20,3,1,'Qualification Assessment','INV-2025-7068-27',NULL,'customerprofession',27,'efe9e0d9-2031-4de9-8a05-166f661800d0',12.00,1,'PAID',2025,'2025-08-22 10:36:22','2025-08-22 11:04:45'),(21,6,1,'Registration','INV-2025-3508-28',5,'customerprofession',28,'917310fe-9c48-4532-9c6e-80281f84e12a',12.00,1,'CANCELLED',2025,'2025-08-29 14:30:01','2025-08-29 14:43:30'),(22,6,1,'New Application','INV-2025-6257-28',NULL,'customerprofession',28,'482e73c6-cf89-407b-b7fb-98d09e53f254',250.00,1,'CANCELLED',2025,'2025-08-29 14:30:01','2025-08-29 14:43:30'),(23,6,1,'Qualification Assessment','INV-2025-5587-28',NULL,'customerprofession',28,'cc53511c-e02b-4322-82ff-b23f3d968fff',12.00,1,'PAID',2025,'2025-08-29 14:31:48','2025-08-29 14:34:58'),(24,6,1,'Registration','INV-2025-7003-29',5,'customerprofession',29,'247a605b-529b-4b1c-bb41-5f9e14139e35',12.00,1,'PAID',2025,'2025-08-29 14:46:40','2025-08-29 15:40:13'),(25,6,1,'New Application','INV-2025-6439-29',NULL,'customerprofession',29,'ca8c032e-77a3-44bc-8ed5-cff1e6db378f',250.00,1,'PENDING',2025,'2025-08-29 14:46:40','2025-08-29 16:01:29'),(26,6,1,'Qualification Assessment','INV-2025-9779-29',NULL,'customerprofession',29,'823b2f45-63fe-4f1d-9a8b-81c44f08a58b',12.00,1,'PAID',2025,'2025-08-29 14:47:21','2025-08-29 14:48:18'),(27,7,1,'Registration','INV-2025-3631-30',5,'customerprofession',30,'fdd7a7bd-6417-4229-8886-8f96ebb45186',12.00,1,'PAID',2025,'2025-08-31 16:01:01','2025-08-31 17:20:35'),(28,7,1,'New Application','INV-2025-6708-30',NULL,'customerprofession',30,'79ac1424-e848-4ff3-a665-5d7ee4b04ab0',250.00,1,'PAID',2025,'2025-08-31 16:01:01','2025-08-31 18:00:32'),(29,7,1,'Qualification Assessment','INV-2025-9127-30',NULL,'customerprofession',30,'3933996f-0df3-433d-b15c-d466c5fa4476',12.00,1,'PAID',2025,'2025-08-31 16:01:50','2025-08-31 17:15:23'),(30,8,1,'Registration','INV-2025-4370-31',5,'customerprofession',31,'ada62105-cece-4597-8fa6-ed6f01a97ebc',10.00,1,'PENDING',2025,'2025-09-09 10:10:41','2025-09-09 10:10:41'),(31,8,1,'Registration','INV-2025-5951-33',5,'customerprofession',33,'758fe0f2-e924-4dd1-9a59-312f38147744',10.00,1,'PAID',2025,'2025-09-09 18:42:37','2025-09-09 19:01:39'),(32,9,1,'Registration','INV-2025-2167-34',5,'customerprofession',34,'1d304c26-ae83-4742-8004-8bd0ace8fa0e',10.00,7,'PENDING',2025,'2025-09-11 08:36:45','2025-09-11 08:36:45');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"75978677-75c2-43ef-90b7-28522eac7a6e\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:4:\\\"Test\\\";s:8:\\\"\\u0000*\\u0000email\\\";s:15:\\\"test@test.co.zw\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:8:\\\"iRGI9zQs\\\";s:2:\\\"id\\\";s:36:\\\"363942dc-bbee-465b-8b0f-81e6809112f2\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1755264256,\"delay\":null}',0,NULL,1755264257,1755264257),(2,'default','{\"uuid\":\"f34e15b9-e46e-4dc3-a34f-19cfcf1fd61f\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:4:\\\"Test\\\";s:8:\\\"\\u0000*\\u0000email\\\";s:15:\\\"test@test.co.zw\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:8:\\\"Ehlnfg0w\\\";s:2:\\\"id\\\";s:36:\\\"f86bbb80-bc8a-49da-8372-a3b24d5fc1a3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1755270794,\"delay\":null}',0,NULL,1755270794,1755270794),(3,'default','{\"uuid\":\"c2cbbd0c-419e-4fce-9fa7-3d4575f2a320\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:4:\\\"John\\\";s:8:\\\"\\u0000*\\u0000email\\\";s:23:\\\"benson.misi@outlook.com\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:8:\\\"mkLgMl5V\\\";s:2:\\\"id\\\";s:36:\\\"8446831e-6f9b-4e0d-928b-3154d27ec7ca\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1756484942,\"delay\":null}',0,NULL,1756484942,1756484942),(4,'default','{\"uuid\":\"61568a0d-8525-4fc7-abac-df22aabfc37d\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:5;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"Cloe \\\";s:8:\\\"\\u0000*\\u0000email\\\";s:22:\\\"cleo.sibanda@gmail.com\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:8:\\\"bRhSbTR6\\\";s:2:\\\"id\\\";s:36:\\\"2557a838-ae14-44d5-9c1a-335f92b27b7b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1756663225,\"delay\":null}',0,NULL,1756663225,1756663225),(5,'default','{\"uuid\":\"bb11041b-12d7-42d9-88ba-9bbc3bce318b\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:7:\\\"Tanisha\\\";s:8:\\\"\\u0000*\\u0000email\\\";s:22:\\\"tanisha.misi@gmail.com\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:8:\\\"PocssPB0\\\";s:2:\\\"id\\\";s:36:\\\"91c80ae1-33b4-417e-a123-48d0eeb88b61\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1757355443,\"delay\":null}',0,NULL,1757355443,1757355443),(6,'default','{\"uuid\":\"a3244b81-27ea-4cde-a967-250f941db439\",\"displayName\":\"App\\\\Notifications\\\\AccountcreatedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:44:\\\"App\\\\Notifications\\\\AccountcreatedNotification\\\":4:{s:7:\\\"\\u0000*\\u0000name\\\";s:6:\\\"Tendai\\\";s:8:\\\"\\u0000*\\u0000email\\\";s:21:\\\"tendai.towo@gmail.com\\\";s:11:\\\"\\u0000*\\u0000password\\\";s:12:\\\"Tanisha1315@\\\";s:2:\\\"id\\\";s:36:\\\"a038756e-992b-4895-8f8a-06d29a2816a1\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1757532270,\"delay\":null}',0,NULL,1757532270,1757532270);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manualpayments`
--

DROP TABLE IF EXISTS `manualpayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manualpayments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `mode` enum('CASH','SWIPE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CASH',
  `amount` decimal(10,2) NOT NULL,
  `receipted_by` int NOT NULL,
  `year` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manualpayments_customer_id_foreign` (`customer_id`),
  KEY `manualpayments_currency_id_foreign` (`currency_id`),
  CONSTRAINT `manualpayments_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `manualpayments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manualpayments`
--

LOCK TABLES `manualpayments` WRITE;
/*!40000 ALTER TABLE `manualpayments` DISABLE KEYS */;
/*!40000 ALTER TABLE `manualpayments` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_13_192347_create_accounttypes_table',1),(5,'2025_08_13_192406_create_roles_table',1),(6,'2025_08_13_192422_create_systemmodules_table',1),(7,'2025_08_13_192435_create_submodules_table',1),(8,'2025_08_13_192450_create_permissions_table',1),(9,'2025_08_13_192540_create_permission_roles_table',1),(10,'2025_08_13_192559_create_role_users_table',1),(11,'2025_08_13_192731_create_registertypes_table',1),(12,'2025_08_13_192747_create_customertypes_table',1),(13,'2025_08_13_192818_create_customertype_registertypes_table',1),(14,'2025_08_13_192858_create_employmentlocations_table',1),(15,'2025_08_13_192921_create_documents_table',1),(16,'2025_08_13_192944_create_qualificationcategories_table',1),(17,'2025_08_13_193023_create_employmentstatuses_table',1),(18,'2025_08_13_193035_create_nationalities_table',1),(19,'2025_08_13_193044_create_provinces_table',1),(20,'2025_08_13_193050_create_cities_table',1),(21,'2025_08_13_193301_create_customers_table',1),(22,'2025_08_13_193344_create_tires_table',1),(23,'2025_08_13_193405_create_professions_table',1),(24,'2025_08_13_193422_create_professiondocuments_table',1),(25,'2025_08_13_193442_create_documentrequirements_table',1),(26,'2025_08_13_193648_create_currencies_table',1),(27,'2025_08_13_193700_create_applicationfees_table',1),(28,'2025_08_13_193937_create_penalties_table',1),(29,'2025_08_13_194024_create_discounts_table',1),(30,'2025_08_13_194052_create_registrationfees_table',1),(31,'2025_08_13_194123_create_otherservices_table',1),(32,'2025_08_13_194130_create_otherservicedocuments_table',1),(33,'2025_08_13_194136_create_otherservicefees_table',1),(34,'2025_08_13_194241_create_paymentchannels_table',1),(35,'2025_08_13_194318_create_exchangerates_table',1),(36,'2025_08_13_194444_create_settlementsplits_table',1),(37,'2025_08_13_194552_create_paymentchannelparameters_table',1),(39,'2025_08_13_194912_create_customerregistrations_table',1),(40,'2025_08_13_194951_create_qualificationlevels_table',2),(41,'2025_08_13_194952_create_customerprofessionqualifications_table',3),(42,'2025_08_13_195007_create_customerapplications_table',3),(43,'2025_08_13_195036_create_customerprofessiondocuments_table',3),(44,'2025_08_13_200515_create_invoices_table',3),(45,'2025_08_13_200553_create_suspenses_table',3),(46,'2025_08_13_200554_create_receipts_table',3),(47,'2025_08_13_200844_create_paynowtransactions_table',3),(48,'2025_08_13_200938_create_banktransactions_table',3),(49,'2025_08_13_201013_create_elections_table',3),(50,'2025_08_13_201032_create_electionpositions_table',3),(51,'2025_08_13_201149_create_electioncandidates_table',3),(52,'2025_08_13_201205_create_electionvotes_table',3),(53,'2025_08_13_201335_create_customerusers_table',3),(56,'2025_08_13_222339_create_personal_access_tokens_table',5),(57,'2025_08_13_215727_create_permission_tables',6),(58,'2025_08_19_130300_create_banks_table',7),(59,'2025_08_19_130307_create_bankaccounts_table',7),(60,'2025_08_20_083224_create_customeremployments_table',8),(61,'2025_08_20_083349_create_customercontacts_table',8),(62,'2025_08_20_184003_create_registrationnumbers_table',9),(64,'2025_08_20_190525_create_manualpayments_table',10),(65,'2025_08_13_194832_create_customerprofessions_table',11),(66,'2025_08_21_204302_create_proofofpayments_table',12),(68,'2025_08_29_140901_create_customerprofessioncomments_table',13),(71,'2025_08_31_165246_create_customerprofessionqualificationassessments_table',14),(73,'2025_09_08_182538_create_customerprofessioninstitutions_table',15),(74,'2025_09_08_183956_create_professionconditions_table',16),(75,'2025_09_08_192714_create_certificatetypes_table',16),(108,'2025_09_08_192824_create_customrtypecertificatetypes_table',17),(109,'2025_09_09_151857_create_studentregistrationfees_table',17),(110,'2025_09_09_152457_create_studentqualifications_table',17),(111,'2025_09_09_152513_create_studentplacements_table',17),(112,'2025_09_09_153626_create_studentqualificationdocuments_table',17),(113,'2025_09_10_110059_create_applicationtypes_table',18),(118,'2025_09_10_144833_create_mycdps_table',19),(119,'2025_09_10_144839_create_mycdpattachments_table',19),(120,'2025_09_10_152931_create_applicationsessions_table',20);
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
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
INSERT INTO `model_has_permissions` VALUES (4,'App\\Models\\User',1);
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
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(4,'App\\Models\\User',3),(3,'App\\Models\\User',7);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mycdpattachments`
--

DROP TABLE IF EXISTS `mycdpattachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mycdpattachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mycdp_id` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mycdpattachments`
--

LOCK TABLES `mycdpattachments` WRITE;
/*!40000 ALTER TABLE `mycdpattachments` DISABLE KEYS */;
INSERT INTO `mycdpattachments` VALUES (2,1,'PROGRAMME','mycdp/n6VURotZNHkCz9wyCXizeldZpftAaROllp1KwYcu.pdf','2025-09-10 15:58:15','2025-09-10 15:58:15'),(3,1,'REGISTER','mycdp/Hs3C0FK559dnsecu7kEjAd3P4EwZg8hIrVpAbqxp.pdf','2025-09-10 15:58:20','2025-09-10 15:58:20'),(4,1,'CERTIFICATE','mycdp/z2SxZI7DfN5Tz3lnc7xvlc36foiM4POkcMiWGCt6.pdf','2025-09-10 15:58:23','2025-09-10 15:58:23');
/*!40000 ALTER TABLE `mycdpattachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mycdps`
--

DROP TABLE IF EXISTS `mycdps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mycdps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `durationunit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `assessed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mycdps`
--

LOCK TABLES `mycdps` WRITE;
/*!40000 ALTER TABLE `mycdps` DISABLE KEYS */;
INSERT INTO `mycdps` VALUES (1,30,'PROGRAMME 1: GOVERNANCE AND ADMINISTRATIONs',2025,'sdfafsdsfsfdassdfasdssdffdsfdsf test','PHYSICAL',NULL,2,'DAYS',1,'AWAITING_ASSESSMENT',NULL,NULL,'2025-09-10 14:28:38','2025-09-10 16:02:40');
/*!40000 ALTER TABLE `mycdps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nationalities`
--

DROP TABLE IF EXISTS `nationalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nationalities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nationalities`
--

LOCK TABLES `nationalities` WRITE;
/*!40000 ALTER TABLE `nationalities` DISABLE KEYS */;
INSERT INTO `nationalities` VALUES (1,'Zimbabwe','2025-08-17 14:26:10','2025-08-17 14:26:10');
/*!40000 ALTER TABLE `nationalities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otherservicedocuments`
--

DROP TABLE IF EXISTS `otherservicedocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otherservicedocuments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `otherservice_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otherservicedocuments_otherservice_id_foreign` (`otherservice_id`),
  KEY `otherservicedocuments_document_id_foreign` (`document_id`),
  CONSTRAINT `otherservicedocuments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  CONSTRAINT `otherservicedocuments_otherservice_id_foreign` FOREIGN KEY (`otherservice_id`) REFERENCES `otherservices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otherservicedocuments`
--

LOCK TABLES `otherservicedocuments` WRITE;
/*!40000 ALTER TABLE `otherservicedocuments` DISABLE KEYS */;
INSERT INTO `otherservicedocuments` VALUES (2,2,3,'2025-08-19 10:04:00','2025-08-19 10:04:00');
/*!40000 ALTER TABLE `otherservicedocuments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otherservices`
--

DROP TABLE IF EXISTS `otherservices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otherservices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generalledger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generatecertificate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `requireapproval` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `expiretype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otherservices`
--

LOCK TABLES `otherservices` WRITE;
/*!40000 ALTER TABLE `otherservices` DISABLE KEYS */;
INSERT INTO `otherservices` VALUES (2,1,'Qualification Assessment','12323','NO','YES','LIFETIME','12','2025-08-19 08:35:16','2025-08-19 08:35:16');
/*!40000 ALTER TABLE `otherservices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paymentchannelparameters`
--

DROP TABLE IF EXISTS `paymentchannelparameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentchannelparameters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paymentchannel_id` bigint unsigned NOT NULL,
  `currency_id` int DEFAULT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paymentchannelparameters_paymentchannel_id_foreign` (`paymentchannel_id`),
  CONSTRAINT `paymentchannelparameters_paymentchannel_id_foreign` FOREIGN KEY (`paymentchannel_id`) REFERENCES `paymentchannels` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paymentchannelparameters`
--

LOCK TABLES `paymentchannelparameters` WRITE;
/*!40000 ALTER TABLE `paymentchannelparameters` DISABLE KEYS */;
INSERT INTO `paymentchannelparameters` VALUES (1,2,1,'IntegrationId','15935','2025-08-18 14:24:48','2025-08-18 14:24:48'),(2,2,1,'Integrationkey','103522ac-a2a0-4f99-a3d0-1a15a34160a0','2025-08-18 14:25:46','2025-08-20 15:19:00'),(3,2,3,'IntegrationId','15935','2025-08-18 14:25:59','2025-08-18 14:25:59'),(5,2,3,'Integrationkey','103522ac-a2a0-4f99-a3d0-1a15a34160a0','2025-08-18 14:29:30','2025-08-20 15:19:10');
/*!40000 ALTER TABLE `paymentchannelparameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paymentchannels`
--

DROP TABLE IF EXISTS `paymentchannels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentchannels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `showpublic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paymentchannels`
--

LOCK TABLES `paymentchannels` WRITE;
/*!40000 ALTER TABLE `paymentchannels` DISABLE KEYS */;
INSERT INTO `paymentchannels` VALUES (1,'Cash','2025-08-18 13:33:37',NULL,'N','2025-08-18 14:14:37'),(2,'PAYNOW','2025-08-18 14:14:56','paymentchannels/pWLKVj4nqSsIbsM0wcxEnYn8pCKKzVy4oWoqfiGI.png','Y','2025-08-20 11:14:00');
/*!40000 ALTER TABLE `paymentchannels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paynowtransactions`
--

DROP TABLE IF EXISTS `paynowtransactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paynowtransactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pollurl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `amount` decimal(10,2) NOT NULL,
  `createdby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paynowtransactions_customer_id_foreign` (`customer_id`),
  KEY `paynowtransactions_currency_id_foreign` (`currency_id`),
  KEY `paynowtransactions_createdby_foreign` (`createdby`),
  CONSTRAINT `paynowtransactions_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `paynowtransactions_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `paynowtransactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paynowtransactions`
--

LOCK TABLES `paynowtransactions` WRITE;
/*!40000 ALTER TABLE `paynowtransactions` DISABLE KEYS */;
INSERT INTO `paynowtransactions` VALUES (8,3,1,'c42b6ae3-6a96-4262-8f9d-952ff5d384ac','https://www.paynow.co.zw/Interface/CheckPayment/?guid=6d300c07-ad53-427a-b881-26738b170cfb','PAID',200.00,1,'2025-08-22 10:56:50','2025-08-22 10:57:17'),(9,6,1,'229ef7d4-96a9-442a-8c3e-51391607fd54','https://www.paynow.co.zw/Interface/CheckPayment/?guid=b787da2d-de2b-47dd-bc5b-dfcfc3d5a03e','PAID',12.00,1,'2025-08-29 14:32:27','2025-08-29 14:32:45'),(10,6,1,'21a7f0d9-b37b-4d77-ac4c-6f6984d73990','https://www.paynow.co.zw/Interface/CheckPayment/?guid=271a6956-bfd9-4868-a3e5-b2d4bd66c05e','PAID',250.00,1,'2025-08-29 16:28:38','2025-08-29 16:28:49'),(11,8,1,'28ac0865-bf68-4223-b193-88cbbe8c8017','https://www.paynow.co.zw/Interface/CheckPayment/?guid=fe71bbf7-01d1-46ac-9440-c9581bc1f292','PAID',20.00,1,'2025-09-09 19:00:59','2025-09-09 19:01:18'),(12,9,1,'2a37d234-c820-4d52-8bcb-8fb102721370','https://www.paynow.co.zw/Interface/CheckPayment/?guid=ce247d87-d5b7-4e9f-b07f-179815414360','pending',800.00,7,'2025-09-11 08:14:59','2025-09-11 08:14:59');
/*!40000 ALTER TABLE `paynowtransactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalties`
--

DROP TABLE IF EXISTS `penalties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tire_id` bigint unsigned NOT NULL,
  `lowerlimit` int NOT NULL,
  `upperlimit` int NOT NULL,
  `penalty` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penalties_tire_id_foreign` (`tire_id`),
  CONSTRAINT `penalties_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalties`
--

LOCK TABLES `penalties` WRITE;
/*!40000 ALTER TABLE `penalties` DISABLE KEYS */;
INSERT INTO `penalties` VALUES (2,1,5,12,5.00,'2025-08-18 16:14:38','2025-08-18 16:14:38');
/*!40000 ALTER TABLE `penalties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `submodule_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (3,1,'accounttypes.access','web','2025-08-14 10:12:30','2025-08-14 10:12:30'),(4,1,'settings.access','web','2025-08-14 10:12:30','2025-08-14 10:12:30'),(5,2,'systemmodule.access','web','2025-08-15 08:04:45','2025-08-15 08:04:45'),(6,3,'roles.access','web','2025-08-15 08:08:00','2025-08-15 08:08:00'),(7,2,'systemmodule.modify','web','2025-08-15 08:09:04','2025-08-15 08:09:04'),(8,3,'role.modify','web','2025-08-15 08:10:00','2025-08-15 08:10:00'),(10,3,'role.assign','web','2025-08-15 08:11:52','2025-08-15 08:11:52'),(11,4,'users.access','web','2025-08-15 08:13:20','2025-08-15 08:13:20'),(12,4,'users.modify','web','2025-08-15 08:13:31','2025-08-15 08:13:31'),(13,1,'accounttypes.modify','web','2025-08-15 10:27:59','2025-08-15 10:27:59'),(14,4,'users.delete','web','2025-08-15 11:03:22','2025-08-15 11:03:22'),(15,4,'users.view','web','2025-08-15 11:03:30','2025-08-15 11:03:30'),(16,4,'users.assign','web','2025-08-15 11:03:50','2025-08-15 11:03:50'),(17,5,'configurations.access','web','2025-08-16 19:29:54','2025-08-16 19:29:54'),(18,5,'configurations.modify','web','2025-08-16 19:30:07','2025-08-16 19:30:07'),(19,6,'tire.access','web','2025-08-17 14:57:42','2025-08-17 14:57:42'),(21,6,'tire.modify','web','2025-08-17 14:57:53','2025-08-17 14:57:53'),(24,6,'professionmanagements.access','web','2025-08-17 15:04:46','2025-08-17 15:04:46'),(25,7,'professions.access','web','2025-08-17 15:28:27','2025-08-17 15:28:27'),(26,7,'professions.modify','web','2025-08-17 15:28:37','2025-08-17 15:28:37'),(27,8,'currency.access','web','2025-08-17 17:06:18','2025-08-17 17:06:18'),(28,8,'finance.access','web','2025-08-17 17:06:18','2025-08-17 17:06:18'),(29,8,'currency.modify','web','2025-08-17 17:06:27','2025-08-17 17:06:27'),(30,9,'exchangerate.access','web','2025-08-17 17:27:06','2025-08-17 17:27:06'),(31,9,'exchangerate.create','web','2025-08-17 17:27:19','2025-08-17 17:32:52'),(32,9,'exchangerate.update','web','2025-08-17 17:33:01','2025-08-17 17:33:01'),(33,9,'exchangerate.delete','web','2025-08-17 17:33:09','2025-08-17 17:33:09'),(34,10,'settlementsplits.access','web','2025-08-18 12:05:51','2025-08-18 12:05:51'),(35,10,'settlementsplits.modify','web','2025-08-18 12:06:04','2025-08-18 12:06:04'),(36,11,'paymentchannels.access','web','2025-08-18 13:15:45','2025-08-18 13:15:45'),(37,11,'paymentchannels.modify','web','2025-08-18 13:15:58','2025-08-18 13:15:58'),(38,12,'registrationfees.access','web','2025-08-18 14:45:50','2025-08-18 14:45:50'),(39,12,'registrationfees.modify','web','2025-08-18 14:46:03','2025-08-18 14:46:03'),(40,13,'applicationfees.access','web','2025-08-18 15:28:20','2025-08-18 15:28:20'),(41,13,'applicationfees.modify','web','2025-08-18 15:28:35','2025-08-18 15:28:35'),(42,14,'penalties.access','web','2025-08-18 15:53:26','2025-08-18 15:53:26'),(43,14,'penalties.modify','web','2025-08-18 15:53:37','2025-08-18 15:53:37'),(44,15,'discounts.access','web','2025-08-18 16:21:38','2025-08-18 16:21:38'),(45,15,'discounts.modify','web','2025-08-18 16:21:51','2025-08-18 16:21:51'),(46,16,'otherservices.access','web','2025-08-19 06:54:42','2025-08-19 06:54:42'),(47,16,'otherservices.modify','web','2025-08-19 06:54:55','2025-08-19 06:54:55'),(48,17,'banks.access','web','2025-08-19 12:38:34','2025-08-19 12:38:34'),(49,17,'banks.modify','web','2025-08-19 12:38:42','2025-08-19 12:38:42'),(50,18,'banktransactions.access','web','2025-08-19 16:01:02','2025-08-19 16:01:02'),(51,18,'banktransactions.modify','web','2025-08-19 16:01:12','2025-08-19 16:01:12'),(52,19,'customers.access','web','2025-08-19 17:00:09','2025-08-19 17:00:09'),(53,19,'customers.modify','web','2025-08-19 17:00:22','2025-08-19 17:00:22'),(54,20,'invoices.access','web','2025-08-21 19:32:02','2025-08-21 19:32:02'),(55,20,'invoices.receipt','web','2025-08-21 19:32:14','2025-08-21 19:32:14'),(56,18,'banktransactions.claim','web','2025-08-22 05:49:59','2025-08-22 05:49:59'),(57,21,'assessments.access','web','2025-08-22 09:14:13','2025-08-22 09:14:13'),(59,21,'assessments.process','web','2025-08-22 09:14:26','2025-08-22 09:14:26'),(60,21,'approval.access','web','2025-08-29 13:42:50','2025-08-29 13:42:50'),(61,22,'registrations.access','web','2025-08-31 17:29:57','2025-08-31 17:29:57'),(62,22,'registrations.approve','web','2025-08-31 17:30:09','2025-08-31 17:30:09'),(63,23,'applications.access','web','2025-08-31 18:07:26','2025-08-31 18:07:26'),(64,23,'applications.approve','web','2025-08-31 18:07:36','2025-08-31 18:07:36'),(65,24,'session.access','web','2025-09-10 13:42:57','2025-09-10 13:42:57'),(66,24,'session.modify','web','2025-09-10 13:43:08','2025-09-10 13:43:08');
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
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
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
-- Table structure for table `professionconditions`
--

DROP TABLE IF EXISTS `professionconditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professionconditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profession_id` bigint unsigned NOT NULL,
  `customertype_id` bigint unsigned NOT NULL,
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `professionconditions_profession_id_foreign` (`profession_id`),
  KEY `professionconditions_registertype_id_foreign` (`customertype_id`),
  CONSTRAINT `professionconditions_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professionconditions`
--

LOCK TABLES `professionconditions` WRITE;
/*!40000 ALTER TABLE `professionconditions` DISABLE KEYS */;
INSERT INTO `professionconditions` VALUES (1,2,3,'To work under the supervision of a registered practitioner','2025-09-10 07:26:05','2025-09-10 07:26:05'),(2,2,4,'To work under the supervision of a registered practitioner','2025-09-10 07:27:45','2025-09-10 07:27:45');
/*!40000 ALTER TABLE `professionconditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professiondocuments`
--

DROP TABLE IF EXISTS `professiondocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professiondocuments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profession_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customertype_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `professiondocuments_profession_id_foreign` (`profession_id`),
  KEY `professiondocuments_document_id_foreign` (`document_id`),
  CONSTRAINT `professiondocuments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  CONSTRAINT `professiondocuments_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professiondocuments`
--

LOCK TABLES `professiondocuments` WRITE;
/*!40000 ALTER TABLE `professiondocuments` DISABLE KEYS */;
INSERT INTO `professiondocuments` VALUES (4,2,4,'2025-08-21 12:47:08','2025-08-21 12:47:08',1);
/*!40000 ALTER TABLE `professiondocuments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professions`
--

DROP TABLE IF EXISTS `professions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tire_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required_cdp` int DEFAULT '0',
  `minimum_cdp` int DEFAULT '0',
  `prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `professions_tire_id_foreign` (`tire_id`),
  CONSTRAINT `professions_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professions`
--

LOCK TABLES `professions` WRITE;
/*!40000 ALTER TABLE `professions` DISABLE KEYS */;
INSERT INTO `professions` VALUES (2,1,'Clinical Assistants',10,5,'CA','2025-08-17 16:00:43','2025-08-17 16:00:43','active');
/*!40000 ALTER TABLE `professions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proofofpayments`
--

DROP TABLE IF EXISTS `proofofpayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proofofpayments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proofofpayments_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `proofofpayments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proofofpayments`
--

LOCK TABLES `proofofpayments` WRITE;
/*!40000 ALTER TABLE `proofofpayments` DISABLE KEYS */;
INSERT INTO `proofofpayments` VALUES (4,17,'documents/rAePftlwe4802vo64tRmsNZ5wo8ysLZOtQk9RYLi.pdf','PENDING','2025-08-21 19:14:20','2025-08-21 19:14:20'),(5,17,'documents/pQQJm0OrnuYj7fzBnC3zlRpu2A7SW89iU0hnYaXf.pdf','PENDING','2025-08-21 19:14:32','2025-08-21 19:14:32'),(6,20,'documents/v8LRMrMojozoPj73Kyo1vc5xY424rWs1XsSCAJDg.pdf','PENDING','2025-08-22 11:01:50','2025-08-22 11:01:50'),(7,20,'documents/Gtm5WdoEbZ2gExxx8HmGH38WkjAH31GJqF4qjWkV.pdf','PENDING','2025-08-22 11:02:01','2025-08-22 11:02:01'),(8,23,'documents/6veVc4uwGwhCoM5wp0ziI4SIjwX5ytspMxXqZDkY.pdf','PENDING','2025-08-29 14:33:38','2025-08-29 14:33:38'),(9,26,'documents/mLYBNsM3UhvFRA5sJQxJ4Dg9bg0AbVD70b3gxsVm.pdf','PENDING','2025-08-29 14:47:38','2025-08-29 14:47:38'),(10,29,'documents/lH0cQDyv8PsZGW5S57GBYhnTkclCL47rwOPJ6LiI.pdf','PENDING','2025-08-31 16:53:44','2025-08-31 16:53:44');
/*!40000 ALTER TABLE `proofofpayments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provinces`
--

DROP TABLE IF EXISTS `provinces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provinces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provinces`
--

LOCK TABLES `provinces` WRITE;
/*!40000 ALTER TABLE `provinces` DISABLE KEYS */;
INSERT INTO `provinces` VALUES (1,'Midlands','2025-08-17 14:31:17','2025-08-17 14:31:17');
/*!40000 ALTER TABLE `provinces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualificationcategories`
--

DROP TABLE IF EXISTS `qualificationcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qualificationcategories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requireapproval` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualificationcategories`
--

LOCK TABLES `qualificationcategories` WRITE;
/*!40000 ALTER TABLE `qualificationcategories` DISABLE KEYS */;
INSERT INTO `qualificationcategories` VALUES (1,'Foreign','Y','2025-08-17 08:49:05','2025-08-17 08:49:05'),(2,'Local','N','2025-08-17 08:49:12','2025-08-17 08:49:12');
/*!40000 ALTER TABLE `qualificationcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualificationlevels`
--

DROP TABLE IF EXISTS `qualificationlevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qualificationlevels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualificationlevels`
--

LOCK TABLES `qualificationlevels` WRITE;
/*!40000 ALTER TABLE `qualificationlevels` DISABLE KEYS */;
INSERT INTO `qualificationlevels` VALUES (1,'PHD','2025-08-17 07:48:01','2025-08-17 07:48:01'),(2,'Masters','2025-08-17 07:48:06','2025-08-17 07:48:06'),(3,'Degree','2025-08-17 07:48:10','2025-08-17 07:48:10'),(4,'Higher Diploma','2025-08-17 07:48:21','2025-08-17 07:48:21'),(5,'Diploma','2025-08-17 07:48:28','2025-08-17 07:48:28'),(6,'Certificate','2025-08-17 07:48:33','2025-08-17 07:48:33');
/*!40000 ALTER TABLE `qualificationlevels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `suspense_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `exchangerate_id` bigint unsigned NOT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `createdby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receipts_customer_id_foreign` (`customer_id`),
  KEY `receipts_currency_id_foreign` (`currency_id`),
  KEY `receipts_suspense_id_foreign` (`suspense_id`),
  KEY `receipts_invoice_id_foreign` (`invoice_id`),
  KEY `receipts_exchangerate_id_foreign` (`exchangerate_id`),
  KEY `receipts_createdby_foreign` (`createdby`),
  CONSTRAINT `receipts_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `receipts_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `receipts_exchangerate_id_foreign` FOREIGN KEY (`exchangerate_id`) REFERENCES `exchangerates` (`id`),
  CONSTRAINT `receipts_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `receipts_suspense_id_foreign` FOREIGN KEY (`suspense_id`) REFERENCES `suspenses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipts`
--

LOCK TABLES `receipts` WRITE;
/*!40000 ALTER TABLE `receipts` DISABLE KEYS */;
INSERT INTO `receipts` VALUES (1,2,1,8,17,4,'REC-2025-8930-17',12.00,1,'2025-08-22 09:06:05','2025-08-22 09:06:05'),(2,3,1,9,20,4,'REC-2025-8077-20',12.00,1,'2025-08-22 11:04:45','2025-08-22 11:04:45'),(3,3,1,10,20,4,'REC-2025-8077-20',12.00,1,'2025-08-22 11:04:45','2025-08-22 11:04:45'),(4,6,1,11,23,4,'REC-2025-5869-23',12.00,1,'2025-08-29 14:34:58','2025-08-29 14:34:58'),(5,6,1,12,26,4,'REC-2025-4153-26',12.00,1,'2025-08-29 14:48:18','2025-08-29 14:48:18'),(6,6,1,12,26,4,'REC-2025-9371-26',12.00,1,'2025-08-29 15:17:04','2025-08-29 15:17:04'),(7,6,1,12,26,4,'REC-2025-5323-26',12.00,1,'2025-08-29 15:20:24','2025-08-29 15:20:24'),(8,6,1,12,26,4,'REC-2025-1370-26',12.00,1,'2025-08-29 15:26:36','2025-08-29 15:26:36'),(9,6,1,12,24,4,'REC-2025-8767-24',12.00,1,'2025-08-29 15:40:13','2025-08-29 15:40:13'),(10,6,1,12,24,4,'REC-2025-1342-24',12.00,1,'2025-08-29 15:44:59','2025-08-29 15:44:59'),(13,7,1,14,29,4,'REC-2025-2596-29',12.00,1,'2025-08-31 17:15:23','2025-08-31 17:15:23'),(14,7,1,14,27,4,'REC-2025-1287-27',12.00,1,'2025-08-31 17:20:35','2025-08-31 17:20:35'),(15,7,1,14,28,4,'REC-2025-7723-28',250.00,1,'2025-08-31 18:00:32','2025-08-31 18:00:32'),(16,8,1,15,31,4,'REC-2025-7216-31',10.00,1,'2025-09-09 19:01:39','2025-09-09 19:01:39');
/*!40000 ALTER TABLE `receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registertypes`
--

DROP TABLE IF EXISTS `registertypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registertypes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registertypes`
--

LOCK TABLES `registertypes` WRITE;
/*!40000 ALTER TABLE `registertypes` DISABLE KEYS */;
INSERT INTO `registertypes` VALUES (1,'Main','2025-08-17 07:50:25','2025-08-17 07:50:25'),(2,'Intern','2025-08-17 07:50:32','2025-08-17 07:50:32'),(3,'Student','2025-08-17 07:50:38','2025-08-17 07:50:38');
/*!40000 ALTER TABLE `registertypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrationfees`
--

DROP TABLE IF EXISTS `registrationfees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrationfees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` int DEFAULT NULL,
  `customertype_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `employmentlocation_id` bigint unsigned NOT NULL,
  `generalledger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registrationfees_customertype_id_foreign` (`customertype_id`),
  KEY `registrationfees_currency_id_foreign` (`currency_id`),
  KEY `registrationfees_qualificationcategory_id_foreign` (`employmentlocation_id`),
  CONSTRAINT `registrationfees_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `registrationfees_customertype_id_foreign` FOREIGN KEY (`customertype_id`) REFERENCES `customertypes` (`id`),
  CONSTRAINT `registrationfees_qualificationcategory_id_foreign` FOREIGN KEY (`employmentlocation_id`) REFERENCES `qualificationcategories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrationfees`
--

LOCK TABLES `registrationfees` WRITE;
/*!40000 ALTER TABLE `registrationfees` DISABLE KEYS */;
INSERT INTO `registrationfees` VALUES (1,2025,1,1,1,'2342',12.00,'2025-08-18 15:03:50','2025-08-18 15:03:50'),(2,2025,1,1,2,'2342',12.00,'2025-08-18 15:04:18','2025-08-18 15:04:18'),(3,2025,3,1,1,'2342',12.00,'2025-08-18 15:05:12','2025-08-18 15:05:12'),(5,2025,3,1,2,'2342',10.00,'2025-08-18 15:07:08','2025-08-18 15:07:08');
/*!40000 ALTER TABLE `registrationfees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrationnumbers`
--

DROP TABLE IF EXISTS `registrationnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrationnumbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` int NOT NULL,
  `year` int NOT NULL DEFAULT '2025',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrationnumbers`
--

LOCK TABLES `registrationnumbers` WRITE;
/*!40000 ALTER TABLE `registrationnumbers` DISABLE KEYS */;
INSERT INTO `registrationnumbers` VALUES (1,10,2025,'2025-08-20 16:58:32','2025-09-11 07:38:14');
/*!40000 ALTER TABLE `registrationnumbers` ENABLE KEYS */;
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
INSERT INTO `role_has_permissions` VALUES (3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(21,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1);
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
  `accounttype_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `roles` VALUES (1,1,'SuperAdmin','web','2025-08-14 09:48:34','2025-08-14 09:48:34'),(2,2,'Practitioner','web','2025-08-14 17:36:55','2025-09-10 17:17:49'),(3,3,'Student','web','2025-08-14 17:37:29','2025-08-14 17:37:29'),(4,1,'Registrar','web','2025-08-15 12:13:08','2025-08-15 12:13:08');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('Jsw5tP6aYJBjTXii98mgcnEZ7Sqqbjhs1OA5GofC',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibjJxRnpmWTZrVU5kUlNQalhndHVsOVhiZksxdXV0VDdON3FNZlRZWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjQ6Im1hcnkiO2E6MTp7czo1OiJ0b2FzdCI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1757587716);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settlementsplits`
--

DROP TABLE IF EXISTS `settlementsplits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settlementsplits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employmentlocation_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `user_id` int DEFAULT NULL,
  `percentage` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settlementsplits_qualificationcategory_id_foreign` (`employmentlocation_id`),
  KEY `settlementsplits_currency_id_foreign` (`currency_id`),
  CONSTRAINT `settlementsplits_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlementsplits`
--

LOCK TABLES `settlementsplits` WRITE;
/*!40000 ALTER TABLE `settlementsplits` DISABLE KEYS */;
INSERT INTO `settlementsplits` VALUES (5,'REGISTRATION',2,1,NULL,100.00,'2025-08-19 16:55:38','2025-08-19 16:55:38'),(6,'REGISTRATION',1,1,NULL,100.00,'2025-08-19 16:56:00','2025-08-19 16:56:00'),(7,'NEWAPPLICATION',1,1,NULL,100.00,'2025-08-19 16:56:29','2025-08-19 16:56:29'),(8,'NEWAPPLICATION',2,1,NULL,100.00,'2025-08-19 16:56:49','2025-08-19 16:56:49'),(9,'RENEWAL',1,1,NULL,100.00,'2025-08-19 16:57:03','2025-08-19 16:57:03'),(10,'RENEWAL',2,1,NULL,70.00,'2025-08-19 16:57:17','2025-08-19 16:57:17');
/*!40000 ALTER TABLE `settlementsplits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentplacements`
--

DROP TABLE IF EXISTS `studentplacements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentplacements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` bigint unsigned NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startdate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enddate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisorname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisorphone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisoremail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisorposition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_supervisor_registered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `regnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentplacements`
--

LOCK TABLES `studentplacements` WRITE;
/*!40000 ALTER TABLE `studentplacements` DISABLE KEYS */;
INSERT INTO `studentplacements` VALUES (1,33,'Anixsys pvt ltd','elearning  analyst','2025-05-01','2025-09-09','Tawana ','2134214132','benson.misi@gmail.com','director','NO',NULL,NULL,'2025-09-09 18:12:51','2025-09-09 18:15:30'),(2,34,'PR2019192','ICT OFFICER  SOFTWARE DEVELOPMENT & INTERGRATION','2025-09-11',NULL,'Tawana ','0773454949','benson.misi@gmail.com','director','NO',NULL,NULL,'2025-09-11 08:36:39','2025-09-11 08:36:39');
/*!40000 ALTER TABLE `studentplacements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentqualificationdocuments`
--

DROP TABLE IF EXISTS `studentqualificationdocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentqualificationdocuments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `studentqualification_id` int NOT NULL,
  `document_id` int NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentqualificationdocuments`
--

LOCK TABLES `studentqualificationdocuments` WRITE;
/*!40000 ALTER TABLE `studentqualificationdocuments` DISABLE KEYS */;
/*!40000 ALTER TABLE `studentqualificationdocuments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentqualifications`
--

DROP TABLE IF EXISTS `studentqualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentqualifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customerprofession_id` int NOT NULL,
  `qualificationlevel_id` bigint unsigned NOT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startyear` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endyear` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentqualifications`
--

LOCK TABLES `studentqualifications` WRITE;
/*!40000 ALTER TABLE `studentqualifications` DISABLE KEYS */;
INSERT INTO `studentqualifications` VALUES (2,33,6,'Emerate Ambulance','Ambulance technician','2022',NULL,NULL,'2025-09-09 17:24:06','2025-09-09 17:24:06'),(3,34,5,'Emerate Ambulance','Ambulance technician','2021',NULL,NULL,'2025-09-11 08:34:49','2025-09-11 08:34:49');
/*!40000 ALTER TABLE `studentqualifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentregistrationfees`
--

DROP TABLE IF EXISTS `studentregistrationfees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentregistrationfees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `qualificationcategory_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentregistrationfees`
--

LOCK TABLES `studentregistrationfees` WRITE;
/*!40000 ALTER TABLE `studentregistrationfees` DISABLE KEYS */;
/*!40000 ALTER TABLE `studentregistrationfees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submodules`
--

DROP TABLE IF EXISTS `submodules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submodules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `systemmodule_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submodules_systemmodule_id_foreign` (`systemmodule_id`),
  CONSTRAINT `submodules_systemmodule_id_foreign` FOREIGN KEY (`systemmodule_id`) REFERENCES `systemmodules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submodules`
--

LOCK TABLES `submodules` WRITE;
/*!40000 ALTER TABLE `submodules` DISABLE KEYS */;
INSERT INTO `submodules` VALUES (1,1,'Account type','o-cog','accounttypes.access','accounttypes.index','2025-08-14 10:08:12','2025-08-15 07:43:43'),(2,1,'System modules','o-link','systemmodule.access','systemmodules.index','2025-08-15 07:37:14','2025-08-15 07:37:14'),(3,1,'Roles','o-shield-check','roles.access','roles.index','2025-08-15 07:40:54','2025-08-15 07:40:54'),(4,1,'Users','o-users','users.access','users.index','2025-08-15 08:13:14','2025-08-15 08:13:14'),(5,1,'Configurations','o-cog-6-tooth','configurations.access','configurations.index','2025-08-16 19:29:45','2025-08-16 19:29:45'),(6,4,'Profession Tires','o-cog','tire.access','tires.index','2025-08-17 14:57:24','2025-08-17 14:57:24'),(7,4,'Professions','o-academic-cap','professions.access','professions.index','2025-08-17 15:28:22','2025-08-17 15:28:22'),(8,5,'Currencies','o-document-currency-dollar','currency.access','currencies.index','2025-08-17 17:06:16','2025-08-17 17:06:16'),(9,5,'Exchange rates','o-banknotes','exchangerate.access','exchangerates.index','2025-08-17 17:27:02','2025-08-17 17:27:02'),(10,5,'Settlement splits','o-percent-badge','settlementsplits.access','settlementsplits.index','2025-08-18 12:05:43','2025-08-18 12:05:43'),(11,5,'Payment channels','o-credit-card','paymentchannels.access','paymentchannels.index','2025-08-18 13:15:41','2025-08-18 13:15:41'),(12,5,'Registration fees','o-currency-dollar','registrationfees.access','registrationfees.index','2025-08-18 14:45:46','2025-08-18 14:45:46'),(13,5,'Application fees','o-currency-dollar','applicationfees.access','applicationfees.index','2025-08-18 15:28:16','2025-08-18 15:28:16'),(14,5,'Penalties','o-banknotes','penalties.access','penalities.index','2025-08-18 15:53:21','2025-08-18 15:53:21'),(15,5,'Discounts','o-percent-badge','discounts.access','discounts.index','2025-08-18 16:21:33','2025-08-18 16:21:33'),(16,5,'Other service','o-document-currency-dollar','otherservices.access','otherservices.index','2025-08-19 06:54:28','2025-08-19 06:54:28'),(17,5,'Banks','o-banknotes','banks.access','banks.index','2025-08-19 12:38:27','2025-08-19 12:38:27'),(18,5,'Bank transactions','o-building-library','banktransactions.access','banktransactions.index','2025-08-19 16:00:55','2025-08-19 16:00:55'),(19,4,'Customers','o-user-group','customers.access','customers.index','2025-08-19 17:00:04','2025-08-19 17:00:04'),(20,5,'Invoices','o-banknotes','invoices.access','invoices.index','2025-08-21 19:31:55','2025-08-21 19:31:55'),(21,6,'Assesments','o-book-open','assessments.access','assessments.index','2025-08-22 09:14:11','2025-08-22 09:14:11'),(22,6,'Registrations','o-academic-cap','registrations.access','registrationapprovals.index','2025-08-31 17:29:53','2025-08-31 17:29:53'),(23,6,'Applications','o-academic-cap','applications.access','applicationapprovals.index','2025-08-31 18:07:21','2025-08-31 18:07:21'),(24,4,'Sessions','o-clock','session.access','applicationsessions.index','2025-09-10 13:42:51','2025-09-10 13:42:51');
/*!40000 ALTER TABLE `submodules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suspenses`
--

DROP TABLE IF EXISTS `suspenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suspenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `createdby` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suspenses_customer_id_foreign` (`customer_id`),
  KEY `suspenses_currency_id_foreign` (`currency_id`),
  KEY `suspenses_createdby_foreign` (`createdby`),
  CONSTRAINT `suspenses_createdby_foreign` FOREIGN KEY (`createdby`) REFERENCES `users` (`id`),
  CONSTRAINT `suspenses_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `suspenses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suspenses`
--

LOCK TABLES `suspenses` WRITE;
/*!40000 ALTER TABLE `suspenses` DISABLE KEYS */;
INSERT INTO `suspenses` VALUES (8,2,1,'109d3224-24b9-45b8-b4a3-9a87988dfb7b','bank',2,150.00,'PENDING',1,'2025-08-22 07:13:08','2025-08-22 07:13:08',NULL),(9,3,1,'d2c3c4f9-a8d8-41d9-8666-11aa101e903a','PAYNOW',8,200.00,'PENDING',1,'2025-08-22 10:57:17','2025-08-22 10:57:17',NULL),(10,3,1,'d2bf2c14-0f96-49e5-8213-453a3d95d657','bank',3,200.00,'PENDING',1,'2025-08-22 11:04:14','2025-08-22 11:04:14',NULL),(11,6,1,'a0c519aa-59fe-4998-aa99-d4fcd239ec94','PAYNOW',9,12.00,'UTILIZED',1,'2025-08-29 14:32:45','2025-08-29 14:34:58',NULL),(12,6,1,'87b1547f-7411-4f32-9773-eeb4fbf4a20c','bank',4,150.00,'PENDING',1,'2025-08-29 14:48:09','2025-08-29 16:22:30',NULL),(14,7,1,'91450275-4aca-4ba1-8123-58ae7d819155','bank',5,450.00,'PENDING',1,'2025-08-31 16:57:13','2025-08-31 16:57:13',NULL),(15,8,1,'9cf908b8-0d19-46ff-9e69-32f83d065087','PAYNOW',11,20.00,'PENDING',1,'2025-09-09 19:01:18','2025-09-09 19:01:18',NULL);
/*!40000 ALTER TABLE `suspenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemmodules`
--

DROP TABLE IF EXISTS `systemmodules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `systemmodules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accounttype_id` bigint unsigned NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `systemmodules_accounttype_id_foreign` (`accounttype_id`),
  CONSTRAINT `systemmodules_accounttype_id_foreign` FOREIGN KEY (`accounttype_id`) REFERENCES `accounttypes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemmodules`
--

LOCK TABLES `systemmodules` WRITE;
/*!40000 ALTER TABLE `systemmodules` DISABLE KEYS */;
INSERT INTO `systemmodules` VALUES (1,'Settings',1,'o-cog','settings.access','2025-08-14 09:58:27','2025-08-14 09:58:27'),(4,'Management',1,'o-bookmark-square','professionmanagements.access','2025-08-17 14:56:03','2025-08-17 14:59:29'),(5,'Finance',1,'o-currency-dollar','finance.access','2025-08-17 17:04:51','2025-08-17 17:04:51'),(6,'Approvals',1,'o-check','approval.access','2025-08-22 09:11:45','2025-08-22 09:11:45');
/*!40000 ALTER TABLE `systemmodules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tires`
--

DROP TABLE IF EXISTS `tires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tires` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tires`
--

LOCK TABLES `tires` WRITE;
/*!40000 ALTER TABLE `tires` DISABLE KEYS */;
INSERT INTO `tires` VALUES (1,'Tire 1','2025-08-17 15:08:25','2025-08-17 15:09:23'),(2,'Tire 2','2025-08-17 15:08:30','2025-08-17 15:08:30'),(3,'Specialist','2025-08-17 15:08:36','2025-08-17 15:08:36');
/*!40000 ALTER TABLE `tires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` text COLLATE utf8mb4_unicode_ci,
  `accounttype_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'3636041b-5614-4fff-aae6-14435606e63c',1,'admin','admin','123456789','admin@admin.com','2025-08-13 20:32:17','$2y$12$3dUXsARW9Z/sHgT0YT0HA.GkaFt2jZOQo/ixAY3zv8Kk9SO.NUQmK','tL8puDApaiNnOt2VdvLNVMSrxBhyfC6XtBZ8eoAw7dPq3qkW4NXaB9UBuayV','2025-08-13 20:32:18','2025-08-15 11:32:13',NULL,'active'),(3,'dde4e8e7-7f1a-4591-803c-9936556b15bc',1,'Test','Test','0773454949','test@test.co.zw',NULL,'$2y$12$G4OFds/.uYQKKyxU/jYV5OzL/avUNHRi112jk6D4ktggThF6slwhy',NULL,'2025-08-15 13:13:13','2025-08-15 13:13:13',NULL,'active'),(4,'e3d780e5-c1be-4a29-b1b4-7f06099263cc',2,'John','Doe','0773454949','benson.misi@outlook.com',NULL,'$2y$12$ABDC14nyzORtkO/pArVqWOONTGF02nG5ClYRbvG3Gn7xrCEraIt7S',NULL,'2025-08-29 14:29:01','2025-08-29 14:29:01',NULL,'active'),(5,'62549a70-0b61-4f72-8838-ba85486ebea6',2,'Cloe ','Sibanda','0775474661','cleo.sibanda@gmail.com',NULL,'$2y$12$S6NHsOnWeYJF.WG7ipJAce3eZUv2PrbJH2Tx8HJIMyTY20rXzQSQi',NULL,'2025-08-31 16:00:24','2025-08-31 16:00:24',NULL,'active'),(6,'4ea4fdc6-f58c-4182-b78d-90b3fff945c5',2,'Tanisha','Misi','0773454949','tanisha.misi@gmail.com',NULL,'$2y$12$e7gM0xVWbUxh2lZSjCnz3OzCY9MZHpctyx7CWM.841Le2WVsyCaO2',NULL,'2025-09-08 16:17:23','2025-09-08 16:17:23',NULL,'active'),(7,'a50d6d36-86e7-46ad-b7a9-c70c9a23cc80',3,'Tendai','Towo','0773454949','tendai.towo@gmail.com',NULL,'$2y$12$opvY7dAgp2aHU0OSsFx22eSN7UHLELQztdl9fD8NijB8ML7D38PIK','ditzooNcY7xfJpXmXC9FMGpqlnrWvOHcolAvuPNeoUy8L2iEQOWfYINDKt9G','2025-09-10 17:24:29','2025-09-10 17:24:29',NULL,'active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'practitioner_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-11 13:02:24

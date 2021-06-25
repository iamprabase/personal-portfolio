/*
 Navicat Premium Data Transfer

 Source Server         : deltasalesapp_live
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : deltasalesapp.com:3306
 Source Scheme         : deltasal_salesapp

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 28/05/2019 06:15:12
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for announce_employee
-- ----------------------------
DROP TABLE IF EXISTS `announce_employee`;
CREATE TABLE `announce_employee`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 281 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for announcements
-- ----------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(2) NULL DEFAULT 1,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for attendances
-- ----------------------------
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `check_datetime` datetime(0) NOT NULL,
  `check_type` tinyint(1) NOT NULL COMMENT '1:checkin,2:checkout',
  `auto_checkout` tinyint(1) NULL DEFAULT NULL COMMENT '1:auto_checkout',
  `adate` date NULL DEFAULT NULL,
  `atime` time(0) NULL DEFAULT NULL,
  `unix_timestamp` bigint(20) NULL DEFAULT NULL,
  `latitude` double(20, 6) NULL DEFAULT NULL,
  `longitude` double(20, 6) NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remark` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `device` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_id`(`unique_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4358 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for attendances01-16-2018
-- ----------------------------
DROP TABLE IF EXISTS `attendances01-16-2018`;
CREATE TABLE `attendances01-16-2018`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `check_datetime` datetime(0) NOT NULL,
  `check_type` tinyint(1) NOT NULL COMMENT '1:checkin,2:checkout',
  `auto_checkout` tinyint(1) NULL DEFAULT NULL COMMENT '1:auto_checkout',
  `adate` date NULL DEFAULT NULL,
  `atime` time(0) NULL DEFAULT NULL,
  `latitude` double(20, 6) NULL DEFAULT NULL,
  `longitude` double(20, 6) NULL DEFAULT NULL,
  `remark` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `device` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_id`(`unique_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1147 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for banks
-- ----------------------------
DROP TABLE IF EXISTS `banks`;
CREATE TABLE `banks`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Active',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) NULL DEFAULT NULL,
  `desc` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 113 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cities
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cities_id_index`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 48332 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for client_settings
-- ----------------------------
DROP TABLE IF EXISTS `client_settings`;
CREATE TABLE `client_settings`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NULL DEFAULT NULL,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `phonecode` int(11) NULL DEFAULT 977,
  `phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ext_no` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `mobile` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `logo_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `small_logo` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `small_logo_path` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `favicon` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `favicon_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `default_currency` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `currency_format` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `currency_symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  `time_zone` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_format` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_type` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `order_prefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `order_no_start` int(11) NOT NULL DEFAULT 0,
  `invoice_prefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_host` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_username` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_password` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_port` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_mail_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `recovery_mail_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `other_mails_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_logo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_text` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `login_logo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_logo_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `copyright_text` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `location` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `city` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `state` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `country` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `zip` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `address_1` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `address_2` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `opening_time` time(0) NULL DEFAULT NULL,
  `closing_time` time(0) NULL DEFAULT NULL,
  `order_with_amt` tinyint(1) NOT NULL DEFAULT 0,
  `order_approval` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 57 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for clients
-- ----------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `client_type` tinyint(1) NOT NULL DEFAULT 0,
  `superior` int(11) NULL DEFAULT NULL,
  `client_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phonecode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mobile` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fax` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `website` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `address_1` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `address_2` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `city` int(11) NULL DEFAULT NULL,
  `state` int(11) NULL DEFAULT NULL,
  `country` int(11) NULL DEFAULT NULL,
  `pin` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `about` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `employee_ids` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `opening_balance` decimal(10, 2) NULL DEFAULT 0.00,
  `status` enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `market_area` int(11) NULL DEFAULT NULL,
  `created_by` int(11) NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 425 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for collections
-- ----------------------------
DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `employee_type` enum('Employee','Admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Employee',
  `payment_received` decimal(12, 2) NULL DEFAULT NULL,
  `due_payment` decimal(12, 2) NULL DEFAULT NULL,
  `payment_method` enum('Cash','Cheque','Bank Transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `payment_note` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('Close','Cancel','Open','Pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `payment_date` date NULL DEFAULT NULL,
  `next_date` datetime(0) NULL DEFAULT NULL,
  `bank_id` int(11) NULL DEFAULT NULL,
  `cheque_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `cheque_date` date NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 305 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for companies
-- ----------------------------
DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contact_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `country_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contact_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `whitelabel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `customize` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `start_date` timestamp(0) NULL DEFAULT NULL,
  `end_date` timestamp(0) NULL DEFAULT NULL,
  `extNo` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `city` int(11) NULL DEFAULT NULL,
  `state` int(11) NULL DEFAULT NULL,
  `country` int(11) NULL DEFAULT NULL,
  `aboutCompany` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `account_created` timestamp(0) NULL DEFAULT NULL,
  `contact_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_active` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `is_verified` tinyint(1) NULL DEFAULT 0,
  `verify_token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `companies_domain_unique`(`domain`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 60 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for company_plan
-- ----------------------------
DROP TABLE IF EXISTS `company_plan`;
CREATE TABLE `company_plan`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 94 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for contactus
-- ----------------------------
DROP TABLE IF EXISTS `contactus`;
CREATE TABLE `contactus`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `subject` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 70 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for countries
-- ----------------------------
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phonecode` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `countries_id_index`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 247 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for currency
-- ----------------------------
DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `currency` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `code` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `symbol` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 264 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `billing_address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `delivery_address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contact_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contact_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for employeegroups
-- ----------------------------
DROP TABLE IF EXISTS `employeegroups`;
CREATE TABLE `employeegroups`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('Active','Inactive','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Active',
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 109 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firebase_token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `employee_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `employeegroup` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `country_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `b_date` date NULL DEFAULT NULL,
  `gender` enum('Male','Female','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Male',
  `status` enum('Active','Inactive','Archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `client_ids` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `designation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Employee',
  `local_add` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `per_add` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `recent_location` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employees_employee_code_unique`(`employee_code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 294 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for expenses
-- ----------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `amount` decimal(8, 2) NULL DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `expense_photo` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `expense_photo_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `approved_by` int(11) NULL DEFAULT NULL,
  `remark` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `status` enum('Approved','Cancelled','Pending','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `employee_type` enum('Admin','Employee','','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 211 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for followup_comments
-- ----------------------------
DROP TABLE IF EXISTS `followup_comments`;
CREATE TABLE `followup_comments`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `followup_id` int(11) NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for followups
-- ----------------------------
DROP TABLE IF EXISTS `followups`;
CREATE TABLE `followups`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` int(11) NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Hot','Warm','Cold','Closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `next_followup` datetime(0) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for handles
-- ----------------------------
DROP TABLE IF EXISTS `handles`;
CREATE TABLE `handles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `map_type` enum('1','2') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `company_id`(`company_id`, `client_id`, `employee_id`, `map_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1143 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for images
-- ----------------------------
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `type_id` int(11) NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `image` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `image_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `delated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 615 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for leads
-- ----------------------------
DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NULL DEFAULT NULL,
  `company` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `source` int(11) NOT NULL,
  `status` enum('Hot','Cold','Warm','') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` int(11) NULL DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `country_id` int(11) NULL DEFAULT 0,
  `zip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `website` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `assigned_id` int(11) NULL DEFAULT NULL,
  `created` date NULL DEFAULT NULL,
  `lastcontact` datetime(0) NULL DEFAULT NULL,
  `dateassigned` date NULL DEFAULT NULL,
  `dateconverted` datetime(0) NULL DEFAULT NULL,
  `lost` tinyint(1) NULL DEFAULT 0,
  `junk` int(11) NULL DEFAULT 0,
  `public` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `delated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for leadssources
-- ----------------------------
DROP TABLE IF EXISTS `leadssources`;
CREATE TABLE `leadssources`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for leave_type
-- ----------------------------
DROP TABLE IF EXISTS `leave_type`;
CREATE TABLE `leave_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for leaves
-- ----------------------------
DROP TABLE IF EXISTS `leaves`;
CREATE TABLE `leaves`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `leave_title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `leavetype` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  `leave_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `remarks` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('Approved','Rejected','Pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `approved_by` int(11) NULL DEFAULT NULL,
  `status_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `leave_status_change` datetime(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 93 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for locations
-- ----------------------------
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `company_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `raw_latitude` double(11, 8) NOT NULL,
  `latitude` double(10, 8) NOT NULL,
  `raw_longitude` double(11, 8) NOT NULL,
  `longitude` double(11, 8) NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `distance_from_last_gps` double(11, 8) NULL DEFAULT NULL,
  `altitude` int(10) NULL DEFAULT NULL,
  `unix_timestamp` bigint(20) NULL DEFAULT NULL,
  `accuracy` double(10, 3) NULL DEFAULT NULL,
  `speed` double(10, 3) NULL DEFAULT NULL,
  `speed_accuracy` double(10, 3) NULL DEFAULT NULL,
  `battery_level` double(10, 0) NULL DEFAULT NULL,
  `activity` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `still` tinyint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'still:1;not still:0;',
  `datetime` timestamp(0) NULL DEFAULT NULL,
  `date` date NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  `sync_flag` tinyint(1) NULL DEFAULT 1 COMMENT '0:not synced,1:synced',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `company_id`(`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 468911 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for managers
-- ----------------------------
DROP TABLE IF EXISTS `managers`;
CREATE TABLE `managers`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `contact_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contact_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_owner` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 57 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for marketareas
-- ----------------------------
DROP TABLE IF EXISTS `marketareas`;
CREATE TABLE `marketareas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NULL DEFAULT NULL,
  `name` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for meetings
-- ----------------------------
DROP TABLE IF EXISTS `meetings`;
CREATE TABLE `meetings`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `company_id` int(11) NULL DEFAULT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `employee_id` int(11) NULL DEFAULT NULL,
  `checkintime` time(0) NULL DEFAULT NULL,
  `meetingdate` date NULL DEFAULT NULL,
  `remark` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `latitude` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `comm_medium` enum('Audio Call','Video Call','Meeting','Chat','Messages','Conference','Others') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 154 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT 'null:for all employee',
  `employee_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'json encoded data employee ids',
  `read_by` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'json encoded data employee ids',
  `data_type` enum('leave','collection','order','announcement','expense','meeting','remark','task') CHARACTER SET gbk COLLATE gbk_chinese_ci NULL DEFAULT NULL,
  `action_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT 'id of data_type',
  `to` tinyint(1) NOT NULL COMMENT '0:To Admin,1:To Employee',
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'json encoded data payload',
  `status` tinyint(2) NULL DEFAULT 1 COMMENT '1:Unread,2:Read,3:Archived',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2059 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for notifications_copy1
-- ----------------------------
DROP TABLE IF EXISTS `notifications_copy1`;
CREATE TABLE `notifications_copy1`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT 'null:for all employee',
  `data_type` enum('leave','collection','order','announcement','expense','meeting','remark','task') CHARACTER SET gbk COLLATE gbk_chinese_ci NULL DEFAULT NULL,
  `to` tinyint(1) NOT NULL COMMENT '0:To Admin,1:To Employee',
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'json encoded data payload',
  `status` tinyint(2) NULL DEFAULT 1 COMMENT '1:Unread,2:Read,3:Archived',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1100 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for orderproducts
-- ----------------------------
DROP TABLE IF EXISTS `orderproducts`;
CREATE TABLE `orderproducts`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT NULL,
  `product_id` int(11) NULL DEFAULT NULL,
  `product_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mrp` decimal(10, 2) NULL DEFAULT NULL,
  `unit` int(10) UNSIGNED NULL DEFAULT NULL,
  `unit_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `unit_symbol` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `rate` decimal(12, 2) NULL DEFAULT NULL,
  `quantity` int(11) NULL DEFAULT NULL,
  `amount` decimal(12, 2) NULL DEFAULT NULL,
  `pdiscount` int(11) NULL DEFAULT NULL,
  `pdiscount_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ptotal_amt` decimal(12, 2) NULL DEFAULT NULL,
  `short_desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 504 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `order_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '1',
  `tot_amount` decimal(12, 2) NULL DEFAULT NULL,
  `tax` decimal(12, 2) NULL DEFAULT NULL,
  `discount` int(11) NULL DEFAULT NULL,
  `discount_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `grand_total` decimal(12, 2) NULL DEFAULT NULL,
  `payment_received` decimal(12, 2) NULL DEFAULT NULL,
  `due_payment` decimal(12, 2) NULL DEFAULT NULL,
  `payment_method` enum('Cash','Cheque','Bank Transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `order_note` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `order_date` date NULL DEFAULT NULL,
  `status` enum('Close','Cancel','Open','Pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `sales_person` int(11) NULL DEFAULT NULL,
  `cancel_note` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delivery_status` enum('New','In Process','Complete','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delivery_person` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `delivery_date` datetime(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 326 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for partytype
-- ----------------------------
DROP TABLE IF EXISTS `partytype`;
CREATE TABLE `partytype`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `allow_salesman` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plans
-- ----------------------------
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `users` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_in` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('Active','Inactive','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Active',
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for product_image
-- ----------------------------
DROP TABLE IF EXISTS `product_image`;
CREATE TABLE `product_image`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `product_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `category_id` int(11) NULL DEFAULT NULL,
  `sub_cat_id` int(11) NULL DEFAULT NULL,
  `brand` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mrp` decimal(10, 2) NULL DEFAULT NULL,
  `unit` int(11) NOT NULL DEFAULT 5,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `s_d_price` decimal(8, 2) NULL DEFAULT NULL,
  `d_price` decimal(8, 2) NULL DEFAULT NULL,
  `r_price` decimal(8, 2) NULL DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `short_desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `discount1` int(11) NULL DEFAULT NULL,
  `quantity1` int(11) NULL DEFAULT NULL,
  `discount2` int(11) NULL DEFAULT NULL,
  `quantity2` int(11) NULL DEFAULT NULL,
  `discount3` int(11) NULL DEFAULT NULL,
  `quantity3` int(11) NULL DEFAULT NULL,
  `status` enum('Active','Inactive','Out Of Stock','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Active',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 974 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for request_quote
-- ----------------------------
DROP TABLE IF EXISTS `request_quote`;
CREATE TABLE `request_quote`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `skype` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `companyName` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 45 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ext_no` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `mobile` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `logo_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `small_logo` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `small_logo_path` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `favicon` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `favicon_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `default_currency` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `currency_format` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `currency_symbol` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `time_zone` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_format` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_type` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `order_prefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_prefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_host` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_username` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_password` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `smtp_port` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_mail_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `recovery_mail_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `other_mails_from` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_logo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_text` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `login_logo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_logo_path` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `copyright_text` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `location` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `city` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `state` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `country` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `zip` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `updated_at` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for states
-- ----------------------------
DROP TABLE IF EXISTS `states`;
CREATE TABLE `states`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `states_id_index`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4122 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for task_comment
-- ----------------------------
DROP TABLE IF EXISTS `task_comment`;
CREATE TABLE `task_comment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `comments` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_by` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `client_id` int(11) NULL DEFAULT NULL,
  `assigned_from` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `assigned_from_type` enum('Admin','Employee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('High','Medium','Low','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  `due_date` date NOT NULL,
  `status` enum('Completed','In Progress','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 92 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tax_on_orders
-- ----------------------------
DROP TABLE IF EXISTS `tax_on_orders`;
CREATE TABLE `tax_on_orders`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT NULL,
  `tax_name` varchar(191) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tax_percent` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 509 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tax_types
-- ----------------------------
DROP TABLE IF EXISTS `tax_types`;
CREATE TABLE `tax_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NULL DEFAULT NULL,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `percent` decimal(4, 2) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for unit_types
-- ----------------------------
DROP TABLE IF EXISTS `unit_types`;
CREATE TABLE `unit_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `symbol` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` enum('Active','Inactive','','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NOT NULL,
  `deleted_at` timestamp(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 56 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_appadmin` tinyint(3) UNSIGNED NULL DEFAULT 0,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;

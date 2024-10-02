-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2024 at 02:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: crm
--

-- --------------------------------------------------------

--
-- Table structure for table countries
--

CREATE TABLE countries (
  id bigint(20) UNSIGNED NOT NULL,
  country varchar(255) NOT NULL,
  status enum('active','inactive') NOT NULL DEFAULT 'active',
  deleted_at timestamp NULL DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table countries
--

INSERT INTO countries (id, country, status, deleted_at, created_at, updated_at) VALUES
(1, 'Saudi Arabia', 'active', NULL, '2024-01-15 16:15:32', '2024-01-15 16:15:32'),
(2, 'United States', 'active', NULL, '2024-01-15 16:15:39', '2024-01-15 16:15:39'),
(3, 'United Kingdom', 'active', NULL, '2024-01-15 16:15:49', '2024-01-15 16:15:49'),
(4, 'United Arab Emirates', 'active', NULL, '2024-01-15 16:15:56', '2024-01-15 16:15:56'),
(5, 'France', 'active', NULL, '2024-01-15 16:16:12', '2024-01-15 16:16:12'),
(6, 'Germany', 'active', NULL, '2024-01-15 16:16:19', '2024-01-15 16:16:19'),
(7, 'Algeria', 'active', NULL, '2024-01-15 16:17:31', '2024-01-15 16:17:31'),
(8, 'Albania', 'active', NULL, '2024-01-15 16:25:49', '2024-01-15 16:25:49'),
(9, 'Qatar', 'active', NULL, '2024-01-15 17:46:38', '2024-01-15 17:46:38'),
(19, 'Montenegro', 'active', NULL, '2024-01-16 13:29:24', '2024-01-16 13:29:24'),
(20, 'Armenia', 'active', NULL, '2024-01-23 10:50:53', '2024-01-23 10:50:53'),
(21, 'Georgia', 'active', NULL, '2024-01-23 10:58:42', '2024-01-23 10:58:42'),
(22, 'India', 'active', NULL, '2024-01-23 11:07:12', '2024-01-23 11:07:12'),
(23, 'Korea, South (South Korea)', 'active', NULL, '2024-01-27 13:24:27', '2024-02-05 11:50:33'),
(24, 'Maldives', 'active', NULL, '2024-02-06 11:17:28', '2024-02-06 11:17:28'),
(25, 'Greece', 'active', NULL, '2024-02-22 15:19:42', '2024-02-22 15:19:42'),
(26, 'AUSTRIA', 'active', NULL, '2024-02-22 15:19:42', '2024-02-22 15:19:42'),
(35, 'Oman', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(36, 'Tunisia', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(37, 'Nigeria', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(38, 'Netherlands', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(39, 'Canada', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(40, 'Riyadh', 'active', NULL, '2024-03-30 16:12:20', '2024-03-30 16:12:20'),
(41, 'Switzerland', 'active', NULL, '2024-05-08 20:07:30', '2024-05-08 20:07:30'),
(42, 'Rwanda', 'active', NULL, '2024-07-09 10:10:41', '2024-07-09 10:10:41'),
(43, 'Tanzania', 'active', NULL, '2024-07-09 10:15:42', '2024-07-09 10:15:42'),
(44, 'Pakistan', 'active', NULL, '2024-07-18 10:12:32', '2024-07-18 10:12:32'),
(45, 'Romania', 'active', NULL, '2024-07-18 10:12:32', '2024-07-18 10:12:32'),
(46, 'Nepal', 'active', NULL, '2024-07-18 10:12:32', '2024-07-18 10:12:32'),
(47, 'Somalia', 'active', NULL, '2024-07-18 10:12:32', '2024-07-18 10:12:32'),
(48, 'Zimbabwe', 'active', NULL, '2024-07-18 10:12:32', '2024-07-18 10:12:32'),
(49, 'Togo', 'active', NULL, '2024-07-18 10:12:33', '2024-07-18 10:12:33'),
(50, 'Botswana', 'active', NULL, '2024-07-18 10:21:11', '2024-07-18 10:21:11'),
(51, 'England', 'active', NULL, '2024-08-13 15:58:51', '2024-08-13 15:58:51'),
(52, 'Italy', 'active', NULL, '2024-08-20 13:27:23', '2024-08-20 13:27:23'),
(53, 'Kenya', 'active', NULL, '2024-08-31 17:18:09', '2024-08-31 17:18:09'),
(54, 'Indonesia', 'active', NULL, '2024-08-31 17:18:09', '2024-08-31 17:18:09'),
(55, 'USA', 'active', NULL, '2024-08-31 17:18:09', '2024-08-31 17:18:09'),
(56, 'South Africa', 'active', NULL, '2024-08-31 17:18:09', '2024-08-31 17:18:09'),
(57, 'Iraq', 'active', NULL, '2024-08-31 17:18:09', '2024-08-31 17:18:09'),
(58, 'Uganda', 'active', NULL, '2024-08-31 19:39:20', '2024-08-31 19:39:20'),
(59, 'Ghana', 'active', NULL, '2024-08-31 19:41:29', '2024-08-31 19:41:29'),
(60, 'Philippines', 'active', NULL, '2024-09-02 11:11:22', '2024-09-02 11:11:22'),
(61, 'Benin', 'active', NULL, '2024-09-02 11:11:22', '2024-09-02 11:11:22'),
(62, 'Morocco', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(63, 'Ethiopia', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(64, 'Afghanistan', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(65, 'South Sudan', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(66, 'Ivory Coast', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(67, 'Egypt', 'active', NULL, '2024-09-03 13:41:52', '2024-09-03 13:41:52'),
(68, 'Turkey', 'active', NULL, '2024-09-04 14:01:13', '2024-09-04 14:01:13'),
(69, 'California', 'active', NULL, '2024-09-05 13:27:04', '2024-09-05 13:27:04'),
(70, 'Sri Lanka', 'active', NULL, '2024-09-05 13:27:04', '2024-09-05 13:27:04'),
(71, 'Bangladesh', 'active', NULL, '2024-09-18 13:28:05', '2024-09-18 13:28:05');

INSERT INTO `countries` (`country`, `status`, `created_at`, `updated_at`) VALUES
('Andorra', 'active', NOW(), NOW()),
('Angola', 'active', NOW(), NOW()),
('Antigua and Barbuda', 'active', NOW(), NOW()),
('Argentina', 'active', NOW(), NOW()),
('Australia', 'active', NOW(), NOW()),
('Azerbaijan', 'active', NOW(), NOW()),
('Bahamas', 'active', NOW(), NOW()),
('Bahrain', 'active', NOW(), NOW()),
('Barbados', 'active', NOW(), NOW()),
('Belarus', 'active', NOW(), NOW()),
('Belgium', 'active', NOW(), NOW()),
('Belize', 'active', NOW(), NOW()),
('Bhutan', 'active', NOW(), NOW()),
('Bolivia', 'active', NOW(), NOW()),
('Bosnia and Herzegovina', 'active', NOW(), NOW()),
('Brazil', 'active', NOW(), NOW()),
('Brunei', 'active', NOW(), NOW()),
('Bulgaria', 'active', NOW(), NOW()),
('Burkina Faso', 'active', NOW(), NOW()),
('Burundi', 'active', NOW(), NOW()),
('Cambodia', 'active', NOW(), NOW()),
('Cameroon', 'active', NOW(), NOW()),
('Cape Verde', 'active', NOW(), NOW()),
('Central African Republic', 'active', NOW(), NOW()),
('Chad', 'active', NOW(), NOW()),
('Chile', 'active', NOW(), NOW()),
('China', 'active', NOW(), NOW()),
('Colombia', 'active', NOW(), NOW()),
('Comoros', 'active', NOW(), NOW()),
('Congo', 'active', NOW(), NOW()),
('Costa Rica', 'active', NOW(), NOW()),
('Croatia', 'active', NOW(), NOW()),
('Cuba', 'active', NOW(), NOW()),
('Cyprus', 'active', NOW(), NOW()),
('Czech Republic', 'active', NOW(), NOW()),
('Denmark', 'active', NOW(), NOW()),
('Djibouti', 'active', NOW(), NOW()),
('Dominica', 'active', NOW(), NOW()),
('Dominican Republic', 'active', NOW(), NOW()),
('Ecuador', 'active', NOW(), NOW()),
('El Salvador', 'active', NOW(), NOW()),
('Equatorial Guinea', 'active', NOW(), NOW()),
('Eritrea', 'active', NOW(), NOW()),
('Estonia', 'active', NOW(), NOW()),
('Eswatini', 'active', NOW(), NOW()),
('Fiji', 'active', NOW(), NOW()),
('Finland', 'active', NOW(), NOW()),
('Gabon', 'active', NOW(), NOW()),
('Gambia', 'active', NOW(), NOW()),
('Grenada', 'active', NOW(), NOW()),
('Guatemala', 'active', NOW(), NOW()),
('Guinea', 'active', NOW(), NOW()),
('Guinea-Bissau', 'active', NOW(), NOW()),
('Guyana', 'active', NOW(), NOW()),
('Haiti', 'active', NOW(), NOW()),
('Honduras', 'active', NOW(), NOW()),
('Hungary', 'active', NOW(), NOW()),
('Iceland', 'active', NOW(), NOW()),
('Iran', 'active', NOW(), NOW()),
('Ireland', 'active', NOW(), NOW()),
('Israel', 'active', NOW(), NOW()),
('Jamaica', 'active', NOW(), NOW()),
('Japan', 'active', NOW(), NOW()),
('Jordan', 'active', NOW(), NOW()),
('Kazakhstan', 'active', NOW(), NOW()),
('Kiribati', 'active', NOW(), NOW()),
('Kuwait', 'active', NOW(), NOW()),
('Kyrgyzstan', 'active', NOW(), NOW()),
('Laos', 'active', NOW(), NOW()),
('Latvia', 'active', NOW(), NOW()),
('Lebanon', 'active', NOW(), NOW()),
('Lesotho', 'active', NOW(), NOW()),
('Liberia', 'active', NOW(), NOW()),
('Libya', 'active', NOW(), NOW()),
('Liechtenstein', 'active', NOW(), NOW()),
('Lithuania', 'active', NOW(), NOW()),
('Luxembourg', 'active', NOW(), NOW()),
('Madagascar', 'active', NOW(), NOW()),
('Malawi', 'active', NOW(), NOW()),
('Malaysia', 'active', NOW(), NOW()),
('Mali', 'active', NOW(), NOW()),
('Malta', 'active', NOW(), NOW()),
('Marshall Islands', 'active', NOW(), NOW()),
('Mauritania', 'active', NOW(), NOW()),
('Mauritius', 'active', NOW(), NOW()),
('Mexico', 'active', NOW(), NOW()),
('Micronesia', 'active', NOW(), NOW()),
('Moldova', 'active', NOW(), NOW()),
('Monaco', 'active', NOW(), NOW()),
('Mongolia', 'active', NOW(), NOW()),
('Mozambique', 'active', NOW(), NOW()),
('Myanmar', 'active', NOW(), NOW()),
('Namibia', 'active', NOW(), NOW()),
('Nauru', 'active', NOW(), NOW()),
('Nicaragua', 'active', NOW(), NOW()),
('Niger', 'active', NOW(), NOW()),
('North Macedonia', 'active', NOW(), NOW()),
('Norway', 'active', NOW(), NOW()),
('Palau', 'active', NOW(), NOW()),
('Panama', 'active', NOW(), NOW()),
('Paraguay', 'active', NOW(), NOW()),
('Peru', 'active', NOW(), NOW()),
('Poland', 'active', NOW(), NOW()),
('Portugal', 'active', NOW(), NOW()),
('Russia', 'active', NOW(), NOW()),
('Saint Kitts and Nevis', 'active', NOW(), NOW()),
('Saint Lucia', 'active', NOW(), NOW()),
('Saint Vincent and the Grenadines', 'active', NOW(), NOW()),
('Samoa', 'active', NOW(), NOW()),
('San Marino', 'active', NOW(), NOW()),
('Sao Tome and Principe', 'active', NOW(), NOW()),
('Senegal', 'active', NOW(), NOW()),
('Serbia', 'active', NOW(), NOW()),
('Seychelles', 'active', NOW(), NOW()),
('Sierra Leone', 'active', NOW(), NOW()),
('Singapore', 'active', NOW(), NOW()),
('Slovakia', 'active', NOW(), NOW()),
('Slovenia', 'active', NOW(), NOW()),
('Solomon Islands', 'active', NOW(), NOW()),
('Suriname', 'active', NOW(), NOW()),
('Sweden', 'active', NOW(), NOW()),
('Taiwan', 'active', NOW(), NOW()),
('Thailand', 'active', NOW(), NOW()),
('Timor-Leste', 'active', NOW(), NOW()),
('Tonga', 'active', NOW(), NOW()),
('Trinidad and Tobago', 'active', NOW(), NOW()),
('Turkmenistan', 'active', NOW(), NOW()),
('Tuvalu', 'active', NOW(), NOW()),
('Uruguay', 'active', NOW(), NOW()),
('Uzbekistan', 'active', NOW(), NOW()),
('Vanuatu', 'active', NOW(), NOW()),
('Vatican City', 'active', NOW(), NOW()),
('Venezuela', 'active', NOW(), NOW()),
('Vietnam', 'active', NOW(), NOW()),
('Zambia', 'active', NOW(), NOW());


--
-- Indexes for dumped tables
--

--
-- Indexes for table countries
--
ALTER TABLE countries
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY countries_country_unique (country);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table countries
--
ALTER TABLE countries
  MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

this is my country table i want to add all remaning country nae without effecting and upper records 

ChatGPT said:
ChatGPT
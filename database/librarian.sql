-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 07:34 AM
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
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `librarian`
--

CREATE TABLE `librarian` (
  `staffId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `librarian`
--

INSERT INTO `librarian` (`staffId`, `name`, `email`, `phoneNumber`, `role`, `password`) VALUES
(1, 'Alice Johnson', 'alice.j@library.com', '555-1001', 'admin', 'd7f426595d8fcdb34a510a20c78c957f'),
(2, 'Bob Smith', 'bob.s@library.com', '555-1002', 'staff', '0cab1f01e180f1ff5b143076de8820d3'),
(3, 'Charlie Davis', 'charlie.d@library.com', '555-1003', 'staff', '9661aa9457a54ab363e0f502dac338a2'),
(4, 'Diana Prince', 'diana.p@library.com', '555-1004', 'admin', '032b77b6e587ef1460642cb9ca043852'),
(5, 'Ethan Hunt', 'ethan.h@library.com', '555-1005', 'staff', 'e86e6b1f4e46154861f7894b6f12478b'),
(6, 'Fiona Glenanne', 'fiona.g@library.com', '555-1006', 'staff', 'fc9ab0db79e1914292ec6cd7b3d4e03f'),
(7, 'George Costanza', 'george.c@library.com', '555-1007', 'staff', '90e725f2160eea43366bbe90f4937cad'),
(8, 'Hannah Abbott', 'hannah.a@library.com', '555-1008', 'staff', '00e0bef42b40ef87cd660f8abccf7af8'),
(9, 'Ian Wright', 'ian.w@library.com', '555-1009', 'admin', '8fe52b3767377c1965599ddc1dd5ec49'),
(10, 'Julia Roberts', 'julia.r@library.com', '555-1010', 'staff', 'd791b5503e39e34144a22e335a324635');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`staffId`),
  ADD UNIQUE KEY `email` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

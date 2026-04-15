-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 07:35 AM
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
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`id`, `title`, `author`, `isbn`, `genre`) VALUES
(1, 'The Big Black', 'Who Knows', '14356787624', 'Historical'),
(2, 'The Last Ember of Tomorrow', 'Abdul Wise', '52282', 'fiction'),
(3, 'Shadows Beneath Glass Skies', 'Kibo Solomon', '25306', 'historical'),
(4, 'A Clockwork Heart in Winter', 'Elizabeth Forbes', '16539', 'non-fiction'),
(5, 'The Silence of Falling Stars', 'Lee Z. Kirkland', '04837', 'space'),
(6, 'Echoes from the Ninth Horizon', 'Len Barnes', '51407', 'science fiction'),
(7, 'Ashes of a Forgotten Kingdom', 'Berk E. Warren', '63553', 'mythic'),
(8, 'The Girl Who Borrowed Time', 'Claudia Y. Brock', '66752', 'super natural'),
(9, 'When the Ocean Learned to Burn', 'Rigel Christensen', '68296', 'science fiction'),
(10, 'The Midnight Architect', 'Maggy O. Good', '15167', 'dystopian'),
(11, 'A Garden Planted in Storms', 'Amir E. Whitehead', '39652', 'thriller '),
(12, 'What will You do', 'Miguel Osen', '619', 'Thriller');

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy`
--

CREATE TABLE `bookcopy` (
  `id` int(11) NOT NULL,
  `bookID` int(11) DEFAULT NULL,
  `status` enum('available','checked_out','lost') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookcopy`
--

INSERT INTO `bookcopy` (`id`, `bookID`, `status`) VALUES
(1, 1, 'checked_out'),
(2, 12, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `fine`
--

CREATE TABLE `fine` (
  `fineId` int(11) NOT NULL,
  `loanId` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `id` int(11) NOT NULL,
  `bookCopyId` int(11) DEFAULT NULL,
  `studentId` int(11) DEFAULT NULL,
  `borrowDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `dueDate` date NOT NULL,
  `returnDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`id`, `bookCopyId`, `studentId`, `borrowDate`, `dueDate`, `returnDate`) VALUES
(1, 1, 101, '2026-04-13 21:25:15', '2026-04-27', '2026-04-27 00:00:00'),
(2, 1, 101, '2026-04-15 03:33:35', '2026-04-29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentId`, `name`, `email`, `phoneNumber`) VALUES
(101, 'John Doe', 'JohnDoe355@gmail.com', '(387)-234-2919'),
(102, 'Nolan Bright', 'a.aliquet@google.com', '(867) 736-1313'),
(103, 'Xanthus Brennan', 'ac.turpis@icloud.ca', '(843) 954-8189'),
(104, 'Shannon Bullock', 'euismod.et.commodo@icloud.couk', '(971) 869-7475'),
(105, 'Sheila Jackson', 'a@hotmail.ca', '1-680-636-7243'),
(106, 'Nasim Roman', 'enim.etiam@aol.couk', '(627) 145-9192'),
(107, 'Colleen Sargent', 'pede.praesent.eu@yahoo.org', '(805) 376-6781'),
(108, 'Chaney Coleman', 'at.risus@google.com', '1-127-238-4269'),
(109, 'Hiram Evans', 'ultrices.mauris@protonmail.edu', '1-385-964-8602'),
(110, 'Amanda Elliott', 'mi.ac@google.couk', '1-859-466-4528'),
(111, 'Yvette Deleon', 'suspendisse@outlook.ca', '(307) 827-1719');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookID` (`bookID`);

--
-- Indexes for table `fine`
--
ALTER TABLE `fine`
  ADD PRIMARY KEY (`fineId`),
  ADD KEY `loanId` (`loanId`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`staffId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookCopyId` (`bookCopyId`),
  ADD KEY `studentId` (`studentId`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bookcopy`
--
ALTER TABLE `bookcopy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fine`
--
ALTER TABLE `fine`
  MODIFY `fineId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD CONSTRAINT `bookcopy_ibfk_1` FOREIGN KEY (`bookID`) REFERENCES `book` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fine`
--
ALTER TABLE `fine`
  ADD CONSTRAINT `fine_ibfk_1` FOREIGN KEY (`loanId`) REFERENCES `loan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`bookCopyId`) REFERENCES `bookcopy` (`id`),
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

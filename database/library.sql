-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 08:35 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkEligibility` (IN `pStudentId` INT, OUT `isEligible` BOOLEAN, OUT `message` VARCHAR(255))   BEGIN
DECLARE activeLoan INT;
DECLARE totalFine DECIMAL(5,2);

SELECT COUNT(*) INTO activeLoan
FROM loan
WHERE studentId = pStudentId AND returnDate IS NULL;

SELECT IFNULL(SUM(f.amount), 0) INTO totalFine
FROM fine f
JOIN loan l ON f.loanId = l.id
WHERE l.studentId = pStudentId AND f.status = 'unpaid';

IF activeLoan >= 5 THEN
	SET isEligible = FALSE;
    SET message = 'Student has reached the limit of 5 active loans.';
ELSEIF totalFine >= 10.00 THEN
	SET isEligible = FALSE;
    SET message = CONCAT('Student restricted: Outstanding fines of $', totalFine);
ELSE
	SET isEligible = TRUE;
    SET message = 'Student can rent.';
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPopularBook` (IN `pStudentId` INT)   BEGIN

DECLARE studentMajor varchar(100);
SET studentMajor = getMajor(pStudentId);

SELECT b.title, b.author, b.genre, COUNT(l.id) AS borrowCount
FROM loan l
JOIN student s ON l.studentId = s.studentId
JOIN bookcopy bc ON l.bookCopyId = bc.id
JOIN book b ON bc.bookID = b.id
WHERE s.major = studentMajor
GROUP BY b.id
ORDER BY borrowCount DESC
LIMIT 5;

END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `getMajor` (`pStudentId` INT) RETURNS VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
DECLARE studentMajor varchar(100);

SELECT major INTO studentMajor 
FROM student
WHERE studentId = pStudentId;
RETURN studentMajor;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `isAvailable` (`pBookId` INT) RETURNS TINYINT(1) DETERMINISTIC BEGIN
DECLARE aCount INT;
SELECT COUNT(*) INTO aCount 
FROM bookcopy
WHERE bookID = pBookId AND status = 'available';
RETURN aCount > 0;
END$$

DELIMITER ;

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
(12, 'What will You do', 'Miguel Osen', '619', 'Thriller'),
(13, 'Harry Potter', 'J.K. Rowling', '3377889910', 'Fantasy');

--
-- Triggers `book`
--
DELIMITER $$
CREATE TRIGGER `makeBookCopy` AFTER INSERT ON `book` FOR EACH ROW BEGIN
DECLARE count INT DEFAULT 0;
WHILE count < 3 DO
	INSERT INTO bookcopy (bookID, status)
    VALUES (NEW.id, 'available');
    SET count = count + 1;
END WHILE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy`
--

CREATE TABLE `bookcopy` (
  `id` int(11) NOT NULL,
  `bookID` int(11) DEFAULT NULL,
  `status` enum('available','checked_out','lost','on_hold') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookcopy`
--

INSERT INTO `bookcopy` (`id`, `bookID`, `status`) VALUES
(3, 5, 'on_hold'),
(4, 1, 'available'),
(5, 10, 'available'),
(6, 4, 'available'),
(7, 3, 'available'),
(8, 11, 'available'),
(9, 6, 'available'),
(11, 12, 'available'),
(12, 7, 'available'),
(13, 8, 'available'),
(14, 9, 'available'),
(18, 5, 'available'),
(19, 1, 'available'),
(20, 10, 'available'),
(21, 4, 'checked_out'),
(22, 3, 'available'),
(23, 11, 'available'),
(24, 6, 'available'),
(25, 2, 'on_hold'),
(26, 12, 'available'),
(27, 7, 'available'),
(28, 8, 'available'),
(29, 9, 'available'),
(33, 5, 'available'),
(34, 1, 'available'),
(35, 10, 'available'),
(36, 4, 'available'),
(37, 3, 'available'),
(38, 11, 'available'),
(39, 6, 'available'),
(41, 12, 'available'),
(42, 7, 'available'),
(43, 8, 'available'),
(44, 9, 'available'),
(45, 13, 'available'),
(46, 13, 'available'),
(47, 13, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `fine`
--

CREATE TABLE `fine` (
  `fineId` int(11) NOT NULL,
  `loanId` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid'
) ;

--
-- Dumping data for table `fine`
--

INSERT INTO `fine` (`fineId`, `loanId`, `amount`, `status`) VALUES
(4, 24, 1.50, 'unpaid'),
(5, 26, 8.00, 'unpaid'),
(6, 22, 0.50, 'paid'),
(7, 23, 1.00, 'paid'),
(8, 24, 2.00, 'unpaid'),
(9, 25, 3.50, 'unpaid'),
(10, 26, 4.00, 'paid'),
(11, 27, 0.75, 'unpaid'),
(12, 28, 1.25, 'paid'),
(13, 29, 2.25, 'unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `hold`
--

CREATE TABLE `hold` (
  `id` int(11) NOT NULL,
  `studentId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `holdDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('waiting','notified','expired','completed') DEFAULT 'waiting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hold`
--

INSERT INTO `hold` (`id`, `studentId`, `bookId`, `holdDate`, `status`) VALUES
(1, 102, 2, '2026-04-22 08:15:58', 'notified'),
(2, 105, 5, '2026-04-22 08:15:58', 'waiting'),
(3, 103, 5, '2026-04-22 08:15:58', 'waiting'),
(4, 108, 5, '2026-04-22 08:15:58', 'waiting'),
(5, 106, 5, '2026-04-22 08:15:58', 'waiting'),
(6, 104, 5, '2026-04-22 08:15:58', 'waiting'),
(7, 101, 5, '2026-04-22 08:15:58', 'waiting'),
(8, 110, 5, '2026-04-22 08:15:58', 'waiting'),
(9, 107, 5, '2026-04-22 08:15:58', 'waiting'),
(10, 111, 5, '2026-04-22 08:15:58', 'notified');

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
(2, 'Bob Smith', 'bob.s@library.com', '555-1002', 'staff', 'password123'),
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
  `processedBy` int(3) DEFAULT NULL,
  `studentId` int(11) DEFAULT NULL,
  `loanStatus` enum('pending','active','returned','cancelled') DEFAULT 'pending',
  `borrowDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `dueDate` date NOT NULL,
  `returnDate` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`id`, `bookCopyId`, `processedBy`, `studentId`, `loanStatus`, `borrowDate`, `dueDate`, `returnDate`) VALUES
(22, 20, 6, 108, 'returned', '2026-04-22 08:25:03', '2026-04-24', '2026-04-22 04:23:00'),
(23, 28, 1, 101, 'returned', '2026-04-22 09:11:53', '2026-05-06', '2026-04-22 04:35:39'),
(24, 13, 3, 101, 'active', '2026-04-22 08:43:38', '2026-05-06', '2026-05-09 12:00:00'),
(25, 20, 3, 101, 'active', '2026-04-22 08:43:38', '2026-05-06', '2026-05-01 12:00:00'),
(26, 25, 1, 101, 'returned', '2026-04-22 09:33:04', '2026-05-06', '2026-05-22 12:00:00'),
(27, 21, NULL, 101, 'pending', '2026-04-23 01:35:02', '2026-05-06', NULL),
(28, 34, 2, 102, 'returned', '2026-04-23 10:15:00', '2026-05-07', '2026-05-06 16:22:00'),
(29, 35, 4, 103, 'returned', '2026-04-24 11:05:00', '2026-05-08', '2026-05-09 13:30:00'),
(30, 36, 5, 104, 'active', '2026-04-25 12:40:00', '2026-05-09', NULL),
(31, 37, NULL, 105, 'pending', '2026-04-26 14:10:00', '2026-05-10', NULL);

--
-- Triggers `loan`
--
DELIMITER $$
CREATE TRIGGER `loanEligibilityBeforeInsert` BEFORE INSERT ON `loan` FOR EACH ROW BEGIN
    DECLARE vIsEligible BOOLEAN DEFAULT TRUE;
    DECLARE vMessage VARCHAR(255) DEFAULT '';

    CALL checkEligibility(NEW.studentId, vIsEligible, vMessage);

    IF vIsEligible = FALSE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = vMessage;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `loanEligibilityBeforeActivate` BEFORE UPDATE ON `loan` FOR EACH ROW BEGIN
    DECLARE vIsEligible BOOLEAN DEFAULT TRUE;
    DECLARE vMessage VARCHAR(255) DEFAULT '';

    IF OLD.loanStatus <> 'active' AND NEW.loanStatus = 'active' THEN
        CALL checkEligibility(NEW.studentId, vIsEligible, vMessage);

        IF vIsEligible = FALSE THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = vMessage;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `onReturn` BEFORE UPDATE ON `loan` FOR EACH ROW IF NEW.returnDate IS NOT NULL AND OLD.returnDate IS NULL THEN
    SET NEW.loanStatus = 'returned';
    
    SELECT id INTO @nextHoldId 
    FROM hold 
    WHERE bookId = (SELECT bookID FROM bookcopy WHERE id = NEW.bookCopyId)
    AND status = 'waiting'
    ORDER BY holdDate ASC LIMIT 1;


IF @nextHoldId IS NOT NULL THEN
	UPDATE hold SET status = 'notified' WHERE id = 		@nextHoldId;
	UPDATE bookcopy SET status = 'on_hold' WHERE id = NEW.bookCopyId;
ELSE
	UPDATE bookcopy SET status = 'available' WHERE id = NEW.bookCopyId;
END IF;

IF NEW.returnDate > NEW.dueDate THEN 
	SET @daysLate = DATEDIFF(NEW.returnDate, NEW.dueDate);
	INSERT INTO fine (loanId, amount, status)
	VALUES (NEW.id, @daysLate * .50, 'unpaid');
END IF;

END IF
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `updateStatus` AFTER INSERT ON `loan` FOR EACH ROW BEGIN
    IF NEW.loanStatus = 'pending' THEN
        UPDATE bookcopy
        SET status = 'on_hold'
        WHERE id = NEW.bookCopyId;
    ELSEIF NEW.loanStatus = 'active' THEN
        UPDATE bookcopy
        SET status = 'checked_out'
        WHERE id = NEW.bookCopyId;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `onLoanProcessed` AFTER UPDATE ON `loan` FOR EACH ROW BEGIN
    IF OLD.loanStatus = 'pending' AND NEW.loanStatus = 'active' THEN
        UPDATE bookcopy
        SET status = 'checked_out'
        WHERE id = NEW.bookCopyId;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `major` varchar(100) DEFAULT 'General Studies'
) ;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentId`, `name`, `email`, `phoneNumber`, `password`, `major`) VALUES
(101, 'John Doe', 'JohnDoe355@gmail.com', '(387)-234-2919', 'securePass101', 'Computer Science'),
(102, 'Nolan Bright', 'a.aliquet@google.com', '(867) 736-1313', 'securePass102', 'Computer Science'),
(103, 'Xanthus Brennan', 'ac.turpis@icloud.ca', '(843) 954-8189', 'bizPassword456', 'Business Administration'),
(104, 'Shannon Bullock', 'euismod.et.commodo@icloud.couk', '(971) 869-7475', 'nursePass789', 'Nursing'),
(105, 'Sheila Jackson', 'a@hotmail.ca', '1-680-636-7243', 'engPassword321', 'Mechanical Engineering'),
(106, 'Nasim Roman', 'enim.etiam@aol.couk', '(627) 145-9192', 'psychPass654', 'Psychology'),
(107, 'Colleen Sargent', 'pede.praesent.eu@yahoo.org', '(805) 376-6781', 'csPassword789', 'Computer Science'),
(108, 'Chaney Coleman', 'at.risus@google.com', '1-127-238-4269', 'artPass123', 'Fine Arts'),
(109, 'Hiram Evans', 'ultrices.mauris@protonmail.edu', '1-385-964-8602', 'bioPass456', 'Biology'),
(110, 'Amanda Elliott', 'mi.ac@google.couk', '1-859-466-4528', 'nursePass321', 'Nursing'),
(111, 'Yvette Deleon', 'suspendisse@outlook.ca', '(307) 827-1719', 'bizPass789', 'Business Administration');

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
-- Indexes for table `hold`
--
ALTER TABLE `hold`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studentId` (`studentId`),
  ADD KEY `bookId` (`bookId`);

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
  ADD KEY `studentId` (`studentId`),
  ADD KEY `fk_librarian_loan` (`processedBy`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `bookcopy`
--
ALTER TABLE `bookcopy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `fine`
--
ALTER TABLE `fine`
  MODIFY `fineId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hold`
--
ALTER TABLE `hold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `hold`
--
ALTER TABLE `hold`
  ADD CONSTRAINT `hold_ibfk_1` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`),
  ADD CONSTRAINT `hold_ibfk_2` FOREIGN KEY (`bookId`) REFERENCES `book` (`id`);

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `fk_librarian_loan` FOREIGN KEY (`processedBy`) REFERENCES `librarian` (`staffId`),
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`bookCopyId`) REFERENCES `bookcopy` (`id`),
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

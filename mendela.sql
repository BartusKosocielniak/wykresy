-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 09, 2026 at 11:05 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mendela`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `temperatures`
--

CREATE TABLE `temperatures` (
  `day_number` int(11) DEFAULT NULL,
  `temperature` float DEFAULT NULL,
  `id` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `temperatures`
--

INSERT INTO `temperatures` (`day_number`, `temperature`, `id`) VALUES
(1, 36.3, 1),
(2, 36.6, 1),
(3, -1, 1),
(4, -1, 1),
(5, -1, 1),
(6, 0, 1),
(11, 36.2, 1),
(7, 37, 1),
(9, 0, 1),
(12, 36.6, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `imie` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `imie`) VALUES
(1, 'Maciej'),
(2, 'Edward');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(3, 'bkosciel@interia.pl', '$2y$10$L9poNjybuJkazBiZAE3tweRiwdb.uFvCEn29AAIEI8ohNVh3L0kOm'),
(2, 'Bart?omiej', '1234'),
(4, 'bjkkrakow@gmail.com', '$2y$10$UNX2aTkFkNv0TF51nseh/OfffR/fi2nG95gWnY1DUMienBHCGQ8S.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_chars`
--

CREATE TABLE `user_chars` (
  `id_char` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chars`
--

INSERT INTO `user_chars` (`id_char`, `user_id`) VALUES
(1, 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);

--
-- Indeksy dla tabeli `user_chars`
--
ALTER TABLE `user_chars`
  ADD PRIMARY KEY (`id_char`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_chars`
--
ALTER TABLE `user_chars`
  MODIFY `id_char` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

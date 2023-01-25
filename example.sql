-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 18 Sty 2023, 15:08
-- Wersja serwera: 10.4.24-MariaDB
-- Wersja PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `example`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `chats`
--

CREATE TABLE `chats` (
  `chat_id` int(11) NOT NULL,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `activated` int(1) NOT NULL DEFAULT 0,
  `activationcode` text NOT NULL,
  `storage_path` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `user1` (`user1`,`user2`),
  ADD KEY `user2` (`user2`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`,`storage_path`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`user1`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`user2`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

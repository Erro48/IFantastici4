-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Lug 16, 2021 alle 10:35
-- Versione del server: 10.4.8-MariaDB
-- Versione PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fanta_f1`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `rpossiede`
--

CREATE TABLE `rpossiede` (
  `id_possessione` int(11) NOT NULL,
  `id_squadra` int(11) NOT NULL,
  `id_pilota` int(11) NOT NULL,
  `attivo` bit(1) NOT NULL DEFAULT b'1',
  `campionato_corrente` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `rpossiede`
--

INSERT INTO `rpossiede` (`id_possessione`, `id_squadra`, `id_pilota`, `attivo`, `campionato_corrente`) VALUES
(1, 2, 3, b'1', b'1'),
(2, 2, 14, b'1', b'1'),
(3, 2, 4, b'1', b'1'),
(4, 2, 5, b'1', b'1'),
(5, 2, 11, b'1', b'1'),
(6, 1, 7, b'1', b'1'),
(7, 1, 13, b'1', b'1'),
(8, 1, 6, b'1', b'1'),
(9, 1, 16, b'1', b'1'),
(10, 1, 17, b'1', b'1'),
(11, 3, 1, b'1', b'1'),
(12, 3, 10, b'1', b'1'),
(13, 3, 18, b'1', b'1'),
(14, 3, 12, b'1', b'1'),
(15, 3, 20, b'1', b'1'),
(16, 4, 2, b'1', b'1'),
(17, 4, 8, b'1', b'1'),
(18, 4, 9, b'1', b'1'),
(19, 4, 15, b'1', b'1'),
(20, 4, 19, b'1', b'1');

-- --------------------------------------------------------

--
-- Struttura della tabella `tpiloti`
--

CREATE TABLE `tpiloti` (
  `id_pilota` int(11) NOT NULL,
  `nome_pilota` varchar(15) NOT NULL,
  `cognome_pilota` varchar(15) NOT NULL,
  `prezzo_base` float(5,2) NOT NULL,
  `prezzo_reale` float(5,2) NOT NULL,
  `k_scuderia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tpiloti`
--

INSERT INTO `tpiloti` (`id_pilota`, `nome_pilota`, `cognome_pilota`, `prezzo_base`, `prezzo_reale`, `k_scuderia`) VALUES
(1, 'Lewis', 'Hamilton', 33.50, 52.00, 1),
(2, 'Valtteri', 'Bottas', 23.60, 23.60, 1),
(3, 'Max', 'Verstappen', 24.80, 35.00, 2),
(4, 'Sergio', 'Perez', 18.40, 36.00, 2),
(5, 'Daniel', 'Ricciardo', 17.30, 31.00, 3),
(6, 'Lando', 'Norris', 13.10, 17.00, 3),
(7, 'Charles', 'Leclerc', 16.80, 26.00, 4),
(8, 'Carlos', 'Sainz', 14.40, 16.00, 4),
(9, 'Sebastian', 'Vettel', 16.20, 32.00, 5),
(10, 'Lance', 'Stroll', 13.90, 13.90, 5),
(11, 'Fernando', 'Alonso', 15.60, 18.00, 6),
(12, 'Esteban', 'Ocon', 10.10, 10.10, 6),
(13, 'Pierre', 'Gasly', 11.70, 21.00, 7),
(14, 'Yuki', 'Tsunoda', 8.80, 10.50, 7),
(15, 'Kimi', 'Raikkonen', 9.60, 18.00, 8),
(16, 'Antonio', 'Giovinazzi', 7.90, 7.90, 8),
(17, 'George', 'Russel', 6.20, 7.00, 9),
(18, 'Nicholas', 'Latifi', 6.50, 6.50, 9),
(19, 'Mick', 'Schumacher', 5.80, 7.00, 10),
(20, 'Nikita', 'Mazepin', 5.50, 5.50, 10);

-- --------------------------------------------------------

--
-- Struttura della tabella `tscuderie`
--

CREATE TABLE `tscuderie` (
  `id_scuderia` int(11) NOT NULL,
  `nome_scuderia` varchar(50) NOT NULL,
  `nome_breve` varchar(20) NOT NULL,
  `prezzo_base` float(5,2) NOT NULL,
  `prezzo_reale` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tscuderie`
--

INSERT INTO `tscuderie` (`id_scuderia`, `nome_scuderia`, `nome_breve`, `prezzo_base`, `prezzo_reale`) VALUES
(1, 'Mercedes AMG Petronas', 'Mercedes', 38.00, 38.00),
(2, 'Red Bull Racing', 'Red Bull', 25.90, 32.00),
(3, 'McLaren F1 Team', 'McLaren', 18.90, 28.00),
(4, 'Scuderia Ferrari', 'Ferrari', 18.10, 0.00),
(5, 'Aston Martin F1 Team', 'Aston Martin', 17.60, 17.60),
(6, 'Alpine F1 Team', 'Alpine', 15.40, 0.00),
(7, 'Scuderia AlphaTauri', 'AlphaTauri', 12.70, 0.00),
(8, 'Alfa Romeo Racing ORLEN', 'Alfa Romeo', 8.90, 0.00),
(9, 'Williams Racing', 'Williams', 6.30, 0.00),
(10, 'Uralkali Haas F1 Team', 'Haas', 6.10, 0.00);

-- --------------------------------------------------------

--
-- Struttura della tabella `tsquadre`
--

CREATE TABLE `tsquadre` (
  `id_squadra` int(11) NOT NULL,
  `nome_squadra` varchar(50) NOT NULL,
  `turbo_driver` varchar(15) NOT NULL,
  `mega_driver` varchar(15) DEFAULT NULL,
  `mega_driver_flag` tinyint(1) NOT NULL,
  `k_scuderia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tsquadre`
--

INSERT INTO `tsquadre` (`id_squadra`, `nome_squadra`, `turbo_driver`, `mega_driver`, `mega_driver_flag`, `k_scuderia`) VALUES
(1, 'Daniele TEAM', 'Norris', NULL, 1, 1),
(2, 'Racing Primate', 'Perez', NULL, 1, 5),
(3, 'Jacopo TEAM', 'Ocon', NULL, 0, 2),
(4, 'ST97', 'Sainz', NULL, 1, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `tutenti`
--

CREATE TABLE `tutenti` (
  `id_utente` int(11) NOT NULL,
  `nome_utente` varchar(15) NOT NULL,
  `password_utente` varchar(255) NOT NULL,
  `k_squadra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tutenti`
--

INSERT INTO `tutenti` (`id_utente`, `nome_utente`, `password_utente`, `k_squadra`) VALUES
(2, 'Enrico', '$2y$10$dimepRex9Kyfogn6OlQ9Yu.OP/u.nBaGlZXZD.QLy7y4AZOwp7HL2', 2),
(3, 'Daniele', '$2y$10$zPQhY0V3sxeogAiY7UuGW.ucLeMZkEVRf/dOA1Bh0cbT8EILVMRV.', 1),
(4, 'Jacopo', '$2y$10$uup4x2oBNu1r4ErX/r/v2u14hsbPTWNtIVjpW65duFNwYqonvFfna', 3),
(5, 'Sara', '$2y$10$OuKZLTceMHib1GdAjiAxNuLMHmG6xMr2Gx/O9PFhEtBXRs89Lpk0m', 4);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `rpossiede`
--
ALTER TABLE `rpossiede`
  ADD PRIMARY KEY (`id_possessione`),
  ADD KEY `rpossiede_ibfk_1` (`id_pilota`),
  ADD KEY `rpossiede_ibfk_2` (`id_squadra`);

--
-- Indici per le tabelle `tpiloti`
--
ALTER TABLE `tpiloti`
  ADD PRIMARY KEY (`id_pilota`),
  ADD KEY `k_scuderia` (`k_scuderia`);

--
-- Indici per le tabelle `tscuderie`
--
ALTER TABLE `tscuderie`
  ADD PRIMARY KEY (`id_scuderia`),
  ADD UNIQUE KEY `nome_scuderia` (`nome_scuderia`);

--
-- Indici per le tabelle `tsquadre`
--
ALTER TABLE `tsquadre`
  ADD PRIMARY KEY (`id_squadra`),
  ADD UNIQUE KEY `nome_squadra` (`nome_squadra`),
  ADD KEY `k_scuderia` (`k_scuderia`);

--
-- Indici per le tabelle `tutenti`
--
ALTER TABLE `tutenti`
  ADD PRIMARY KEY (`id_utente`),
  ADD UNIQUE KEY `nome_utente` (`nome_utente`),
  ADD KEY `k_squadra` (`k_squadra`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `rpossiede`
--
ALTER TABLE `rpossiede`
  MODIFY `id_possessione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT per la tabella `tpiloti`
--
ALTER TABLE `tpiloti`
  MODIFY `id_pilota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `tscuderie`
--
ALTER TABLE `tscuderie`
  MODIFY `id_scuderia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `tsquadre`
--
ALTER TABLE `tsquadre`
  MODIFY `id_squadra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `tutenti`
--
ALTER TABLE `tutenti`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `rpossiede`
--
ALTER TABLE `rpossiede`
  ADD CONSTRAINT `rpossiede_ibfk_1` FOREIGN KEY (`id_pilota`) REFERENCES `tpiloti` (`id_pilota`),
  ADD CONSTRAINT `rpossiede_ibfk_2` FOREIGN KEY (`id_squadra`) REFERENCES `tsquadre` (`id_squadra`);

--
-- Limiti per la tabella `tpiloti`
--
ALTER TABLE `tpiloti`
  ADD CONSTRAINT `tpiloti_ibfk_1` FOREIGN KEY (`k_scuderia`) REFERENCES `tscuderie` (`id_scuderia`);

--
-- Limiti per la tabella `tsquadre`
--
ALTER TABLE `tsquadre`
  ADD CONSTRAINT `tsquadre_ibfk_1` FOREIGN KEY (`k_scuderia`) REFERENCES `tscuderie` (`id_scuderia`);

--
-- Limiti per la tabella `tutenti`
--
ALTER TABLE `tutenti`
  ADD CONSTRAINT `tutenti_ibfk_1` FOREIGN KEY (`k_squadra`) REFERENCES `tsquadre` (`id_squadra`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

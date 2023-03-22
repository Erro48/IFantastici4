-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 22, 2023 alle 22:02
-- Versione del server: 10.4.20-MariaDB
-- Versione PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
(83, 1, 3, b'1', b'1'),
(84, 1, 7, b'1', b'1'),
(85, 1, 9, b'1', b'1'),
(86, 1, 19, b'1', b'1'),
(87, 1, 14, b'1', b'1'),
(88, 2, 13, b'1', b'1'),
(89, 2, 2, b'1', b'1'),
(90, 2, 12, b'1', b'1'),
(91, 2, 16, b'1', b'1'),
(92, 2, 20, b'1', b'1'),
(93, 3, 1, b'1', b'1'),
(94, 3, 4, b'1', b'1'),
(95, 3, 5, b'1', b'1'),
(96, 3, 17, b'1', b'1'),
(97, 3, 18, b'1', b'1'),
(98, 4, 8, b'1', b'1'),
(99, 4, 6, b'1', b'1'),
(100, 4, 10, b'1', b'1'),
(101, 4, 15, b'1', b'1'),
(102, 4, 11, b'1', b'1');

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
  `k_scuderia` int(11) NOT NULL,
  `campionato_corrente` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tpiloti`
--

INSERT INTO `tpiloti` (`id_pilota`, `nome_pilota`, `cognome_pilota`, `prezzo_base`, `prezzo_reale`, `k_scuderia`, `campionato_corrente`) VALUES
(1, 'Lewis', 'Hamilton', 33.50, 52.00, 1, 0),
(2, 'George', 'Russel', 23.60, 23.60, 1, 0),
(3, 'Max', 'Verstappen', 24.80, 35.00, 2, 0),
(4, 'Sergio', 'Perez', 18.40, 36.00, 2, 0),
(5, 'Oscar', 'Piastri', 17.30, 31.00, 3, 0),
(6, 'Lando', 'Norris', 13.10, 17.00, 3, 0),
(7, 'Charles', 'Leclerc', 16.80, 26.00, 4, 0),
(8, 'Carlos', 'Sainz', 14.40, 16.00, 4, 0),
(9, 'Fernando', 'Alonso', 16.20, 32.00, 5, 0),
(10, 'Lance', 'Stroll', 13.90, 13.90, 5, 0),
(11, 'Pierre', 'Gasly', 15.60, 18.00, 6, 0),
(12, 'Esteban', 'Ocon', 10.10, 10.10, 6, 0),
(13, 'Nick', 'DeVries', 11.70, 21.00, 7, 0),
(14, 'Yuki', 'Tsunoda', 8.80, 10.50, 7, 0),
(15, 'Valterri', 'Bottas', 9.60, 18.00, 8, 0),
(16, 'Guanyu', 'Zhou', 7.90, 7.90, 8, 0),
(17, 'Alexander', 'Albon', 6.20, 7.00, 9, 0),
(18, 'Logan', 'Sargeant', 6.50, 6.50, 9, 0),
(19, 'Kevin', 'Magnussen', 5.80, 7.00, 10, 0),
(20, 'Nico', 'Hulkenberg', 5.50, 5.50, 10, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `tscuderie`
--

CREATE TABLE `tscuderie` (
  `id_scuderia` int(11) NOT NULL,
  `nome_scuderia` varchar(50) NOT NULL,
  `nome_breve` varchar(20) NOT NULL,
  `prezzo_base` float(5,2) NOT NULL,
  `prezzo_reale` float(5,2) NOT NULL,
  `campionato_corrente` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tscuderie`
--

INSERT INTO `tscuderie` (`id_scuderia`, `nome_scuderia`, `nome_breve`, `prezzo_base`, `prezzo_reale`, `campionato_corrente`) VALUES
(1, 'Mercedes AMG Petronas F1 Team', 'Mercedes', 38.00, 38.00, 0),
(2, 'Oracle Red Bull Racing', 'Red Bull', 25.90, 32.00, 0),
(3, 'McLaren F1 Team', 'McLaren', 18.90, 28.00, 0),
(4, 'Scuderia Ferrari', 'Ferrari', 18.10, 18.10, 0),
(5, 'Aston Martin Aramco Cognizant F1 Team', 'Aston Martin', 17.60, 17.60, 0),
(6, 'BWT Alpine F1 Team', 'Alpine', 15.40, 0.00, 0),
(7, 'Scuderia AlphaTauri', 'AlphaTauri', 12.70, 0.00, 0),
(8, 'Alfa Romeo F1 Team Stake', 'Alfa Romeo', 8.90, 0.00, 0),
(9, 'Williams Racing', 'Williams', 6.30, 0.00, 0),
(10, 'MoneyGram Haas F1 Team', 'Haas', 6.10, 0.00, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `tsquadre`
--

CREATE TABLE `tsquadre` (
  `id_squadra` int(11) NOT NULL,
  `nome_squadra` varchar(50) NOT NULL,
  `punteggio_squadra` decimal(6,2) NOT NULL DEFAULT 0.00,
  `punteggio_precedente_squadra` decimal(6,2) NOT NULL DEFAULT 0.00,
  `ultimo_aggiornamento_punteggio_squadra` date NOT NULL DEFAULT '1000-01-01',
  `turbo_driver` varchar(15) NOT NULL,
  `mega_driver` varchar(15) DEFAULT NULL,
  `mega_driver_flag` tinyint(1) NOT NULL,
  `k_scuderia` int(11) NOT NULL,
  `k_2scuderia` int(11) DEFAULT NULL,
  `campionato_corrente` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tsquadre`
--

INSERT INTO `tsquadre` (`id_squadra`, `nome_squadra`, `punteggio_squadra`, `punteggio_precedente_squadra`, `ultimo_aggiornamento_punteggio_squadra`, `turbo_driver`, `mega_driver`, `mega_driver_flag`, `k_scuderia`, `k_2scuderia`, `campionato_corrente`) VALUES
(1, 'Daniele TEAM', '170.00', '0.00', '2023-03-05', 'Alonso', NULL, 0, 5, NULL, 1),
(2, 'Racing Primate', '82.00', '0.00', '2023-03-05', 'Ocon', NULL, 0, 2, NULL, 1),
(3, 'Jacopo TEAM', '29.00', '0.00', '2023-03-05', 'Piastri', NULL, 0, 4, NULL, 1),
(4, 'ST97', '129.00', '0.00', '2023-03-05', 'Stroll', NULL, 0, 1, NULL, 1);

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
(4, 'Jacopo', '$2y$10$dimepRex9Kyfogn6OlQ9Yu.OP/u.nBaGlZXZD.QLy7y4AZOwp7HL2', 3),
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
  ADD PRIMARY KEY (`id_scuderia`);

--
-- Indici per le tabelle `tsquadre`
--
ALTER TABLE `tsquadre`
  ADD PRIMARY KEY (`id_squadra`),
  ADD UNIQUE KEY `k_2scuderia` (`k_2scuderia`),
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
  MODIFY `id_possessione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT per la tabella `tpiloti`
--
ALTER TABLE `tpiloti`
  MODIFY `id_pilota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT per la tabella `tscuderie`
--
ALTER TABLE `tscuderie`
  MODIFY `id_scuderia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT per la tabella `tsquadre`
--
ALTER TABLE `tsquadre`
  MODIFY `id_squadra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  ADD CONSTRAINT `tsquadre_ibfk_1` FOREIGN KEY (`k_scuderia`) REFERENCES `tscuderie` (`id_scuderia`),
  ADD CONSTRAINT `tsquadre_ibfk_2` FOREIGN KEY (`k_2scuderia`) REFERENCES `tscuderie` (`id_scuderia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `tutenti`
--
ALTER TABLE `tutenti`
  ADD CONSTRAINT `tutenti_ibfk_1` FOREIGN KEY (`k_squadra`) REFERENCES `tsquadre` (`id_squadra`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

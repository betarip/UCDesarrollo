-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 13, 2016 at 04:45 PM
-- Server version: 10.0.23-MariaDB
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `accesos`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DarAcceso` (IN `p_usuario` VARCHAR(42), IN `p_puerta` INT(11), OUT `p_acceso` TINYINT(1), OUT `p_pin` INT(11))  begin
	declare num_rows integer;
	select count(*)  into num_rows 
		from puertas 
		where usuario = p_usuario and puerta = p_puerta;
	if(num_rows>0) then
		select pin into p_pin 
		from usuarios
		where UUID = p_usuario;
		set p_acceso = 1;
	else
		set p_pin = 0;
		set p_acceso = 0;
	end if;

end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `puertas`
--

CREATE TABLE `puertas` (
  `id` int(11) NOT NULL,
  `puerta` int(11) NOT NULL,
  `usuario` varchar(42) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `puertas`
--

INSERT INTO `puertas` (`id`, `puerta`, `usuario`) VALUES
(3, 1, '0x3E0x810xBC0xEC'),
(9, 2, '0x3E0x810xBC0xEC'),
(10, 2, '0x0F0xDB0x2F0xF9'),
(11, 3, '0x0F0xDB0x2F0xF9');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `uuid` varchar(44) NOT NULL,
  `pin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`uuid`, `pin`) VALUES
('0x0F0xDB0x2F0xF9', 4321),
('0x3E0x810xBC0xEC', 2323),
('AAAA', 7865);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `puertas`
--
ALTER TABLE `puertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario` (`usuario`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`uuid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `puertas`
--
ALTER TABLE `puertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `puertas`
--
ALTER TABLE `puertas`
  ADD CONSTRAINT `puertas_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`uuid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

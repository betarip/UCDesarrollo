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
-- Database: `asistencia`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarAlumno` (IN `i_alumno` INT(11), IN `i_uuid` VARCHAR(42), IN `i_nombre` VARCHAR(120), OUT `id_out` INT(11), OUT `exito` TINYINT(1))  begin
	declare num_rows integer;
	select count(*) into num_rows
		from alumno
		where id_alumno=i_alumno or uuid = i_uuid;
	if(num_rows > 0) then
		set exito =0;
		set id_out = 0;
	else
		select count(*) into num_rows
			from alumno
			where uuid = i_uuid;
		if(num_rows>0) then
			set exito=0;
			set id_out=0;
		else
			insert into alumno(id_alumno, uuid, nombre) values (i_alumno,i_uuid,i_nombre);
			set id_out = i_alumno;
			set exito = 1;
		end if;
	end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarClaseAlumno` (IN `i_alumno` INT(11), IN `i_clase` INT(11), OUT `id_out` INT(11), OUT `exito` TINYINT(1))  begin
	declare num_rows integer;
	select count(*) into num_rows
		from registro_alumno_clase
		where id_alumno=i_alumno and id_clase=i_clase;
	if(num_rows > 0) then
		set exito =0;
		set id_out = 0;
	else
		insert into registro_alumno_clase(id_registro, id_alumno, id_clase) values (null,i_alumno,i_clase);
		set id_out = LAST_INSERT_ID();
		set exito = 1;
	end if;
    end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarProfesor` (IN `i_nombre` VARCHAR(120), IN `i_uuid` VARCHAR(44), IN `i_usuario` VARCHAR(100), IN `i_pass` VARCHAR(20), OUT `id` INT(11), OUT `exito` TINYINT(1))  begin 
	declare num_rows integer;
	select count(*) into num_rows
		from profesor
		where uuid = i_uuid;
	if(num_rows>0) then
		set exito=0;
		set id=0;
	else
		select count(*) into num_rows
			from alumno
			where uuid = i_uuid;
		if(num_rows>0) then
			set exito=0;
			set id=0;
		else
			INSERT INTO profesor (nombre, uuid,usuario,pass) values (i_nombre , i_uuid,i_usuario,i_pass);
			set id = LAST_INSERT_ID();
			set exito =1;
		end if;
	end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarAsistencia` (IN `i_alumno` INT(11), IN `i_clase` INT(11), IN `i_horario` INT(11), OUT `id_out` INT(11), OUT `exito` TINYINT(1))  begin
	declare num_rows integer;
	select count(*) into num_rows
		from horario
		where id_clase=i_clase and id_horario= i_horario;
	if(num_rows < 1) then
		set exito = 0;
		set id_out = 0;
	else
		select count(*) into num_rows
			from registro_alumno_clase
			where id_clase=i_clase and id_alumno=i_alumno;
		if (num_rows > 0) then
			insert into asistencia (id_asistencia,id_clase,id_horario,id_alumno,hora) values (null,i_clase,i_horario,i_alumno,null);
			set id_out = LAST_INSERT_ID();
			set exito = 1;
		else
			set exito =2;
			set id_out = 0;
		end if;

	end if;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `alumno`
--

CREATE TABLE `alumno` (
  `id_alumno` int(11) NOT NULL,
  `uuid` varchar(42) NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `alumno`
--

INSERT INTO `alumno` (`id_alumno`, `uuid`, `nombre`) VALUES
(1, '0FDB2FF9', 'isaias');

-- --------------------------------------------------------

--
-- Table structure for table `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asistencia`
--

INSERT INTO `asistencia` (`id_asistencia`, `id_clase`, `id_horario`, `id_alumno`, `hora`) VALUES
(7, 25, 17, 1, '2016-05-10 16:44:56'),
(8, 25, 17, 1, '2016-05-10 16:45:01'),
(9, 25, 18, 1, '2016-05-10 16:57:25'),
(10, 25, 18, 1, '2016-05-10 17:22:06'),
(11, 25, 18, 1, '2016-05-10 17:27:37'),
(12, 25, 18, 1, '2016-05-10 17:32:21'),
(13, 25, 18, 1, '2016-05-10 17:35:31'),
(14, 25, 18, 1, '2016-05-11 10:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `clase`
--

CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `id_profesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clase`
--

INSERT INTO `clase` (`id_clase`, `nombre`, `id_profesor`) VALUES
(25, 'historia', 14);

-- --------------------------------------------------------

--
-- Table structure for table `horario`
--

CREATE TABLE `horario` (
  `id_horario` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `dia` varchar(1) NOT NULL,
  `hora_inicio` varchar(5) NOT NULL,
  `hora_fin` varchar(5) NOT NULL,
  `aula` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `horario`
--

INSERT INTO `horario` (`id_horario`, `id_clase`, `dia`, `hora_inicio`, `hora_fin`, `aula`) VALUES
(17, 25, 'L', '12:00', '13:30', '2'),
(18, 25, 'M', '12:00', '13:00', '2');

-- --------------------------------------------------------

--
-- Table structure for table `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `uuid` varchar(44) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `pass` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `profesor`
--

INSERT INTO `profesor` (`id_profesor`, `nombre`, `uuid`, `usuario`, `pass`) VALUES
(14, 'isaias', '3E81BCEC', 'user', '12345'),
(15, 'Ivan Perez Vazquez', '2E020D00000000', 'ivan', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `registro_alumno_clase`
--

CREATE TABLE `registro_alumno_clase` (
  `id_registro` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `registro_alumno_clase`
--

INSERT INTO `registro_alumno_clase` (`id_registro`, `id_clase`, `id_alumno`) VALUES
(35, 25, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumno`
--
ALTER TABLE `alumno`
  ADD UNIQUE KEY `id_alumno` (`id_alumno`);

--
-- Indexes for table `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_clase` (`id_clase`),
  ADD KEY `id_horario` (`id_horario`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indexes for table `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_profesor_2` (`id_profesor`);

--
-- Indexes for table `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indexes for table `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_profesor`);

--
-- Indexes for table `registro_alumno_clase`
--
ALTER TABLE `registro_alumno_clase`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_clase` (`id_clase`,`id_alumno`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `clase`
--
ALTER TABLE `clase`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `horario`
--
ALTER TABLE `horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `registro_alumno_clase`
--
ALTER TABLE `registro_alumno_clase`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id_horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencia_ibfk_3` FOREIGN KEY (`id_alumno`) REFERENCES `alumno` (`id_alumno`) ON DELETE CASCADE;

--
-- Constraints for table `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `clase_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE CASCADE;

--
-- Constraints for table `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `horario_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE;

--
-- Constraints for table `registro_alumno_clase`
--
ALTER TABLE `registro_alumno_clase`
  ADD CONSTRAINT `registro_alumno_clase_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_alumno_clase_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `alumno` (`id_alumno`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

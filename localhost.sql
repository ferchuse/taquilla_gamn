-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 27-12-2016 a las 18:39:06
-- Versión del servidor: 5.1.41
-- Versión de PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `road_local`
--
CREATE DATABASE `gamn_local` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gamn_local`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletos`
--

CREATE TABLE IF NOT EXISTS `boletos` (
  `cve` int(4) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `guia` int(4) NOT NULL,
  `unidad` int(4) NOT NULL,
  `no_eco` int(4) NOT NULL,
  `costo` int(4) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `usuario` int(4) NOT NULL,
  `estatus` tinyint(1) NOT NULL,
  `usucan` int(4) NOT NULL,
  `fechacan` datetime NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `costo_boletos`
--

CREATE TABLE IF NOT EXISTS `costo_boletos` (
  `cve` int(4) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `estatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `costo_boletos`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia`
--

CREATE TABLE IF NOT EXISTS `guia` (
  `cve` int(4) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `unidad` int(4) NOT NULL,
  `no_eco` int(4) NOT NULL,
  `usuario` int(4) NOT NULL,
  `fecha_fin` date NOT NULL,
  `hora_fin` time NOT NULL,
  `usuario_fin` int(4) NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `guia`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `taquilla`
--

CREATE TABLE IF NOT EXISTS `taquilla` (
  `cve` int(4) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_impresora` tinyint(1) NOT NULL,
  `nombre_impresora` varchar(100) NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `taquilla`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE IF NOT EXISTS `unidades` (
  `cve` int(4) NOT NULL,
  `no_eco` int(4) NOT NULL,
  `estatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `unidades`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `cve` int(4) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `tipo_taquilla` tinyint(1) NOT NULL,
  `estatus` varchar(1) NOT NULL,
  PRIMARY KEY (`cve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cve`, `nombre`, `usuario`, `password`, `tipo_taquilla`, `estatus`) VALUES
(1, 'Administrador', 'root', 'oceano', 1, 'A');



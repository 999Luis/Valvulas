-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 17-05-2026 a las 02:35:55
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `controlvalvulasbd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo_final`
--

DROP TABLE IF EXISTS `consumo_final`;
CREATE TABLE IF NOT EXISTS `consumo_final` (
  `id_consumo` int NOT NULL AUTO_INCREMENT,
  `calle_id` int DEFAULT NULL,
  `litros_totales` float DEFAULT NULL,
  `tiempo_segundos` int DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_consumo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_sistema`
--

DROP TABLE IF EXISTS `estado_sistema`;
CREATE TABLE IF NOT EXISTS `estado_sistema` (
  `id` int NOT NULL,
  `nombre_dispositivo` varchar(50) DEFAULT NULL,
  `flujo_actual` float DEFAULT '0',
  `estado_valvula` tinyint(1) DEFAULT '0',
  `nivel_tanque_pct` float DEFAULT '0',
  `ultima_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_sistema`
--

INSERT INTO `estado_sistema` (`id`, `nombre_dispositivo`, `flujo_actual`, `estado_valvula`, `nivel_tanque_pct`, `ultima_actualizacion`) VALUES
(1, 'Bomba', 0, 0, 0, '2026-05-02 00:46:45'),
(2, 'Calle 1', 0, 0, 0, '2026-05-02 00:46:26'),
(3, 'Calle 2', 0, 0, 0, '2026-05-01 20:11:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_eventos`
--

DROP TABLE IF EXISTS `log_eventos`;
CREATE TABLE IF NOT EXISTS `log_eventos` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `dispositivo_id` int DEFAULT NULL,
  `accion` varchar(20) DEFAULT NULL,
  `caudal_momento` float DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `log_eventos`
--

INSERT INTO `log_eventos` (`id_log`, `dispositivo_id`, `accion`, `caudal_momento`, `fecha_hora`) VALUES
(1, 1, 'Abierto', 0, '2026-05-01 20:11:17'),
(2, 2, 'Abierto', 0, '2026-05-01 20:11:19'),
(3, 3, 'Abierto', 0, '2026-05-01 20:11:20'),
(4, 1, 'Cerrado', 0, '2026-05-01 20:11:26'),
(5, 2, 'Cerrado', 0, '2026-05-01 20:11:27'),
(6, 3, 'Cerrado', 0, '2026-05-01 20:11:28'),
(7, 2, 'Abierto', 0, '2026-05-02 00:46:25'),
(8, 2, 'Cerrado', 0, '2026-05-02 00:46:26'),
(9, 1, 'Abierto', 0, '2026-05-02 00:46:39'),
(10, 1, 'Cerrado', 0, '2026-05-02 00:46:40'),
(11, 1, 'Abierto', 0, '2026-05-02 00:46:43'),
(12, 1, 'Cerrado', 0, '2026-05-02 00:46:45');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

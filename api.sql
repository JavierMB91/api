-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2026 a las 17:17:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `api`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_pedidos`
--

CREATE TABLE `linea_pedidos` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `numero_factura` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','procesando','enviado','entregado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `precio`, `descripcion`, `imagen`) VALUES
(1, 'LIB001', 'Alas de Sangre', 22.80, 'El fenómeno de fantasía de Rebecca Yarros.', 'img/alas_de_sangre.jpg'),
(2, 'LIB002', 'Hábitos Atómicos', 19.95, 'Cambios pequeños, resultados extraordinarios de James Clear.', 'img/habitos_atomicos.jpg'),
(3, 'COM001', 'One Piece Vol. 105', 8.50, 'El sueño de Luffy continúa en Wano.', 'img/one_piece_105.jpg'),
(4, 'LIB003', 'El problema de los 3 cuerpos', 21.90, 'La aclamada novela de ciencia ficción de Cixin Liu.', 'img/tres_cuerpos.jpg'),
(5, 'LIB004', 'Blackwater I: La riada', 9.90, 'La saga gótica de Michael McDowell que arrasa.', 'img/blackwater_1.jpg'),
(6, 'LIB005', 'La armadura de la luz', 24.90, 'El regreso a Kingsbridge de Ken Follett.', 'img/armadura_luz.jpg'),
(7, 'COM002', 'Heartstopper 5', 15.95, 'La novela gráfica romántica de Alice Oseman.', 'img/heartstopper_5.jpg'),
(8, 'ENC001', 'Enciclopedia Marvel', 45.00, 'La guía definitiva del Universo Marvel actualizada.', 'img/enciclopedia_marvel.jpg'),
(9, 'LIB006', 'El infinito en un junco', 21.90, 'La invención de los libros por Irene Vallejo.', 'img/infinito_junco.jpg'),
(10, 'COM003', 'Jujutsu Kaisen 0', 8.00, 'La precuela del exitoso manga de Gege Akutami.', 'img/jujutsu_kaisen_0.jpg'),
(26, 'LIB007', 'El Principito', 10.00, 'El Principito, obra maestra de Antoine de Saint-Exupéry, es un cuento poético y filosófico sobre un niño de otro planeta (Asteroide B-612) que viaja por el universo. A través de su inocencia y curiosidad, descubre la amistad, el amor y la superficialidad de los adultos, enseñando que «lo esencial es invisible a los ojos». ', 'el-principito_1770911404.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `mail`, `password`) VALUES
(1, 'Juan Perez', 'juan.perez@example.com', '$2y$10$Q1ru2EVpz1cLEs7/DoPfTOLTf8QpddimYGQyTlh3a4IWgR5A54oJe'),
(2, 'Maria Garcia', 'maria.garcia@example.com', '$2y$10$HNT/H0H7ZKHyGy6XTUNWtO9Rg2eO9HY1xDLVrOs3shBVRRNfNWVQe'),
(4, 'Jaime Losada', 'jaime.losada@example.com', '$2y$10$6OBh7DniGR4kuDRKnCVms.EzcnNOWlCOLt6twHh2CWmlUUxXDV6Be'),
(5, 'Administrador', 'admin@gmail.com', '$2y$10$bXqEAx8htkdyvLALjWl/Qe2H4JPOobGKIYlQNwIo26SQJn1OHOOYG');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `linea_pedidos`
--
ALTER TABLE `linea_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_linea_pedido_pedido` (`id_pedido`),
  ADD KEY `fk_linea_pedido_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `fk_pedido_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `linea_pedidos`
--
ALTER TABLE `linea_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `linea_pedidos`
--
ALTER TABLE `linea_pedidos`
  ADD CONSTRAINT `fk_linea_pedido_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_pedido_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

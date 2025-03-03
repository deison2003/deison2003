-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-03-2025 a las 20:53:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `super_mercado`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Lácteos'),
(2, 'Bebidas'),
(3, 'Carnes'),
(4, 'Verduras'),
(5, 'Panadería'),
(6, 'Snacks'),
(7, 'Congelados'),
(8, 'Dulces');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `telefono`) VALUES
(1, 'Juan Pérez', 'juan@example.com', '600123456'),
(2, 'María Gómez', 'maria@example.com', '610987654'),
(3, 'Carlos Rodríguez', 'carlos@example.com', '620456789'),
(4, 'Ana López', 'ana@example.com', '630678912'),
(5, 'Luis Fernández', 'luis@example.com', '640789123'),
(6, 'Elena Martínez', 'elena@example.com', '650321456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_ventas`
--

CREATE TABLE `detalles_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_ventas`
--

INSERT INTO `detalles_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 2, 1.20),
(2, 1, 3, 1, 10.00),
(3, 2, 4, 2, 0.80),
(4, 3, 2, 3, 2.50),
(5, 3, 5, 2, 1.50),
(6, 4, 6, 3, 2.00),
(7, 4, 8, 1, 1.80),
(8, 5, 7, 2, 3.50),
(9, 5, 9, 1, 4.00),
(10, 6, 10, 3, 2.50),
(11, 6, 2, 2, 2.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `stock`, `categoria_id`) VALUES
(1, 'Leche', 1.20, 100, 1),
(2, 'Zumo de Naranja', 2.50, 50, 2),
(3, 'Carne de Res', 10.00, 20, 3),
(4, 'Lechuga', 0.80, 150, 4),
(5, 'Pan Integral', 1.50, 60, 5),
(6, 'Galletas', 2.00, 90, 6),
(7, 'Queso', 3.50, 40, 1),
(8, 'Refresco Cola', 1.80, 70, 2),
(9, 'Helado de Vainilla', 4.00, 30, 7),
(10, 'Chocolate', 2.50, 80, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `cliente_id`, `fecha`, `total`) VALUES
(1, 1, '2024-03-01', 15.80),
(2, 2, '2024-03-02', 5.00),
(3, 3, '2024-03-03', 20.30),
(4, 4, '2024-03-04', 8.50),
(5, 5, '2024-03-05', 12.40),
(6, 6, '2024-03-06', 18.90);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes` (
`id` int(11)
,`nombre` varchar(100)
,`email` varchar(100)
,`telefono` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_compras`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_compras` (
`id` int(11)
,`nombre` varchar(100)
,`total_compras` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_sin_compras`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_sin_compras` (
`id` int(11)
,`nombre` varchar(100)
,`email` varchar(100)
,`telefono` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_detalles_ventas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_detalles_ventas` (
`id` int(11)
,`venta_id` int(11)
,`producto_id` int(11)
,`cantidad` int(11)
,`precio_unitario` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ingresos_por_categoria`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ingresos_por_categoria` (
`categoria` varchar(100)
,`ingresos` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos` (
`id` int(11)
,`nombre` varchar(150)
,`precio` decimal(10,2)
,`stock` int(11)
,`categoria_id` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_caros`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_caros` (
`id` int(11)
,`nombre` varchar(150)
,`precio` decimal(10,2)
,`stock` int(11)
,`categoria_id` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_con_categorias`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_con_categorias` (
`id` int(11)
,`nombre` varchar(150)
,`precio` decimal(10,2)
,`categoria` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_mas_vendidos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_mas_vendidos` (
`id` int(11)
,`nombre` varchar(150)
,`total_vendido` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_stock_bajo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_stock_bajo` (
`id` int(11)
,`nombre` varchar(150)
,`precio` decimal(10,2)
,`stock` int(11)
,`categoria_id` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ultimas_ventas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ultimas_ventas` (
`id` int(11)
,`cliente_id` int(11)
,`fecha` date
,`total` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas` (
`id` int(11)
,`cliente_id` int(11)
,`fecha` date
,`total` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas_clientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas_clientes` (
`id` int(11)
,`nombre` varchar(100)
,`fecha` date
,`total` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas_detalles`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas_detalles` (
`venta_id` int(11)
,`nombre` varchar(150)
,`cantidad` int(11)
,`precio_unitario` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas_mes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas_mes` (
`mes` int(2)
,`total_ventas` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes`
--
DROP TABLE IF EXISTS `vista_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes`  AS SELECT `clientes`.`id` AS `id`, `clientes`.`nombre` AS `nombre`, `clientes`.`email` AS `email`, `clientes`.`telefono` AS `telefono` FROM `clientes` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_compras`
--
DROP TABLE IF EXISTS `vista_clientes_compras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_compras`  AS SELECT `c`.`id` AS `id`, `c`.`nombre` AS `nombre`, count(`v`.`id`) AS `total_compras` FROM (`clientes` `c` left join `ventas` `v` on(`c`.`id` = `v`.`cliente_id`)) GROUP BY `c`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_sin_compras`
--
DROP TABLE IF EXISTS `vista_clientes_sin_compras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_sin_compras`  AS SELECT `clientes`.`id` AS `id`, `clientes`.`nombre` AS `nombre`, `clientes`.`email` AS `email`, `clientes`.`telefono` AS `telefono` FROM `clientes` WHERE !(`clientes`.`id` in (select distinct `ventas`.`cliente_id` from `ventas`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_detalles_ventas`
--
DROP TABLE IF EXISTS `vista_detalles_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_detalles_ventas`  AS SELECT `detalles_ventas`.`id` AS `id`, `detalles_ventas`.`venta_id` AS `venta_id`, `detalles_ventas`.`producto_id` AS `producto_id`, `detalles_ventas`.`cantidad` AS `cantidad`, `detalles_ventas`.`precio_unitario` AS `precio_unitario` FROM `detalles_ventas` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ingresos_por_categoria`
--
DROP TABLE IF EXISTS `vista_ingresos_por_categoria`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ingresos_por_categoria`  AS SELECT `c`.`nombre` AS `categoria`, sum(`dv`.`cantidad` * `dv`.`precio_unitario`) AS `ingresos` FROM ((`detalles_ventas` `dv` join `productos` `p` on(`dv`.`producto_id` = `p`.`id`)) join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) GROUP BY `c`.`nombre` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos`
--
DROP TABLE IF EXISTS `vista_productos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos`  AS SELECT `productos`.`id` AS `id`, `productos`.`nombre` AS `nombre`, `productos`.`precio` AS `precio`, `productos`.`stock` AS `stock`, `productos`.`categoria_id` AS `categoria_id` FROM `productos` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_caros`
--
DROP TABLE IF EXISTS `vista_productos_caros`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_caros`  AS SELECT `productos`.`id` AS `id`, `productos`.`nombre` AS `nombre`, `productos`.`precio` AS `precio`, `productos`.`stock` AS `stock`, `productos`.`categoria_id` AS `categoria_id` FROM `productos` WHERE `productos`.`precio` > 5.00 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_con_categorias`
--
DROP TABLE IF EXISTS `vista_productos_con_categorias`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_con_categorias`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`precio` AS `precio`, `c`.`nombre` AS `categoria` FROM (`productos` `p` join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_mas_vendidos`
--
DROP TABLE IF EXISTS `vista_productos_mas_vendidos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_mas_vendidos`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, sum(`dv`.`cantidad`) AS `total_vendido` FROM (`detalles_ventas` `dv` join `productos` `p` on(`dv`.`producto_id` = `p`.`id`)) GROUP BY `p`.`id` ORDER BY sum(`dv`.`cantidad`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_stock_bajo`
--
DROP TABLE IF EXISTS `vista_stock_bajo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_stock_bajo`  AS SELECT `productos`.`id` AS `id`, `productos`.`nombre` AS `nombre`, `productos`.`precio` AS `precio`, `productos`.`stock` AS `stock`, `productos`.`categoria_id` AS `categoria_id` FROM `productos` WHERE `productos`.`stock` < 20 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ultimas_ventas`
--
DROP TABLE IF EXISTS `vista_ultimas_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ultimas_ventas`  AS SELECT `ventas`.`id` AS `id`, `ventas`.`cliente_id` AS `cliente_id`, `ventas`.`fecha` AS `fecha`, `ventas`.`total` AS `total` FROM `ventas` ORDER BY `ventas`.`fecha` DESC LIMIT 0, 10 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas`
--
DROP TABLE IF EXISTS `vista_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas`  AS SELECT `ventas`.`id` AS `id`, `ventas`.`cliente_id` AS `cliente_id`, `ventas`.`fecha` AS `fecha`, `ventas`.`total` AS `total` FROM `ventas` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas_clientes`
--
DROP TABLE IF EXISTS `vista_ventas_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_clientes`  AS SELECT `v`.`id` AS `id`, `c`.`nombre` AS `nombre`, `v`.`fecha` AS `fecha`, `v`.`total` AS `total` FROM (`ventas` `v` join `clientes` `c` on(`v`.`cliente_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas_detalles`
--
DROP TABLE IF EXISTS `vista_ventas_detalles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_detalles`  AS SELECT `dv`.`venta_id` AS `venta_id`, `p`.`nombre` AS `nombre`, `dv`.`cantidad` AS `cantidad`, `dv`.`precio_unitario` AS `precio_unitario` FROM (`detalles_ventas` `dv` join `productos` `p` on(`dv`.`producto_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas_mes`
--
DROP TABLE IF EXISTS `vista_ventas_mes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_mes`  AS SELECT month(`ventas`.`fecha`) AS `mes`, sum(`ventas`.`total`) AS `total_ventas` FROM `ventas` GROUP BY month(`ventas`.`fecha`) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD CONSTRAINT `detalles_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalles_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

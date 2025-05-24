DROP TABLE IF EXISTS `habitaciones_b`;
CREATE TABLE IF NOT EXISTS `habitaciones_b` (
  `id_habitacion` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('Individual','Doble','Suite') COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Disponible','Ocupada','Mantenimiento') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id_habitacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

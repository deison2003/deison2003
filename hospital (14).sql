-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 24-05-2025 a las 03:30:06
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hospital`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `GenerarFacturaAutomatica`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerarFacturaAutomatica` (IN `p_id_paciente` INT, IN `p_descripcion` VARCHAR(255), IN `p_cantidad` INT, IN `p_precio` DECIMAL(10,2))   BEGIN
    DECLARE v_id_factura INT;
    DECLARE v_subtotal DECIMAL(10,2);
    
    SET v_subtotal = p_cantidad * p_precio;
    
    -- Insertar factura
    INSERT INTO facturacion (id_paciente, fecha_emision, total, estado)
    VALUES (p_id_paciente, CURDATE(), v_subtotal, 'Pendiente');
    
    SET v_id_factura = LAST_INSERT_ID();
    
    -- Insertar detalle
    INSERT INTO factura_detalle (id_factura, descripcion, cantidad, precio, subtotal)
    VALUES (v_id_factura, p_descripcion, p_cantidad, p_precio, v_subtotal);
END$$

DROP PROCEDURE IF EXISTS `GenerarReporteAuditoria`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerarReporteAuditoria` (IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
    -- Reporte de cambios en citas
    SELECT 'Cambios en Citas' AS tipo_reporte, COUNT(*) AS total
    FROM auditoria_citas
    WHERE fecha_cambio BETWEEN p_fecha_inicio AND p_fecha_fin
    
    UNION ALL
    
    -- Reporte de cambios en pacientes
    SELECT 'Cambios en Pacientes' AS tipo_reporte, COUNT(*) AS total
    FROM auditoria_pacientes
    WHERE fecha_cambio BETWEEN p_fecha_inicio AND p_fecha_fin
    
    UNION ALL
    
    -- Reporte de accesos
    SELECT 'Accesos a Datos' AS tipo_reporte, COUNT(*) AS total
    FROM registro_accesos
    WHERE fecha_acceso BETWEEN p_fecha_inicio AND p_fecha_fin;
END$$

DROP PROCEDURE IF EXISTS `ProgramarCitasRecurrentes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ProgramarCitasRecurrentes` (IN `p_id_paciente` INT, IN `p_id_medico` INT, IN `p_motivo` VARCHAR(255), IN `p_fecha_inicio` DATE, IN `p_hora` TIME, IN `p_intervalo_dias` INT, IN `p_numero_citas` INT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE fecha_cita DATETIME;
    
    WHILE i < p_numero_citas DO
        SET fecha_cita = TIMESTAMPADD(DAY, i * p_intervalo_dias, CONCAT(p_fecha_inicio, ' ', p_hora));
        
        INSERT INTO citas (id_paciente, id_medico, fecha_hora, motivo, estado)
        VALUES (p_id_paciente, p_id_medico, fecha_cita, p_motivo, 'Pendiente');
        
        SET i = i + 1;
    END WHILE;
END$$

DROP PROCEDURE IF EXISTS `TransferirPaciente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `TransferirPaciente` (IN `p_id_hospitalizacion` INT, IN `p_nueva_habitacion` INT)   BEGIN
    DECLARE v_habitacion_actual INT;
    DECLARE v_id_paciente INT;
    
    -- Obtener datos actuales
    SELECT id_habitacion, id_paciente INTO v_habitacion_actual, v_id_paciente
    FROM hospitalizaciones
    WHERE id_hospitalizacion = p_id_hospitalizacion;
    
    -- Verificar disponibilidad de la nueva habitación
    IF NOT HabitacionDisponible(p_nueva_habitacion) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'La habitación destino no está disponible';
    END IF;
    
    -- Actualizar hospitalización
    UPDATE hospitalizaciones
    SET id_habitacion = p_nueva_habitacion
    WHERE id_hospitalizacion = p_id_hospitalizacion;
    
    -- Liberar habitación anterior
    UPDATE habitaciones
    SET estado = 'Disponible'
    WHERE id_habitacion = v_habitacion_actual;
    
    -- Ocupar nueva habitación
    UPDATE habitaciones
    SET estado = 'Ocupada'
    WHERE id_habitacion = p_nueva_habitacion;
    
    -- Registrar en auditoría
    INSERT INTO registro_accesos (tabla_accedida, id_registro_accedido, usuario, fecha_acceso, accion)
    VALUES ('hospitalizaciones', p_id_hospitalizacion, CURRENT_USER(), NOW(), 
            CONCAT('Transferencia de habitación ', v_habitacion_actual, ' a ', p_nueva_habitacion));
END$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `CalcularEdad`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `CalcularEdad` (`fecha_nacimiento` DATE) RETURNS INT DETERMINISTIC BEGIN
    DECLARE edad INT;
    SET edad = TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE());
    RETURN edad;
END$$

DROP FUNCTION IF EXISTS `ContarCitasPaciente`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `ContarCitasPaciente` (`id_paciente_param` INT) RETURNS INT READS SQL DATA BEGIN
    DECLARE total_citas INT;
    
    SELECT COUNT(*) INTO total_citas
    FROM citas
    WHERE id_paciente = id_paciente_param;
    
    RETURN total_citas;
END$$

DROP FUNCTION IF EXISTS `HabitacionDisponible`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `HabitacionDisponible` (`id_habitacion_param` INT) RETURNS TINYINT(1) READS SQL DATA BEGIN
    DECLARE disponible BOOLEAN;
    
    SELECT estado = 'Disponible' INTO disponible
    FROM habitaciones
    WHERE id_habitacion = id_habitacion_param;
    
    RETURN disponible;
END$$

DROP FUNCTION IF EXISTS `TotalFacturadoPaciente`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `TotalFacturadoPaciente` (`id_paciente_param` INT) RETURNS DECIMAL(10,2) READS SQL DATA BEGIN
    DECLARE total DECIMAL(10,2);
    
    SELECT COALESCE(SUM(total), 0) INTO total
    FROM facturacion
    WHERE id_paciente = id_paciente_param;
    
    RETURN total;
END$$

DROP FUNCTION IF EXISTS `VerificarStockMedicamento`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `VerificarStockMedicamento` (`id_medicamento_param` INT, `cantidad_necesaria` INT) RETURNS TINYINT(1) READS SQL DATA BEGIN
    DECLARE stock_actual INT;
    DECLARE suficiente BOOLEAN;
    
    SELECT stock INTO stock_actual
    FROM medicamentos
    WHERE id_medicamento = id_medicamento_param;
    
    SET suficiente = (stock_actual >= cantidad_necesaria);
    
    RETURN suficiente;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_citas`
--

DROP TABLE IF EXISTS `auditoria_citas`;
CREATE TABLE IF NOT EXISTS `auditoria_citas` (
  `id_auditoria` int NOT NULL AUTO_INCREMENT,
  `id_cita` int NOT NULL,
  `cambio` varchar(255) NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `usuario` varchar(100) NOT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `id_cita` (`id_cita`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `auditoria_citas`
--

INSERT INTO `auditoria_citas` (`id_auditoria`, `id_cita`, `cambio`, `fecha_cambio`, `usuario`) VALUES
(1, 6, 'Estado cambiado de Confirmada a Cancelada', '2025-05-28 10:15:00', 'admin@hospital.com'),
(2, 7, 'Estado cambiado de Pendiente a Completada', '2025-05-29 16:30:00', 'admin@hospital.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_pacientes`
--

DROP TABLE IF EXISTS `auditoria_pacientes`;
CREATE TABLE IF NOT EXISTS `auditoria_pacientes` (
  `id_auditoria` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `campo_modificado` varchar(50) NOT NULL,
  `valor_anterior` varchar(255) DEFAULT NULL,
  `valor_nuevo` varchar(255) DEFAULT NULL,
  `fecha_cambio` datetime NOT NULL,
  `usuario` varchar(100) NOT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `auditoria_pacientes`
--

INSERT INTO `auditoria_pacientes` (`id_auditoria`, `id_paciente`, `campo_modificado`, `valor_anterior`, `valor_nuevo`, `fecha_cambio`, `usuario`) VALUES
(1, 2, 'telefono', '3012345678', '3012345688', '2025-05-27 11:20:00', 'admin@hospital.com'),
(2, 5, 'direccion', 'Carrera 12 #34-56', 'Carrera 12 #34-58', '2025-05-28 14:45:00', 'admin@hospital.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

DROP TABLE IF EXISTS `citas`;
CREATE TABLE IF NOT EXISTS `citas` (
  `id_cita` int NOT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pendiente',
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_medico`, `fecha_hora`, `estado`, `motivo`) VALUES
(1, 1, 1, '2025-06-01 09:00:00', 'Confirmada', 'Dolor en el pecho'),
(2, 2, 2, '2025-06-01 10:30:00', 'Confirmada', 'Erupción cutánea'),
(10, 10, 10, '2025-06-05 15:30:00', 'Confirmada', 'Dolor al orinar'),
(1, 1, 1, '2025-06-01 09:00:00', 'Confirmada', 'Dolor en el pecho'),
(2, 2, 2, '2025-06-01 10:30:00', 'Confirmada', 'Erupción cutánea'),
(3, 3, 3, '2025-06-02 11:00:00', 'Pendiente', 'Control pediátrico'),
(4, 4, 4, '2025-06-02 14:00:00', 'Confirmada', 'Dolores de cabeza frecuentes'),
(5, 5, 5, '2025-06-03 08:30:00', 'Confirmada', 'Problemas de visión'),
(6, 6, 6, '2025-06-03 16:00:00', 'Cancelada', 'Dolor en la rodilla'),
(7, 7, 7, '2025-06-04 10:00:00', 'Completada', 'Consulta ginecológica'),
(8, 8, 8, '2025-06-04 11:30:00', 'Pendiente', 'Seguimiento tratamiento'),
(9, 9, 9, '2025-06-05 13:00:00', 'Confirmada', 'Problemas de ansiedad'),
(10, 10, 10, '2025-06-05 15:30:00', 'Confirmada', 'Dolor al orinar');

--
-- Disparadores `citas`
--
DROP TRIGGER IF EXISTS `RegistrarCambioCita`;
DELIMITER $$
CREATE TRIGGER `RegistrarCambioCita` AFTER UPDATE ON `citas` FOR EACH ROW BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO registro_cambios_citas (id_cita, estado_anterior, estado_nuevo, fecha_cambio)
        VALUES (NEW.id_cita, OLD.estado, NEW.estado, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id_especialidad` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id_especialidad`, `nombre`) VALUES
(1, 'Cardiología'),
(2, 'Dermatología'),
(3, 'Pediatría'),
(4, 'Neurología'),
(5, 'Oftalmología'),
(6, 'Traumatología'),
(7, 'Ginecología'),
(8, 'Oncología'),
(9, 'Psiquiatría'),
(10, 'Urología'),
(11, 'Medicina Interna'),
(12, 'Endocrinología'),
(13, 'Neumología'),
(14, 'Gastroenterología'),
(15, 'Hematología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_detalle`
--

DROP TABLE IF EXISTS `factura_detalle`;
CREATE TABLE IF NOT EXISTS `factura_detalle` (
  `id_detalle` int NOT NULL,
  `id_factura` int NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `factura_detalle`
--

INSERT INTO `factura_detalle` (`id_detalle`, `id_factura`, `descripcion`, `cantidad`, `precio`, `subtotal`) VALUES
(1, 1, 'Consulta especializada', 1, 80000.00, 80000.00);

--
-- Disparadores `factura_detalle`
--
DROP TRIGGER IF EXISTS `ActualizarTotalFactura`;
DELIMITER $$
CREATE TRIGGER `ActualizarTotalFactura` AFTER INSERT ON `factura_detalle` FOR EACH ROW BEGIN
    DECLARE total_factura DECIMAL(10,2);
    
    SELECT SUM(subtotal) INTO total_factura
    FROM factura_detalle
    WHERE id_factura = NEW.id_factura;
    
    UPDATE facturacion
    SET total = total_factura
    WHERE id_factura = NEW.id_factura;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `CalcularSubtotalFactura`;
DELIMITER $$
CREATE TRIGGER `CalcularSubtotalFactura` BEFORE INSERT ON `factura_detalle` FOR EACH ROW BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formula_medicamento`
--

DROP TABLE IF EXISTS `formula_medicamento`;
CREATE TABLE IF NOT EXISTS `formula_medicamento` (
  `id_formula` int NOT NULL,
  `id_medicamento` int NOT NULL,
  `cantidad` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `formula_medicamento`
--

INSERT INTO `formula_medicamento` (`id_formula`, `id_medicamento`, `cantidad`) VALUES
(1, 1, 20),
(2, 3, 30),
(3, 5, 15),
(4, 7, 60),
(5, 9, 10);

--
-- Disparadores `formula_medicamento`
--
DROP TRIGGER IF EXISTS `ActualizarStockMedicamento`;
DELIMITER $$
CREATE TRIGGER `ActualizarStockMedicamento` AFTER INSERT ON `formula_medicamento` FOR EACH ROW BEGIN
    UPDATE medicamentos
    SET stock = stock - NEW.cantidad
    WHERE id_medicamento = NEW.id_medicamento;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `actualizar_stock_medicamento`;
DELIMITER $$
CREATE TRIGGER `actualizar_stock_medicamento` AFTER INSERT ON `formula_medicamento` FOR EACH ROW BEGIN
    UPDATE medicamentos 
    SET stock = stock - NEW.cantidad 
    WHERE id_medicamento = NEW.id_medicamento;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

DROP TABLE IF EXISTS `habitaciones`;
CREATE TABLE IF NOT EXISTS `habitaciones` (
  `id_habitacion` int NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('Individual','Doble','Suite') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Disponible','Ocupada','Mantenimiento') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id_habitacion`, `numero`, `tipo`, `estado`) VALUES
(1, '101', 'Individual', 'Ocupada'),
(2, '102', 'Individual', 'Disponible'),
(3, '201', 'Doble', 'Ocupada'),
(4, '202', 'Doble', 'Disponible'),
(5, '301', 'Suite', 'Ocupada'),
(6, '302', 'Suite', 'Disponible'),
(7, '103', 'Individual', 'Mantenimiento'),
(8, '203', 'Doble', 'Ocupada'),
(9, '303', 'Suite', 'Disponible'),
(10, '104', 'Individual', 'Ocupada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospitalizaciones`
--

DROP TABLE IF EXISTS `hospitalizaciones`;
CREATE TABLE IF NOT EXISTS `hospitalizaciones` (
  `id_hospitalizacion` int NOT NULL,
  `id_paciente` int NOT NULL,
  `id_habitacion` int NOT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_salida` datetime DEFAULT NULL,
  `estado` enum('En curso','Finalizada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'En curso',
  `fecha_egreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hospitalizaciones`
--

INSERT INTO `hospitalizaciones` (`id_hospitalizacion`, `id_paciente`, `id_habitacion`, `fecha_ingreso`, `fecha_salida`, `estado`, `fecha_egreso`) VALUES
(1, 1, 1, '2025-05-20 10:00:00', NULL, 'En curso', NULL),
(2, 3, 3, '2025-05-18 14:30:00', '2025-05-22 11:00:00', 'Finalizada', '2025-05-22'),
(3, 5, 5, '2025-05-15 08:45:00', NULL, 'En curso', NULL),
(4, 7, 8, '2025-05-10 16:20:00', '2025-05-17 09:15:00', 'Finalizada', '2025-05-17'),
(5, 9, 10, '2025-05-22 12:00:00', NULL, 'En curso', NULL);

--
-- Disparadores `hospitalizaciones`
--
DROP TRIGGER IF EXISTS `ActualizarEstadoHabitacion`;
DELIMITER $$
CREATE TRIGGER `ActualizarEstadoHabitacion` AFTER INSERT ON `hospitalizaciones` FOR EACH ROW BEGIN
    UPDATE habitaciones
    SET estado = 'Ocupada'
    WHERE id_habitacion = NEW.id_habitacion;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `actualizar_estado_habitacion`;
DELIMITER $$
CREATE TRIGGER `actualizar_estado_habitacion` AFTER INSERT ON `hospitalizaciones` FOR EACH ROW BEGIN
    UPDATE habitaciones 
    SET estado = 'Ocupada' 
    WHERE id_habitacion = NEW.id_habitacion;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `liberar_habitacion`;
DELIMITER $$
CREATE TRIGGER `liberar_habitacion` AFTER UPDATE ON `hospitalizaciones` FOR EACH ROW BEGIN
    IF NEW.estado = 'Finalizada' AND OLD.estado = 'En curso' THEN
        UPDATE habitaciones 
        SET estado = 'Disponible' 
        WHERE id_habitacion = NEW.id_habitacion;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

DROP TABLE IF EXISTS `medicamentos`;
CREATE TABLE IF NOT EXISTS `medicamentos` (
  `id_medicamento` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stock` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`id_medicamento`, `nombre`, `descripcion`, `stock`) VALUES
(0, 'Helado', 'Que te importa', 20),
(1, 'Paracetamol', 'Analgésico y antipirético', 460),
(2, 'Ibuprofeno', 'Antiinflamatorio no esteroideo', 300),
(3, 'Amoxicilina', 'Antibiótico de amplio espectro', 140),
(4, 'Omeprazol', 'Inhibidor de la bomba de protones', 400),
(5, 'Loratadina', 'Antihistamínico para alergias', 220),
(6, 'Atorvastatina', 'Reductor de colesterol', 150),
(7, 'Metformina', 'Antidiabético oral', 60),
(8, 'Losartán', 'Antihipertensivo', 220),
(9, 'Diazepam', 'Ansiolítico y relajante muscular', 80),
(10, 'Salbutamol', 'Broncodilatador', 120);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

DROP TABLE IF EXISTS `medicos`;
CREATE TABLE IF NOT EXISTS `medicos` (
  `id_medico` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo_electronico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_especialidad` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`id_medico`, `nombre`, `apellido`, `telefono`, `correo_electronico`, `id_especialidad`) VALUES
(1, 'Carlos', 'Gómez', '3101234567', 'c.gomez@hospital.com', 1),
(2, 'Ana', 'Martínez', '3112345678', 'a.martinez@hospital.com', 2),
(3, 'Luis', 'Rodríguez', '3123456789', 'l.rodriguez@hospital.com', 3),
(4, 'María', 'López', '3134567890', 'm.lopez@hospital.com', 4),
(5, 'Jorge', 'Hernández', '3145678901', 'j.hernandez@hospital.com', 5),
(6, 'Patricia', 'García', '3156789012', 'p.garcia@hospital.com', 6),
(7, 'Ricardo', 'Pérez', '3167890123', 'r.perez@hospital.com', 7),
(8, 'Sofía', 'Díaz', '3178901234', 's.diaz@hospital.com', 8),
(9, 'Fernando', 'Sánchez', '3189012345', 'f.sanchez@hospital.com', 9),
(10, 'Laura', 'Ramírez', '3190123456', 'l.ramirez@hospital.com', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivos_especialidades`
--

DROP TABLE IF EXISTS `motivos_especialidades`;
CREATE TABLE IF NOT EXISTS `motivos_especialidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `motivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_especialidad` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_especialidad` (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `motivos_especialidades`
--

INSERT INTO `motivos_especialidades` (`id`, `motivo`, `id_especialidad`) VALUES
(1, 'Dolor en el pecho', 1),
(2, 'Erupción cutánea', 2),
(3, 'Control pediátrico', 3),
(4, 'Dolores de cabeza frecuentes', 4),
(5, 'Problemas de visión', 5),
(6, 'Dolor en la rodilla', 6),
(7, 'Consulta ginecológica', 7),
(8, 'Seguimiento tratamiento', 8),
(9, 'Problemas de ansiedad', 9),
(10, 'Dolor al orinar', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id_paciente` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo_electronico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_documento` enum('CC','TI','Pasaporte') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `nombre`, `apellido`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `correo_electronico`, `tipo_documento`, `numero_documento`) VALUES
(1, 'Juan', 'Pérez', '1980-05-15', 'Masculino', 'Calle 123 #45-67', '3001234567', 'juan.perez@email.com', 'CC', '1234567890'),
(2, 'María', 'González', '1992-08-22', 'Femenino', 'Carrera 56 #78-90', '3012345678', 'maria.gonzalez@email.com', 'CC', '2345678901'),
(3, 'Pedro', 'Martínez', '1975-11-30', 'Masculino', 'Avenida 34 #12-56', '3023456789', 'pedro.martinez@email.com', 'CC', '3456789012'),
(4, 'Ana', 'Rodríguez', '1988-03-10', 'Femenino', 'Calle 78 #90-12', '3034567890', 'ana.rodriguez@email.com', 'CC', '4567890123'),
(5, 'Carlos', 'López', '1995-07-18', 'Masculino', 'Carrera 12 #34-56', '3045678901', 'carlos.lopez@email.com', 'CC', '5678901234'),
(6, 'Luisa', 'Hernández', '1983-09-25', 'Femenino', 'Avenida 90 #78-56', '3056789012', 'luisa.hernandez@email.com', 'CC', '6789012345'),
(7, 'Jorge', 'García', '1970-12-05', 'Masculino', 'Calle 56 #34-12', '3067890123', 'jorge.garcia@email.com', 'CC', '7890123456'),
(8, 'Sandra', 'Díaz', '1990-02-14', 'Femenino', 'Carrera 78 #56-34', '3078901234', 'sandra.diaz@email.com', 'CC', '8901234567'),
(9, 'Miguel', 'Sánchez', '1985-06-20', 'Masculino', 'Avenida 12 #34-56', '3089012345', 'miguel.sanchez@email.com', 'CC', '9012345678'),
(10, 'Carolina', 'Ramírez', '1998-04-03', 'Femenino', 'Calle 34 #56-78', '3090123456', 'carolina.ramirez@email.com', 'CC', '0123456789');

--
-- Disparadores `pacientes`
--
DROP TRIGGER IF EXISTS `ValidarEdadPaciente`;
DELIMITER $$
CREATE TRIGGER `ValidarEdadPaciente` BEFORE INSERT ON `pacientes` FOR EACH ROW BEGIN
    DECLARE edad INT;
    SET edad = TIMESTAMPDIFF(YEAR, NEW.fecha_nacimiento, CURDATE());
    
    IF edad < 0 OR edad > 120 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Edad del paciente no válida';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `auditar_cambios_paciente`;
DELIMITER $$
CREATE TRIGGER `auditar_cambios_paciente` AFTER UPDATE ON `pacientes` FOR EACH ROW BEGIN
    -- Nombre
    IF OLD.nombre != NEW.nombre THEN
        INSERT INTO auditoria_pacientes (id_paciente, campo_modificado, valor_anterior, valor_nuevo, fecha_cambio, usuario)
        VALUES (NEW.id_paciente, 'nombre', OLD.nombre, NEW.nombre, NOW(), CURRENT_USER());
    END IF;
    
    -- Apellido
    IF OLD.apellido != NEW.apellido THEN
        INSERT INTO auditoria_pacientes (id_paciente, campo_modificado, valor_anterior, valor_nuevo, fecha_cambio, usuario)
        VALUES (NEW.id_paciente, 'apellido', OLD.apellido, NEW.apellido, NOW(), CURRENT_USER());
    END IF;
    
    -- Correo electrónico
    IF OLD.correo_electronico != NEW.correo_electronico THEN
        INSERT INTO auditoria_pacientes (id_paciente, campo_modificado, valor_anterior, valor_nuevo, fecha_cambio, usuario)
        VALUES (NEW.id_paciente, 'correo_electronico', OLD.correo_electronico, NEW.correo_electronico, NOW(), CURRENT_USER());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_accesos`
--

DROP TABLE IF EXISTS `registro_accesos`;
CREATE TABLE IF NOT EXISTS `registro_accesos` (
  `id_registro` int NOT NULL AUTO_INCREMENT,
  `tabla_accedida` varchar(50) NOT NULL,
  `id_registro_accedido` int DEFAULT NULL,
  `usuario` varchar(100) NOT NULL,
  `fecha_acceso` datetime NOT NULL,
  `accion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_registro`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `registro_accesos`
--

INSERT INTO `registro_accesos` (`id_registro`, `tabla_accedida`, `id_registro_accedido`, `usuario`, `fecha_acceso`, `accion`) VALUES
(1, 'pacientes', 3, 'admin@hospital.com', '2025-05-27 09:30:00', 'Actualización'),
(2, 'citas', 7, 'admin@hospital.com', '2025-05-28 16:45:00', 'Actualización');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

DROP TABLE IF EXISTS `tratamientos`;
CREATE TABLE IF NOT EXISTS `tratamientos` (
  `id_tratamiento` int NOT NULL,
  `id_historia` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tratamientos`
--

INSERT INTO `tratamientos` (`id_tratamiento`, `id_historia`, `descripcion`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 1, 'Tratamiento para hipertensión', '2025-05-20', '2025-08-20'),
(2, 2, 'Tratamiento para dermatitis', '2025-05-21', '2025-07-21'),
(3, 3, 'Antibióticos para infección', '2025-05-18', '2025-05-25'),
(4, 4, 'Medicación para migrañas', '2025-05-22', '2025-08-22'),
(5, 5, 'Gotas oculares', '2025-05-15', '2025-06-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `correo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `clave` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('admin','medico','recepcion') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin',
  `ip_ultimo_intento` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `intentos_fallidos` int NOT NULL DEFAULT '0',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `correo`, `clave`, `rol`, `ip_ultimo_intento`, `intentos_fallidos`, `bloqueado`) VALUES
(1, 'admin@hospital.com', '1234', 'admin', '::1', 0, 0),
(2, 'elsa@gmail.com', '1234', 'admin', '::1', 0, 0),
(3, 'gise@gmail.com', 'c37bf859faf392800d739a41fe5af151', 'recepcion', '::1', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_cinco_primeras_especialidades`
--

DROP TABLE IF EXISTS `vista_cinco_primeras_especialidades`;
CREATE TABLE IF NOT EXISTS `vista_cinco_primeras_especialidades` (
  `id_especialidad` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_cinco_ultimas_especialidades`
--

DROP TABLE IF EXISTS `vista_cinco_ultimas_especialidades`;
CREATE TABLE IF NOT EXISTS `vista_cinco_ultimas_especialidades` (
  `id_especialidad` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas`
--

DROP TABLE IF EXISTS `vista_citas`;
CREATE TABLE IF NOT EXISTS `vista_citas` (
  `id_cita` int DEFAULT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_canceladas`
--

DROP TABLE IF EXISTS `vista_citas_canceladas`;
CREATE TABLE IF NOT EXISTS `vista_citas_canceladas` (
  `id_cita` int DEFAULT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_confirmadas`
--

DROP TABLE IF EXISTS `vista_citas_confirmadas`;
CREATE TABLE IF NOT EXISTS `vista_citas_confirmadas` (
  `id_cita` int DEFAULT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_detalladas`
--

DROP TABLE IF EXISTS `vista_citas_detalladas`;
CREATE TABLE IF NOT EXISTS `vista_citas_detalladas` (
  `id_cita` int DEFAULT NULL,
  `paciente` varchar(100) DEFAULT NULL,
  `medico` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_medicos`
--

DROP TABLE IF EXISTS `vista_citas_medicos`;
CREATE TABLE IF NOT EXISTS `vista_citas_medicos` (
  `id_cita` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_pacientes`
--

DROP TABLE IF EXISTS `vista_citas_pacientes`;
CREATE TABLE IF NOT EXISTS `vista_citas_pacientes` (
  `id_cita` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_pendientes`
--

DROP TABLE IF EXISTS `vista_citas_pendientes`;
CREATE TABLE IF NOT EXISTS `vista_citas_pendientes` (
  `id_cita` int DEFAULT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_por_especialidad`
--

DROP TABLE IF EXISTS `vista_citas_por_especialidad`;
CREATE TABLE IF NOT EXISTS `vista_citas_por_especialidad` (
  `especialidad` varchar(100) DEFAULT NULL,
  `total_citas` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_citas_ultimo_mes`
--

DROP TABLE IF EXISTS `vista_citas_ultimo_mes`;
CREATE TABLE IF NOT EXISTS `vista_citas_ultimo_mes` (
  `id_cita` int DEFAULT NULL,
  `id_paciente` int DEFAULT NULL,
  `id_medico` int DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Completada') DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_consultas_por_dia`
--

DROP TABLE IF EXISTS `vista_consultas_por_dia`;
CREATE TABLE IF NOT EXISTS `vista_consultas_por_dia` (
  `fecha` date DEFAULT NULL,
  `total_citas` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_consultas_por_especialidad`
--

DROP TABLE IF EXISTS `vista_consultas_por_especialidad`;
CREATE TABLE IF NOT EXISTS `vista_consultas_por_especialidad` (
  `especialidad` varchar(100) DEFAULT NULL,
  `total_citas` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_consultas_por_medico`
--

DROP TABLE IF EXISTS `vista_consultas_por_medico`;
CREATE TABLE IF NOT EXISTS `vista_consultas_por_medico` (
  `nombre` varchar(100) DEFAULT NULL,
  `total_consultas` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_conteo_especialidades`
--

DROP TABLE IF EXISTS `vista_conteo_especialidades`;
CREATE TABLE IF NOT EXISTS `vista_conteo_especialidades` (
  `total_especialidades` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vista_especialidades`
--

DROP TABLE IF EXISTS `vista_especialidades`;
CREATE TABLE IF NOT EXISTS `vista_especialidades` (
  `id_especialidad` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

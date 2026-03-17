-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 25-02-2026 a las 04:37:30
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
-- Base de datos: `iqgym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas_clases`
--

CREATE TABLE `citas_clases` (
  `id_cita` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_clase` date NOT NULL,
  `confirmada` tinyint(1) DEFAULT 0,
  `asistio` tinyint(1) DEFAULT NULL,
  `fecha_reserva` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL,
  `nombre_clase` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_entrenador` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `cupo_maximo` int(11) DEFAULT 20,
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('masculino','femenino','otro') DEFAULT 'otro',
  `id_plan_actual` int(11) DEFAULT NULL,
  `estatus` enum('activo','inactivo','suspendido') DEFAULT 'activo',
  `fecha_vencimiento` date DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_ultima_visita` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `id_entrenador_asignado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `apellido`, `email`, `telefono`, `direccion`, `fecha_nacimiento`, `genero`, `id_plan_actual`, `estatus`, `fecha_vencimiento`, `foto_perfil`, `fecha_registro`, `fecha_ultima_visita`, `notas`, `id_entrenador_asignado`) VALUES
(1, 'feka', 'tawers', 'fekatawers@gmail.com', '123789456', NULL, NULL, 'otro', NULL, 'inactivo', NULL, NULL, '2026-02-19 17:34:59', NULL, NULL, NULL),
(2, 'lararios', 'topstado', 'lararios@gmail.com', '132456132', NULL, NULL, 'otro', NULL, 'inactivo', NULL, NULL, '2026-02-19 18:14:38', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos_calendario`
--

CREATE TABLE `eventos_calendario` (
  `id_evento` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_evento` enum('cierre','evento','horario_especial','mantenimiento') DEFAULT 'evento',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `todo_el_dia` tinyint(1) DEFAULT 0,
  `color` varchar(7) DEFAULT '#3788d8',
  `activo` tinyint(1) DEFAULT 1,
  `id_usuario_creador` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_modificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_plan` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','otro') DEFAULT 'efectivo',
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_inicio_vigencia` date NOT NULL,
  `fecha_fin_vigencia` date NOT NULL,
  `id_usuario_registro` int(11) NOT NULL,
  `estatus` enum('completado','cancelado','pendiente') DEFAULT 'completado',
  `referencia` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_modificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` int(11) NOT NULL,
  `nombre_plan` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `duracion_dias` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT NULL,
  `ultima_modificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`, `fecha_creacion`) VALUES
(1, 'admin', 'Administrador con acceso completo al sistema', '2026-01-23 00:05:23'),
(2, 'recepcionista', 'Personal de recepción con acceso limitado', '2026-01-23 00:05:23'),
(3, 'entrenador', 'Entrenador con acceso a clientes y rutinas', '2026-01-23 00:05:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas`
--

CREATE TABLE `rutinas` (
  `id_rutina` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_entrenador` int(11) NOT NULL,
  `nombre_rutina` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `ejercicios` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_modificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT NULL,
  `ultima_modificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `telefono`, `usuario`, `password`, `id_rol`, `activo`, `fecha_creacion`, `ultima_modificacion`) VALUES
(1, 'Carlos', 'Administrador', 'admin@iqgym.com', '6691234567', 'admin', '0192023a7bbd73250516f069df18b500', 1, 1, '2026-01-23 00:05:23', NULL),
(2, 'María', 'Recepción', 'recepcion@iqgym.com', '6691234568', 'recepcion', '591e1af5dec075239fcd6b2aa7dbb6cf', 2, 1, '2026-01-23 00:05:23', NULL),
(3, 'Juan', 'Trainer', 'trainer@iqgym.com', '6691234569', 'trainer1', '774ef6a662e111656cf1455ceeb1a142', 3, 1, '2026-01-23 00:05:23', NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_clases_disponibles`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_clases_disponibles` (
`id_clase` int(11)
,`nombre_clase` varchar(100)
,`entrenador` varchar(201)
,`dia_semana` tinyint(4)
,`hora_inicio` time
,`hora_fin` time
,`cupo_maximo` int(11)
,`reservas_actuales` bigint(21)
,`cupo_disponible` bigint(22)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_clientes_activos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_clientes_activos` (
`id_cliente` int(11)
,`nombre_completo` varchar(201)
,`email` varchar(150)
,`telefono` varchar(15)
,`nombre_plan` varchar(100)
,`precio` decimal(10,2)
,`estatus` enum('activo','inactivo','suspendido')
,`entrenador` varchar(201)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pagos_mes_actual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pagos_mes_actual` (
`id_pago` int(11)
,`cliente` varchar(201)
,`nombre_plan` varchar(100)
,`monto` decimal(10,2)
,`metodo_pago` enum('efectivo','tarjeta','transferencia','otro')
,`fecha_pago` datetime
,`estatus` enum('completado','cancelado','pendiente')
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_clases_disponibles`
--
DROP TABLE IF EXISTS `v_clases_disponibles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clases_disponibles`  AS SELECT `cl`.`id_clase` AS `id_clase`, `cl`.`nombre_clase` AS `nombre_clase`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `entrenador`, `cl`.`dia_semana` AS `dia_semana`, `cl`.`hora_inicio` AS `hora_inicio`, `cl`.`hora_fin` AS `hora_fin`, `cl`.`cupo_maximo` AS `cupo_maximo`, count(`cc`.`id_cita`) AS `reservas_actuales`, `cl`.`cupo_maximo`- count(`cc`.`id_cita`) AS `cupo_disponible` FROM ((`clases` `cl` left join `citas_clases` `cc` on(`cl`.`id_clase` = `cc`.`id_clase` and `cc`.`fecha_clase` >= curdate())) join `usuarios` `u` on(`cl`.`id_entrenador` = `u`.`id_usuario`)) WHERE `cl`.`activa` = 1 GROUP BY `cl`.`id_clase` HAVING `cupo_disponible` > 0 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_clientes_activos`
--
DROP TABLE IF EXISTS `v_clientes_activos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clientes_activos`  AS SELECT `c`.`id_cliente` AS `id_cliente`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `nombre_completo`, `c`.`email` AS `email`, `c`.`telefono` AS `telefono`, `p`.`nombre_plan` AS `nombre_plan`, `p`.`precio` AS `precio`, `c`.`estatus` AS `estatus`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `entrenador` FROM ((`clientes` `c` left join `planes` `p` on(`c`.`id_plan_actual` = `p`.`id_plan`)) left join `usuarios` `u` on(`c`.`id_entrenador_asignado` = `u`.`id_usuario`)) WHERE `c`.`estatus` = 'activo' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pagos_mes_actual`
--
DROP TABLE IF EXISTS `v_pagos_mes_actual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pagos_mes_actual`  AS SELECT `p`.`id_pago` AS `id_pago`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `cliente`, `pl`.`nombre_plan` AS `nombre_plan`, `p`.`monto` AS `monto`, `p`.`metodo_pago` AS `metodo_pago`, `p`.`fecha_pago` AS `fecha_pago`, `p`.`estatus` AS `estatus` FROM ((`pagos` `p` join `clientes` `c` on(`p`.`id_cliente` = `c`.`id_cliente`)) join `planes` `pl` on(`p`.`id_plan` = `pl`.`id_plan`)) WHERE month(`p`.`fecha_pago`) = month(curdate()) AND year(`p`.`fecha_pago`) = year(curdate()) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas_clases`
--
ALTER TABLE `citas_clases`
  ADD PRIMARY KEY (`id_cita`),
  ADD UNIQUE KEY `unique_reserva` (`id_clase`,`id_cliente`,`fecha_clase`),
  ADD KEY `idx_clase` (`id_clase`),
  ADD KEY `idx_cliente` (`id_cliente`),
  ADD KEY `idx_fecha` (`fecha_clase`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `idx_dia` (`dia_semana`),
  ADD KEY `idx_entrenador` (`id_entrenador`),
  ADD KEY `idx_activa` (`activa`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_nombre_apellido` (`nombre`,`apellido`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_telefono` (`telefono`),
  ADD KEY `idx_estatus` (`estatus`),
  ADD KEY `idx_plan` (`id_plan_actual`),
  ADD KEY `idx_entrenador` (`id_entrenador_asignado`),
  ADD KEY `idx_fecha_vencimiento` (`fecha_vencimiento`);

--
-- Indices de la tabla `eventos_calendario`
--
ALTER TABLE `eventos_calendario`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_usuario_creador` (`id_usuario_creador`),
  ADD KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`),
  ADD KEY `idx_tipo` (`tipo_evento`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_plan` (`id_plan`),
  ADD KEY `id_usuario_registro` (`id_usuario_registro`),
  ADD KEY `idx_cliente` (`id_cliente`),
  ADD KEY `idx_fecha_pago` (`fecha_pago`),
  ADD KEY `idx_estatus` (`estatus`),
  ADD KEY `idx_vigencia` (`fecha_inicio_vigencia`,`fecha_fin_vigencia`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`),
  ADD KEY `idx_nombre_plan` (`nombre_plan`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`),
  ADD KEY `idx_nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `rutinas`
--
ALTER TABLE `rutinas`
  ADD PRIMARY KEY (`id_rutina`),
  ADD KEY `idx_cliente` (`id_cliente`),
  ADD KEY `idx_entrenador` (`id_entrenador`),
  ADD KEY `idx_activa` (`activa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_usuario` (`usuario`),
  ADD KEY `idx_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas_clases`
--
ALTER TABLE `citas_clases`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `eventos_calendario`
--
ALTER TABLE `eventos_calendario`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rutinas`
--
ALTER TABLE `rutinas`
  MODIFY `id_rutina` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas_clases`
--
ALTER TABLE `citas_clases`
  ADD CONSTRAINT `citas_clases_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`id_clase`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_clases_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`id_entrenador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_plan_actual`) REFERENCES `planes` (`id_plan`) ON DELETE SET NULL,
  ADD CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`id_entrenador_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `eventos_calendario`
--
ALTER TABLE `eventos_calendario`
  ADD CONSTRAINT `eventos_calendario_ibfk_1` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_plan`) REFERENCES `planes` (`id_plan`),
  ADD CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `rutinas`
--
ALTER TABLE `rutinas`
  ADD CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `rutinas_ibfk_2` FOREIGN KEY (`id_entrenador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

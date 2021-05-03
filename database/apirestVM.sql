CREATE TABLE `balones` (
  `serial` char(11) COLLATE utf8_spanish_ci NOT NULL,
  `capacidad` tinyint(2) NOT NULL,
  `tulipa` tinyint(1) NOT NULL,
  `marca` char(20) COLLATE utf8_spanish_ci NOT NULL,
  `estado` char(10) COLLATE utf8_spanish_ci NOT NULL,
  `operacion` char(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `balones` (`serial`, `capacidad`, `tulipa`, `marca`, `estado`, `operacion`) VALUES
('1', 10, 1, 'casa', 'lleno', 'recibido'),
('2', 10, 1, 'norrys', 'vacio', 'recepcion'),
('3', 10, 1, 'pepe', 'vacio', 'enviado'),
('4', 10, 1, 'casa', 'lleno', 'recibido'),
('5', 10, 1, 'norrys', 'vacio', 'recepcion'),
('6', 10, 1, 'pepe', 'vacio', 'enviado'),
('7', 10, 1, 'casa', 'lleno', 'recibido'),
('8', 10, 1, 'norrys', 'vacio', 'recepcion'),
('9', 10, 1, 'pepe', 'vacio', 'enviado'),
('10', 10, 1, 'casa', 'lleno', 'recibido'),
('11', 10, 1, 'norrys', 'vacio', 'recepcion'),
('12', 10, 1, 'pepe', 'vacio', 'enviado');

CREATE TABLE `clientes` (
  `DNI` tinyint(11) NOT NULL,
  `nombre` char(20) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` tinyint(11) NOT NULL,
  `autorizador` char(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `DNIclientes` tinyint(11) NOT NULL,
  `DNIusuario` tinyint(11) NOT NULL,
  `serial` char(11) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `operacion` char(20) COLLATE utf8_spanish_ci NOT NULL,
  `estado` char(10) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE `pagos` (
  `id_movimiento` tinyint(11) NOT NULL,
  `pago` tinyint(20) NOT NULL,
  `monto` tinyint(255) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE `usuarios` (
  `DNI` tinyint(11) NOT NULL,
  `nombre` char(20) COLLATE utf8_spanish_ci NOT NULL,
  `password` tinyint(255) NOT NULL,
  `pribilegios` char(10) COLLATE utf8_spanish_ci NOT NULL,
  `estado` char(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE `usuarios_token` (
  `tokenId` int(11) NOT NULL,
  `DNI` varchar(45) DEFAULT NULL,
  `token` varchar(45) DEFAULT NULL,
  `estado` varchar(45) CHARACTER SET armscii8 DEFAULT NULL,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `balones`
  ADD PRIMARY KEY (`serial`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`DNI`);

ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_movimiento`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`DNI`);

ALTER TABLE `usuarios_token`
  ADD PRIMARY KEY (`tokenId`);


ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios_token`
  MODIFY `tokenId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
CREATE DATABASE celeris;

USE celeris;

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL,
    apellido_paterno VARCHAR(30) NOT NULL,
    apellido_materno VARCHAR(30) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    numero_cuenta VARCHAR(24) UNIQUE NOT NULL,
    activo BOOLEAN NOT NULL
);

CREATE TABLE departamentos (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(25) NOT NULL,
    descripcion TEXT
);
-- Ventanillas, Asociados, Caja_Fuerte, Asesoramiento

CREATE TABLE roles (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(15) NOT NULL
);
-- administrador, operador, recepcionista

CREATE TABLE horarios (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL
);

CREATE TABLE estado_caja (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(16) NOT NULL
);
-- abierta, cerrada, pausada, fuera_servicio

CREATE TABLE tipo_turno (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(10) NOT NULL
);
-- semanal/terciado

CREATE TABLE estado_turno (
    id INT(1) PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(12) NOT NULL
);
-- llamado, en_espera, cancelado, en_atencion, finalizado

CREATE TABLE tipo_turno_horarios (
    id_tipo_turno INT(1) NOT NULL,
    id_horario INT(1) NOT NULL,
    PRIMARY KEY (id_tipo_turno, id_horario),
    FOREIGN KEY (id_tipo_turno) REFERENCES tipo_turno(id),
    FOREIGN KEY (id_horario) REFERENCES horarios(id)
);

CREATE TABLE cajas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero INT NOT NULL,
    id_departamento INT NOT NULL,
    id_estado INT(1) NOT NULL,
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id),
    FOREIGN KEY (id_estado) REFERENCES estado_caja(id)
);

CREATE TABLE turnos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero INT NOT NULL,
    timestamp_solicitud TIMESTAMP NOT NULL,
    timestamp_llamado TIMESTAMP NULL,
    timestamp_inicio_atencion TIMESTAMP NULL,
    timestamp_fin_atencion TIMESTAMP NULL,
    id_caja INT NOT NULL,
    id_cliente INT NULL,
    FOREIGN KEY (id_caja) REFERENCES cajas(id),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

CREATE TABLE turnos_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_turno INT NOT NULL,
    id_estado INT(1) NOT NULL,
    timestamp_actualizacion TIMESTAMP NOT NULL,
    FOREIGN KEY (id_turno) REFERENCES turnos(id),
    FOREIGN KEY (id_estado) REFERENCES estado_turno(id)
);

CREATE TABLE empleados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL,
    apellido_paterno VARCHAR(30) NOT NULL,
    apellido_materno VARCHAR(30) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(64) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT 1,
    status BOOLEAN NOT NULL DEFAULT 0,
    id_departamento INT(1) NOT NULL,
    id_rol INT(1) NOT NULL,
    id_tipo_turno INT(1) NOT NULL,
    id_horario INT(1) NOT NULL,
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id),
    FOREIGN KEY (id_rol) REFERENCES roles(id),
    FOREIGN KEY (id_tipo_turno, id_horario) REFERENCES tipo_turno_horarios(id_tipo_turno, id_horario)
);

CREATE TABLE asignacion_cajas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_caja INT NOT NULL,
    id_empleado INT NOT NULL,
    FOREIGN KEY (id_caja) REFERENCES cajas(id),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id)
);


INSERT INTO departamentos (nombre, descripcion)
VALUES ("Ventanillas", ""), ("Asociados", ""), ("Caja Fuerte", ""), ("Asesoramiento Financiero", "");

INSERT INTO roles (nombre)
VALUES ("Administrador"), ("Operador"), ("Recepcionista");

INSERT INTO tipo_turno (nombre)
VALUES ("Terciado"), ("Semanal");

INSERT INTO horarios (hora_entrada, hora_salida)
VALUES ("09:00:00", "17:00:00");

INSERT INTO tipo_turno_horarios (id_tipo_turno, id_horario)
VALUES (1, 1), (2, 1);

INSERT INTO estado_turno (nombre)
VALUES ("Llamado"), ("En Espera"), ("En Atencion"), ("Cancelado"), ("Finalizado");

INSERT INTO estado_caja (nombre)
VALUES ("Abierta"), ("Cerrada"), ("Pausada"), ("Fuera de Servico");

-- CAJAS, TODAS LAS CAJAS ESTAN ABIERTAS
-- CAJAS DE VENTANILLAS
INSERT INTO cajas (numero, id_departamento, id_estado)
VALUES (1, 1, 1), (2, 1, 1), (3, 1, 1), (4, 1, 1), (5, 1, 1);

-- CAJAS DE ASOCIADOS
INSERT INTO cajas (numero, id_departamento, id_estado)
VALUES (1, 2, 1), (2, 2, 1), (3, 2, 1);

-- CAJAS DE CAJA FUERTE
INSERT INTO cajas (numero, id_departamento, id_estado)
VALUES (1, 3, 1), (2, 3, 1);

-- CAJAS DE ASESORAMIENTO FINANCIERO
INSERT INTO cajas (numero, id_departamento, id_estado)
VALUES (1, 4, 1), (2, 4, 1);

-- EMPLEADOS
-- ADMINISTRADORES
INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Arturo", "Avila", "Martinez", "arturoam@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 1, 1, 1),
("Hector", "Sanchez", "Tamayo", "hectorsan@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 1, 1, 1);

-- OPERADORES
-- OPERADORES VENTANILLAS
INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Jose Angel", "Santoyo", "Moreno", "josesan@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 1, 1),
("Mariana Sofia", "Hernandez", "Ruiz", "marianaher@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 1, 1),
("Jose", "Perez", "Gonzalez", "josepe@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 2, 1),
("Luis", "Esquivel", "Rojas", "luisesqui@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 2, 1),
("Juan", "Dominguez", "Lara", "juandom@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 2, 1);
-- ASIGNACIONES OPERADORES VENTANILLAS
INSERT INTO asignacion_cajas (id_caja, id_empleado) values (1,3), (2,4), (3,5), (4, 6), (5, 7);

-- OPERADORES ASOCIADOS
INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Diego Isaac", "Puentes", "Villa", "diegop@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 2, 2, 2, 1),
("Ricardo", "Salazar", "Pineda", "ricardosal@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 2, 2, 2, 1),
("Daniel", "Navarro", "Quiroz", "danielnav@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 2, 2, 2, 1);
-- ASIGNACIONES OPERADORES ASOCIADOS
INSERT INTO asignacion_cajas (id_caja, id_empleado) values (6,8), (7,9), (8, 10);

-- OPERADORES CAJA FUERTE
INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Cesar Eduardo", "Martinez", "Ramos", "cesarmar@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 3, 2, 2, 1),
("Valeria", "Torres", "Delgado", "valeriator@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 3, 2, 2, 1);
-- ASIGNACIONES OPERADORES CAJA FUERTE
INSERT INTO asignacion_cajas (id_caja, id_empleado) values (9,11), (10,12);

-- OPERADORES ASESORAMIENTO FINANCERO
INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Jorge Alberto", "Mendez", "Castañeda", "jorgemen@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 4, 2, 2, 1),
("Adrian", "Salcedo", "Villalobos", "adriansal@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 4, 2, 2, 1);
-- ASIGNACIONES OPERADORES ASESORAMIENTO FINANCIERO
INSERT INTO asignacion_cajas (id_caja, id_empleado) values (11,13), (12,14);

-- CLIENTES
INSERT INTO clientes (nombre, apellido_paterno, apellido_materno, email, telefono, numero_cuenta, activo) VALUES
('Carlos', 'Ramírez', 'López', 'carlos.ramirez@email.com', '8711234567', '012345678901234567890123', 1),
('María', 'González', 'Hernández', 'maria.gonzalez@email.com', '8712345678', '123456789012345678901234', 1),
('José', 'Martínez', 'García', 'jose.martinez@email.com', '8713456789', '234567890123456789012345', 1),
('Ana', 'Torres', 'Sánchez', 'ana.torres@email.com', '8714567890', '345678901234567890123456', 1),
('Luis', 'Flores', 'Morales', 'luis.flores@email.com', '8715678901', '456789012345678901234567', 0),
('Patricia', 'Rodríguez', 'Ruiz', 'patricia.rodriguez@email.com', '8716789012', '567890123456789012345678', 1),
('Miguel', 'Pérez', 'Jiménez', 'miguel.perez@email.com', '8717890123', '678901234567890123456789', 1),
('Laura', 'Gómez', NULL, 'laura.gomez@email.com', '8718901234', '789012345678901234567890', 1),
('Roberto', 'Díaz', 'Castro', 'roberto.diaz@email.com', '8719012345', '890123456789012345678901', 0),
('Sofía', 'Vargas', 'Ortega', 'sofia.vargas@email.com', '8710123456', '901234567890123456789012', 1);

-- Turnos con diferentes estados
INSERT INTO turnos (numero, timestamp_solicitud, timestamp_llamado, timestamp_inicio_atencion, timestamp_fin_atencion, id_caja, id_cliente) VALUES
(1, '2025-10-18 08:15:00', '2025-10-18 08:16:00', '2025-10-18 08:17:00', '2025-10-18 08:25:00', 1, 1),
(2, '2025-10-18 08:20:00', '2025-10-18 08:26:00', '2025-10-18 08:27:00', '2025-10-18 08:35:00', 2, 2),
(3, '2025-10-18 08:25:00', '2025-10-18 08:36:00', '2025-10-18 08:37:00', '2025-10-18 08:39:24', 3, 3),
(4, '2025-10-18 08:30:00', '2025-10-18 08:38:00', '2025-10-18 08:40:00', '2025-10-18 08:41:09', 6, 4),
(5, '2025-10-18 08:35:00', '2025-10-18 08:39:00', '2025-10-18 08:40:57', '2025-10-18 08:45:00', 7, 5),
(1, '2025-10-18 09:00:00', '2025-10-18 09:02:00', '2025-10-18 09:03:00', '2025-10-18 09:15:00', 8, 6),
(2, '2025-10-18 09:10:00', '2025-10-18 09:11:00', '2025-10-18 09:12:00', '2025-10-18 09:13:00', 9, NULL),
(1, '2025-10-18 09:15:00', '2025-10-18 09:16:00', '2025-10-18 09:16:21', '2025-10-18 09:16:59', 10, 7),
(2, '2025-10-18 09:20:00', '2025-10-18 09:25:00', '2025-10-18 09:27:46', '2025-10-18 09:35:28', 11, 8),
(1, '2025-10-18 09:25:00', '2025-10-18 09:27:00', '2025-10-18 09:28:00', '2025-10-18 09:40:00', 12, 9);

-- Log de estados para cada turno
INSERT INTO turnos_log (id_turno, id_estado, timestamp_actualizacion) VALUES
-- Turno 1: Finalizado
(1, 2, '2025-10-18 08:15:00'),
(1, 1, '2025-10-18 08:16:00'),
(1, 3, '2025-10-18 08:17:00'),
(1, 5, '2025-10-18 08:25:00'),

-- Turno 2: Finalizado
(2, 2, '2025-10-18 08:20:00'),
(2, 1, '2025-10-18 08:26:00'),
(2, 3, '2025-10-18 08:27:00'),
(2, 5, '2025-10-18 08:35:00'),

-- Turno 3: En Atención
(3, 2, '2025-10-18 08:25:00'),
(3, 1, '2025-10-18 08:36:00'),
(3, 3, '2025-10-18 08:37:00'),
(3, 5, '2025-10-18 08:39:24'),

-- Turno 4: Llamado
(4, 2, '2025-10-18 08:30:00'),
(4, 1, '2025-10-18 08:38:00'),
(4, 3, '2025-10-18 08:40:00'),
(4, 5, '2025-10-18 08:41:09'),

-- Turno 5: En Espera
(5, 2, '2025-10-18 08:35:00'),
(5, 1, '2025-10-18 08:39:00'),
(5, 3, '2025-10-18 08:40:57'),
(5, 5, '2025-10-18 08:45:00'),

-- Turno 6: Finalizado
(6, 2, '2025-10-18 09:00:00'),
(6, 1, '2025-10-18 09:02:00'),
(6, 3, '2025-10-18 09:03:00'),
(6, 5, '2025-10-18 09:15:00'),

-- Turno 7: En Espera (sin cliente asociado)
(7, 2, '2025-10-18 09:10:00'),
(7, 1, '2025-10-18 09:11:00'),
(7, 3, '2025-10-18 09:12:00'),
(7, 5, '2025-10-18 09:13:00'),

-- Turno 8: Llamado
(8, 2, '2025-10-18 09:15:00'),
(8, 1, '2025-10-18 09:16:00'),
(8, 3, '2025-10-18 09:16:21'),
(8, 5, '2025-10-18 09:16:59'),

-- Turno 9: En Espera
(9, 2, '2025-10-18 09:20:00'),
(9, 1, '2025-10-18 09:25:00'),
(9, 3, '2025-10-18 09:27:46'),
(9, 5, '2025-10-18 09:35:28'),

-- Turno 10: Finalizado
(10, 2, '2025-10-18 09:25:00'),
(10, 1, '2025-10-18 09:27:00'),
(10, 3, '2025-10-18 09:28:00'),
(10, 5, '2025-10-18 09:40:00');
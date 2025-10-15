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

INSERT INTO cajas (numero, id_departamento, id_estado)
VALUES (1, 1, 1), (2, 1, 1), (1, 2, 1);

INSERT INTO turnos (numero, timestamp_solicitud, id_caja)
VALUES (1, "2025-10-09 10:30:00", 1), (2, "2025-10-09 10:50:00", 1), (3, "2025-10-09 11:00:00", 1), (1, "2025-10-09 10:30:00", 3);

INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Arturo", "Avila", "Martinez", "arturoam@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 1, 1, 1),
("Jose Angel", "Santoyo", "Moreno", "josesan@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 1, 1),
("Diego Isaac", "Puentes", "Villa", "diegop@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 2, 2, 1),
("Cesar Eduardo", "Martinez", "Ramos", "cesarmar@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 2, 2, 2, 1);

INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, activo, id_departamento, id_rol, id_tipo_turno, id_horario)
VALUES ("Hector", "Sanchez", "Tamayo", "hectorsan@celeris.com", "$2y$10$zz9T9rzxbp1cf5hrsLWi5OFe9wNnQKaqfov0iluL0IeAJMXyiFsAa", 1, 1, 1, 1, 1);

INSERT INTO turnos (numero, timestamp_solicitud, id_caja)
VALUES (1, "2025-10-10 20:20:10", 1), (1, "2025-08-12 12:12:12", 2);

INSERT INTO turnos_log (id_turno, id_estado, timestamp_actualizacion)
VALUES (1, 3, "2025-10-09 10:40:00"), (1, 5, "2025-10-09 10:50:00"), (2, 3, "2025-10-09 11:20:00"), (2, 5, "2025-10-09 11:40:00");

insert into asignacion_cajas (id_caja, id_empleado) values (1,2), (2,3), (3,4);

-- TRAER EL REGISTRO MAS RECIENTE DE TODOS LOS TURNOS DENTRO DE TURNOS_LOG ORDENADOS POR MAS RECIENTE
SELECT t.id, t.numero, t.id_caja, tl.id_estado, tl.timestamp_actualizacion 
FROM turnos t, turnos_log tl 
WHERE t.id = tl.id_turno 
AND tl.id = (SELECT MAX(id) from turnos_log WHERE id_turno = t.id) 
ORDER BY tl.timestamp_actualizacion DESC;

-- TRAER TODOS LOS REGISTROS DE TURNOS DENTRO DE TURNOS_LOG ORDENADOS POR MAS RECIENTE
SELECT t.id, t.numero, t.id_caja, tl.id_estado, tl.timestamp_actualizacion 
FROM turnos t, turnos_log tl 
WHERE t.id = tl.id_turno 
ORDER BY tl.timestamp_actualizacion DESC;

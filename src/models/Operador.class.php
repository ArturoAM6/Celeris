<?php

// Clase Operador, hereda de clase Empleado
class Operador extends Empleado {
    // Propiedades
    public const ID_ROL = 2;

    // Constructor
    public function __construct(string $nombre, string $apellido_paterno, ?string $apellido_materno, string $email, string $password_hash, int $id_departamento, int $id_tipo_turno) {
        parent::__construct($nombre, $apellido_paterno, $apellido_materno, $email, $password_hash, $id_departamento, $id_tipo_turno);
    }

    // Getters & Setters - Metodos abstractos heredados dejan de ser abstractos y se agrega funcionalidad.
    // Metodo publico que devuelve el ID del rol. Devuelve un Int.
    public function getRol(): int {
        return self::ID_ROL;
    }

    // Metodos
    public function atenderTurno() {
        // Falta implementacion
    }

    public function llamarTurno() {
        // Falta implementacion
    }

    public function finalizarTurno() {
        // Falta implementacion
    }
}
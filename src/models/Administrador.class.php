<?php

// Clase Administrador, hereda de clase Empleado
class Administrador extends Empleado {
    // Propiedades
    public const ID_ROL = 1;
    
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
    public function altaEmpleado() {
        // Falta implementacion
    }

    public function bajaEmpleado() {
        // Falta implementacion
    }

    public function modificarEmpleado() {
        // Falta implementacion
    }

    public function desplegarEmpleados() {
        // Falta implementacion
    }

    public function gestionarTipoTurnos() {
        // Falta implementacion
    }

    public function gestionarCajas() {
        // Falta implementacion
    }

    public function desplegarDepartamentos() {
        // Falta implementacion
    }

    public function desplegarTurnos() {
        // Falta implementacion
    }
}
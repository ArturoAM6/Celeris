<?php

// Clase abstracta Empleado, hereda de clase Persona
abstract class Empleado extends Persona {
    // Propiedades
    protected string $password_hash;
    protected int $id_departamento;
    protected int $id_tipo_turno;
    protected int $id_horario;
    protected int $activo;
    
    // Constructor
    public function __construct(string $nombre, string $apellido_paterno, ?string $apellido_materno, string $email, string $password_hash, int $id_departamento, int $id_tipo_turno) {
        parent::__construct($nombre, $apellido_paterno, $apellido_materno, $email);
        $this->password_hash = $password_hash;
        $this->id_departamento = $id_departamento;
        $this->id_tipo_turno = $id_tipo_turno;
        $this->id_horario = 1;
        $this->activo = 1;
    }

    // Getters & Setters
    abstract function getRol(): int;

    // Metodo publico que devuelve la contraseña hasheada. Devuelve un string.
    public function getPasswordHash(): string {
        return $this->password_hash;
    }

    // Metodo publico que devuelve el ID del departamento. Devuelve un Int.
    public function getDepartamento(): int {
        return $this->id_departamento;
    }

    // Metodo publico que devuelve el ID del tipo de turno. Devuelve un Int.
    public function getTipoTurno(): int {
        return $this->id_tipo_turno;
    }

    // Metodo publico que devuelve el ID del horario. Devuelve un Int.
    public function getHorario(): int {
        return $this->id_horario;
    }

    // Metodo publico que devuelve si un empleado sigue activo. Devuelve un Int.
    public function getStatus(): int {
        return $this->activo;
    }
}
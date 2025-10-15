<?php

// Clase abstracta Persona (No es posible crear instancias/objetos de una clase abstracta. Sirve como una plantilla para las clases hijas).
abstract class Persona {
    // Propiedades
    protected ?int $id = null;
    protected string $nombre;
    protected string $apellido_paterno;
    protected ?string $apellido_materno; // Propiedad protegida de tipo string, valor por defecto Null y puede ser nula.
    protected string $email;

    // Constructor
    public function __construct(string $nombre, string $apellido_paterno, ?string $apellido_materno, string $email) {
        $this -> nombre = $nombre;
        $this -> apellido_paterno = $apellido_paterno;
        $this -> apellido_materno = $apellido_materno;
        $this -> email = $email;
    }

    // Getters & Setters 
    
    // Metodo publico que devuelve el ID. Devuelve un Int.
    public function getId(): ?int { 
        return $this->id; 
    }

    // Metodo publico que establece el ID a un valor determinado. No devuelve ningun valor (void).
    public function setId(int $id): void {
        $this->id = $id;
    }

    // Metodo publico que devuelve el nombre. Devuelve un string.
    public function getNombre(): string {
        return $this->nombre;
    }

    // Metodo publico que devuelve el apellido paterno. Devuelve un string.
    public function getApellidoPaterno(): string {
        return $this->apellido_paterno;
    }

    // Metodo publico que devuelve el apellido materno. Devuelve un string.
    public function getApellidoMaterno(): string {
        return $this->apellido_materno;
    }

    // Metodo publico que devuelve el email. Devuelve un string.
    public function getEmail(): string {
        return $this->email;
    }

    // Metodo publico que devuelve el nombre completo (nombre, apellido paterno y apellido materno en caso de no ser null). Devuelve un string.
    public function getNombreCompleto(): string {
        if ($this->apellido_materno == null) {
            return $this->nombre . " " . $this->apellido_paterno;
        }
        return $this->nombre . " " . $this->apellido_paterno . " " . $this->apellido_materno;
    }
}
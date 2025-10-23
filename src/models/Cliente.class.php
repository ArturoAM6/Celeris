<?php

// Clase Cliente, hereda de clase Persona
class Cliente extends Persona {
    // Propiedades
    private string $numero_cuenta;
    private string $telefono;
    
    // Constructor
    public function __construct(string $nombre, string $apellido_paterno, ?string $apellido_materno, string $email, string $numero_cuenta, string $telefono) {
        parent::__construct($nombre, $apellido_paterno, $apellido_materno, $email);
        $this->numero_cuenta = $numero_cuenta;
        $this->telefono = $telefono;
    }

    // Getters & Setters
    
    // Metodo publico que devuelve el numero de cuenta. Devuelve un string.
    public function getNumeroCuenta(): string {
        return $this->numero_cuenta;
    }

    // Metodo publico que devuelve el numero de telefono. Devuelve un string.
    public function getTelefono(): string {
        return $this->telefono;
    }
}
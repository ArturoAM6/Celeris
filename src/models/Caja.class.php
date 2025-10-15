<?php

class Caja {
    // Propiedades
    private ?int $id = null;
    private int $numero;
    private int $id_departamento;
    private int $id_estado;

    // Constructor
    public function __construct(int $numero, int $id_departamento, int $id_estado) {
        $this->numero = $numero;
        $this->id_departamento = $id_departamento;
        $this->id_estado = $id_estado;
    }

    // Getters & Setters
    // Metodo publico que devuelve el ID. Devuelve un Int.
    public function getId(): ?int {
        return $this->id;
    }

    // Metodo publico que establece el ID a un valor determinado. No devuelve ningun valor (void).
    public function setId($id): void {
        $this->id = $id;
    }

    // Metodo publico que devuelve el numero de la caja. Devuelve un Int.
    public function getNumero(): int {
        return $this->numero;
    }

    // Metodo publico que devuelve el ID del departamento de la caja. Devuelve un Int.
    public function getDepartamento(): int {
        return $this->id_departamento;
    }

    // Metodo publico que devuelve el ID del estado de la caja. Devuelve un Int.
    public function getEstado(): int {
        return $this->id_estado;
    }

    public function setEstado(int $id): void {
        $this->id_estado = $id;
    }
}


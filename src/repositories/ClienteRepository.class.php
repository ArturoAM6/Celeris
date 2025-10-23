<?php

class ClienteRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function buscarPorNumeroCuenta(string $numeroCuenta): ?Cliente { 
        $stmt = $this->conexion->prepare("SELECT * FROM clientes WHERE numero_cuenta = :numeroCuenta");
        $stmt->execute([':numeroCuenta' => $numeroCuenta]);
        $data = $stmt->fetch();

        return $data ? $this->crearClienteDesdeArray($data) : null;
    }

    public function buscarPorId(int $id): ?Cliente { 
        $stmt = $this->conexion->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        return $data ? $this->crearClienteDesdeArray($data) : null;
    }

    private function crearClienteDesdeArray(array $data): Cliente {
            $cliente = new Cliente(
            $data['nombre'], 
            $data['apellido_paterno'], 
            $data['apellido_materno'], 
            $data['email'], 
            $data['numero_cuenta'], 
            $data['telefono']
        );
        $cliente->setId($data['id']);

        return $cliente;
    }
}
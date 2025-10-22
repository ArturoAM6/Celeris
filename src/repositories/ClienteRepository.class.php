<?php

class ClienteRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function buscarPorNumeroCuenta(string $numero_cuenta): ?Cliente { 
        $stmt = $this->conexion->prepare("SELECT * FROM clientes WHERE numero_cuenta = :numero_cuenta");
        $stmt->execute([':numero_cuenta' => $numero_cuenta]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearClienteDesdeArray($data);
    }

    public function buscarPorId(int $id): ?Cliente { 
        $stmt = $this->conexion->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearClienteDesdeArray($data);
    }

    private function crearClienteDesdeArray(array $data): Cliente {
        try {
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
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}
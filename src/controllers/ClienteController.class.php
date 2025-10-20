<?php

class ClienteController {
    private ClienteRepository $clienteRepository;

    public function __construct() {
        $this->clienteRepository = new ClienteRepository();
    }

    public function obtenerClientePorId(int $id): ?Cliente {
        try {
            $cliente = $this->clienteRepository->buscarPorId($id);
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
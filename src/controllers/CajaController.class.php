<?php

class CajaController {
    private ServicioCajas $servicioCajas;

    public function __construct() {
        $this->servicioCajas = new ServicioCajas();
    }

    // ============ VISTAS ADMINISTRADOR ============

    public function listarTodo(): ?array {
        try {
            $cajas = $this->servicioCajas->obtenerTodos();
            return $cajas;
        } catch (Exception $th) {
            $this->manejarError($e->getMessage());
        }
    }

    public function cambiarEstado(string $view): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $id = $_POST["id"];
                $idEstado = $_POST["id_estado"];

                if (empty($id) || empty($idEstado)) {
                    throw new Exception("Datos insuficientes");
                }

                if (!$this->servicioCajas->cambiarEstado($_POST)) {
                    throw new Exception("Algo salió mal durante el cambio de estado");
                }

                header("Location: " . BASE_URL . "/$view" . "?mensaje=actualizado");
                exit;
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "/$view" . "?error=" . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function mostrarPorId(int $id): ?Caja{
        try {
            $caja = $this->servicioCajas->obtenerPorId($id);
            return $caja;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function editarAsignacion(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $idCaja = $_POST["id_caja"];
                $idEmpleado = $_POST["id_empleado"];
                
                if (empty($idCaja) || empty($idEmpleado)) {
                    throw new Exception("Datos insuficientes");
                }

                if (!$this->servicioCajas->editarAsignacion($_POST)) {
                    throw new Exception("Algo salió mal durante la asignacion");
                }

                header("Location: " . BASE_URL . "/admin?mensaje=actualizado");
                exit;
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "/admin?error=" . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function obtenerCajaPorEmpleado(Empleado $empleado): ?Caja {
        try {
            $caja = $this->servicioCajas->obtenerCajaPorEmpleado($empleado);
            return $caja;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function gestion(): array {
        try {
            $pagina = isset($_GET['pagina_Caja']) ? (int)$_GET['pagina_Caja'] : 1;
            $porPagina = 10;

            return $this->servicioCajas->gestion($pagina, $porPagina);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}

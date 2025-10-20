<?php

class CajaController {
    private CajaRepository $cajaRepository;

    public function __construct() {
        $this->cajaRepository = new CajaRepository();
    }

    // ============ VISTAS ADMINISTRADOR ============

    public function listarCajas(): ?array {
        try {
            $cajas = $this->cajaRepository->todos();
            return $cajas;
        } catch (Exception $th) {
            $this->manejarError($e->getMessage());
        }
    }
    
    public function obtenerCajaEmpleado(int $id_caja): ?int {
        try {
            $empleado = $this->cajaRepository->getCajaEmpleado($id_caja);
            return $empleado;
        } catch (Exception $th) {
            $this->manejarError($e->getMessage());
        }
    }

    public function asignarEmpleadoCaja(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $numero = $_POST['numero'];
                $id_departamento = $_POST['id_departamento'];
                $id_empleado = $_POST['id_empleado'];
                $id_estado = $_POST['id_estado'];
    
                if (empty($id_departamento) || empty($id_empleado) || empty($id_estado)) {
                    throw new Exception("Los campos con * son obligatorios");
                }
    
                $caja = $this->cajaRepository->obtenerCajaPorId($id);
    
                if (!$caja) {
                    throw new Exception("Caja no encontrada.");
                }
    
                $cajaActualizada = $this->instanciarCaja($_POST);
                $cajaActualizada->setId($id);
                
                $this->cajaRepository->actualizar($cajaActualizada);
                    
                header('Location: ' . BASE_URL . '/admin?mensaje=asignado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function obtenerCajaPorId(int $id_caja): ?Caja{
        $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
        return $caja;
    }

    public function abrirCaja(): void {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];

                $caja = $this->cajaRepository->obtenerCajaPorId($id);

                if (!$caja) {
                    throw new Exception("La caja no existe.");
                }

                $this->cajaRepository->cambiarEstado($id, 1);
                header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function cerrarCaja(): void {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];

                $caja = $this->cajaRepository->obtenerCajaPorId($id);

                if (!$caja) {
                    throw new Exception("La caja no existe.");
                }

                $this->cajaRepository->cambiarEstado($id, 2);
                header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function pausarCaja(): void {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];

                $caja = $this->cajaRepository->obtenerCajaPorId($id);

                if (!$caja) {
                    throw new Exception("La caja no existe.");
                }

                $this->cajaRepository->cambiarEstado($id, 3);
                header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function fueraServicioCaja(): void {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];

                $caja = $this->cajaRepository->obtenerCajaPorId($id);

                if (!$caja) {
                    throw new Exception("La caja no existe.");
                }

                $this->cajaRepository->cambiarEstado($id, 4);
                header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    private function instanciarCaja(array $data): Caja {
        $this->cajaRepository->asignarCaja($data['id'], $data['id_empleado']);

        return new Caja(
            $data['numero'],
            $data['id_departamento'],
            $data['id_estado']
        );
    }


    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

}
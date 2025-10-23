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
    
    // public function obtenerCajaEmpleado(int $id_caja): ?int {
    //     try {
    //         $empleado = $this->cajaRepository->buscarCajaEmpleado($id_caja);
    //         return $empleado;
    //     } catch (Exception $th) {
    //         $this->manejarError($e->getMessage());
    //     }
    // }

    // public function asignarEmpleadoCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $id = $_POST['id'];
    //             $numero = $_POST['numero'];
    //             $id_departamento = $_POST['id_departamento'];
    //             $id_empleado = $_POST['id_empleado'];
    //             $id_estado = $_POST['id_estado'];
    
    //             if (empty($id_departamento) || empty($id_empleado) || empty($id_estado)) {
    //                 throw new Exception("Los campos con * son obligatorios");
    //             }
    
    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);
    
    //             if (!$caja) {
    //                 throw new Exception("Caja no encontrada.");
    //             }
    
    //             $cajaActualizada = $this->instanciarCaja($_POST);
    //             $cajaActualizada->setId($id);
                
    //             $this->cajaRepository->actualizar($cajaActualizada);
                    
    //             header('Location: ' . BASE_URL . '/admin?mensaje=asignado');
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

    // public function abrirCaja(): void {

    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         try {
    //             $id = $_POST['id'];

    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);

    //             if (!$caja) {
    //                 throw new Exception("La caja no existe.");
    //             }

    //             $this->cajaRepository->cambiarEstado($id, 1);
    //             header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

    // public function cerrarCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         try {
    //             $id = $_POST['id'];

    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);

    //             if (!$caja) {
    //                 throw new Exception("La caja no existe.");
    //             }

    //             $this->cajaRepository->cambiarEstado($id, 2);
    //             header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

    // //OPERADOR CAMBIO -pendiente de ver si se puede-
    // public function pausarCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         try {
    //             // Aceptamos ambos nombres de campo para compatibilidad
    //             $id = $_POST['id'] ?? $_POST['id_caja'] ?? null;

    //             if (!$id) {
    //                 throw new Exception("ID de caja no proporcionado.");
    //             }

    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);

    //             if (!$caja) {
    //                 throw new Exception("La caja no existe.");
    //             }

    //             // VALIDACION ADICIONAL: No permitir pausar si existe un turno llamado o en atención
    //             $turnoRepo = new TurnoRepository();
    //             $turnoLlamado = $turnoRepo->obtenerTurnoLlamadoPorCaja($id);
    //             $turnosAtencion = $turnoRepo->obtenerTurnosEnAtencion(); // devuelve todos, filtramos por caja

    //             // comprobar si hay turno en atencion para esta caja
    //             $hayAtencionEnCaja = false;
    //             foreach ($turnosAtencion as $t) {
    //                 if ((int)$t->getCaja() === (int)$id) {
    //                     $hayAtencionEnCaja = true;
    //                     break;
    //                 }
    //             }

    //             if ($turnoLlamado || $hayAtencionEnCaja) {
    //                 header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No puede entrar en descanso: existe un turno llamado o en atención.'));
    //                 exit;
    //             }

    //             $this->cajaRepository->cambiarEstado($id, 3);
    //             header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Caja en descanso.'));
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

    // //OPERADOR CAMBIO -pendiente de ver si se puede-
    // public function reanudarCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         try {
    //             $id = $_POST['id'] ?? $_POST['id_caja'] ?? null;
    //             if (!$id) {
    //                 throw new Exception("ID de caja no proporcionado.");
    //             }
    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);
    //             if (!$caja) {
    //                 throw new Exception("La caja no existe.");
    //             }
    //             $this->cajaRepository->cambiarEstado($id, 1);
    //             header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Caja reabierta.'));
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

    // public function fueraServicioCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         try {
    //             $id = $_POST['id'];

    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);

    //             if (!$caja) {
    //                 throw new Exception("La caja no existe.");
    //             }

    //             $this->cajaRepository->cambiarEstado($id, 4);
    //             header('Location: ' . BASE_URL . '/admin?mensaje=cambio_exitoso');
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }

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

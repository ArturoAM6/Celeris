<?php

class ServicioCajas {
    private CajaRepository $cajaRepository;
    private AsignacionCajaRepository $asignacionCajaRepository;
    private EmpleadoRepository $empleadoRepository;

    public function __construct() {
        $this->cajaRepository = new CajaRepository();
        $this->asignacionCajaRepository = new AsignacionCajaRepository();
        $this->empleadoRepository = new EmpleadoRepository();
    }

    public function obtenerTodos(): ?array {
        $cajas = $this->cajaRepository->todos();
        return $cajas;
    }

    public function cambiarEstado(array $data): bool {
        $caja = $this->cajaRepository->buscarPorId($data["id"]);

        if (!$caja) {
            return false;
        }
        $caja->setEstado($data["id_estado"]);
        return $this->cajaRepository->actualizarEstado($caja);
    }

    public function obtenerPorId(int $id): ?Caja{
        $caja = $this->cajaRepository->buscarPorId($id);
        return $caja;
    }

    public function editarAsignacion(array $data): bool {
        $caja = $this->cajaRepository->buscarPorId($data["id_caja"]);
        $empleado = $this->empleadoRepository->buscarPorId($data["id_empleado"]);

        if (!$caja || !$empleado) {
            return false;
        }

        return $this->asignacionCajaRepository->actualizarAsignacion($caja, $empleado);
    }

    // public function ObtenerEmpleadoCaja(): ?Caja{
    //     try {
    //         if ($_SESSION["id_empleado"]) {
    //             $id_caja = $this->cajaRepository->getNumeroCaja($_SESSION["id_empleado"]);
    //             if (!$id_caja) {
    //                 return null;
    //             }
    //             $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
    //             return $caja;
    //         }
    //         return null;
    //     } catch (Exception $e) {
    //         $this->manejarError($e->getMessage());
    //     }
    // }

    // public function abrir(int $id): ?Caja {
    //     $caja = $this->cajaRepository->buscarPorId($id);

    //     if (!$caja) {
    //         return null;
    //     }

    //     $this->cajaRepository->cambiarEstado($id, 1);
    //     return $caja;
    // }

    // public function cerrar(): void {
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

    public function obtenerCajaPorEmpleado(Empleado $empleado): ?Caja {
        $caja = $this->asignacionCajaRepository->buscarCajaPorEmpleado($empleado);
        
        if (!$caja) {
            return null;
        }

        return $this->instanciarDesdeArray($caja);;
    }

    public function gestion(int $pagina, int $porPagina): array {
        try {
            $cajas = $this->cajaRepository->obtenerPaginacion($pagina, $porPagina);
            $totalCajas = $this->cajaRepository->contar();
            $totalPaginasCajas = ceil($totalCajas / $porPagina);

            return [
                'cajas' => $cajas,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginasCajas,
                'totalCajas' => $totalCajas
            ];
        } catch (Exception $e) {
            return [
                'cajas' => [],
                'paginaActual' => 1,
                'totalPaginas' => 0,
                'totalCajas' => 0
            ];
        }
    }
    
    private function instanciarDesdeArray(array $data): Caja {
        $caja = new Caja(
            $data['numero'],
            $data['id_departamento'],
            $data['id_estado']
        );

        $caja->setId($data["id"]);
        return $caja;
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}
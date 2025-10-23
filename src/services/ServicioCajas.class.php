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
<?php

class TurnoController {
    private TurnoRepository $turnoRepository;
    private CajaRepository $cajaRepository;
    private ClienteRepository $clienteRepository;
    private ServicioTurnos $servicioTurnos;

    public function __construct() {
        $this->turnoRepository = new TurnoRepository();
        $this->cajaRepository = new CajaRepository();
        $this->clienteRepository = new ClienteRepository();
        $this->servicioTurnos = new ServicioTurnos;
    }

    // ============ VISTAS PÚBLICAS ============
    public function obtenerTurnoPorId(int $id): ?Turno {
        try {
            $turno = $this->turnoRepository->buscarPorId($id);
            return $turno;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }

    }

    public function generarTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $departamento = $_POST['departamento'];
                $numeroCuenta = $_POST['numero_cuenta'] ?? '';
                $cliente = null;
                
                if (!empty($numeroCuenta)) {
                    $cliente = $this->clienteRepository->buscarPorNumeroCuenta($numeroCuenta);
                    
                    if (!$cliente) {
                        throw new Exception("Cliente no encontrado");
                    }
                }
                                
                // Obtener siguiente número de turno
                $numeroTurno = $this->turnoRepository->obtenerSiguienteNumero();
                
                // Asignar caja disponible
                $caja = $this->cajaRepository->obtenerCajaDisponible();
                
                if (!$caja) {
                    throw new Exception("No hay cajas disponibles");
                }
                
                // Crear turno
                $turno = new Turno(
                    $numeroTurno,
                    date('Y-m-d H:i:s'),
                    (!$cliente) ? null : $cliente->getId(),
                    $caja->getId()
                );
                
                // Guardar turno
                $this->turnoRepository->guardar($turno);
                $this->turnoRepository->guardarEnLog($turno->getId(), 2, date('Y-m-d H:i:s'));
                
                // Imprimir ticket
                $this->imprimirTurno($cliente, $caja, $turno, $departamento);

                // Mostrar ticket
                $turnoGenerado = $turno;
                header('Location: ' . BASE_URL . '/turno/ticket?id=' . $turno->getId());
                exit;

            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/publicas/generar-turno.php';
            }
        } else {
            require_once __DIR__ . '/../views/publicas/generar-turno.php';
        }
    }

    private function imprimirTurno(?Cliente $cliente, Caja $caja, Turno $turno, string $departamento): void {
        $this->servicioTurnos->generarTurnoPdf(
            (!$cliente) ? "N/A" : $cliente->getNombreCompleto(),
            $caja->getId(),
            $departamento,
            $turno->getId());
    }

    public function consultarTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $numeroTurno = $_POST['numero_turno'] ?? '';
                
                if (empty($numeroTurno)) {
                    throw new Exception("Número de turno es obligatorio");
                }
                
                $turno = $this->turnoRepository->buscarPorNumero($numeroTurno);
                
                if (!$turno) {
                    throw new Exception("Turno no encontrado");
                }
                
                $estadoActual = $this->turnoRepository->obtenerEstadoActual($turno->getId());
                $caja = $this->cajaRepository->obtenerCajaPorId($turno->getCajaId());
                
                require_once __DIR__ . '/../views/publicas/consultar-turno.php';
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/publicas/consultar-turno.php';
            }
        } else {
            require_once __DIR__ . '/../views/publicas/consultar-turno.php';
        }
    }

    public function pantallaTurnos(): void {
        $turnosEnEspera = $this->turnoRepository->turnosEnEspera();
        $turnosEnAtencion = $this->turnoRepository->turnosEnAtencion();
        
        require_once __DIR__ . '/../views/publicas/pantalla-turnos.php';
    }

    public function listarTurnosPorDepartamento(int $id_departamento): void {
        try {
            $turnos = $this->turnoRepository->turnosPorDepartamento($id_departamento);
            include __DIR__ . '/../views/publicas/pantalla-turnos.php';
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    // ============ VISTAS INTERNAS ============
    public function listarTurnos(): ?array {
        try {
            $turnos = $this->turnoRepository->todos();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosActivos(): ?array {
        try {
            $turnos = $this->turnoRepository->obtenerTurnosActivos();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function obtenerDepartamentoTurno(int $id_caja): string {
        try {
            $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
            $id_departamento = $caja->getDepartamento();
            switch ($id_departamento) {
                case 1:
                    return 'Cajas';
                case 2:
                    return 'Asociados';
                case 3:
                    return 'Caja Fuerte';
                case 4:
                    return 'Ventanillas';
                default:
                    throw new Exception('Departamento no existente');
            }
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    // ============ MANEJAR ERRORES ============
    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

}
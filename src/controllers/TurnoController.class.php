<?php

class TurnoController {
    private TurnoRepository $turnoRepository;
    // private CajaRepository $cajaRepository;
    private ClienteRepository $clienteRepository;

    public function __construct() {
        $this->turnoRepository = new TurnoRepository();
        // $this->cajaRepository = new CajaRepository();
        $this->clienteRepository = new ClienteRepository();
    }

    // ============ VISTAS PÚBLICAS ============
    
    public function generarTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // $numeroCuenta = $_POST['numero_cuenta'] ?? '';
                
                // if (empty($numeroCuenta)) {
                //     throw new Exception("Número de cuenta es obligatorio");
                // }
                
                // $cliente = $this->clienteRepository->buscarPorNumeroCuenta($numeroCuenta);
                
                // if (!$cliente) {
                //     throw new Exception("Cliente no encontrado");
                // }
                
                // Obtener siguiente número de turno
                $numeroTurno = $this->turnoRepository->obtenerSiguienteNumero();
                
                // Asignar caja disponible
                // $cajaId = $this->obtenerCajaDisponible();
                
                // if (!$cajaId) {
                //     throw new Exception("No hay cajas disponibles");
                // }

                $cliente = null;
                $caja = 1;
                
                // Crear turno
                $turno = new Turno(
                    $numeroTurno,
                    date('Y-m-d H:i:s'),
                    (!$cliente) ? null : $cliente->getId(),
                    $caja
                );
                
                $this->turnoRepository->guardar($turno);
                
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

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

}
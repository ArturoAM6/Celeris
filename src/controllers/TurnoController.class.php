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

            if (isset($_POST['numero_cuenta']) && !isset($_POST['id_departamento'])) {
                $numeroCuenta = $_POST['numero_cuenta'];
                
                try {
                    $cliente = $this->clienteRepository->buscarPorNumeroCuenta($numeroCuenta);
                    if (!$cliente) {
                        throw new Exception("Cliente no encontrado");
                    }
                        
                    require_once __DIR__ . '/../views/publicas/generar-turno.php';
                    return;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    $this->manejarError($e->getMessage());
                    header('Location: ' . BASE_URL . '/');
                    exit;
                }
            }
                
            if (isset($_POST['id_departamento'])) {
                try {
                    $turno = null;
                    $id_departamento = $_POST['id_departamento'];
                    $numeroCuenta = $_POST['numero_cuenta'] ?? '';
                    $cliente = null;
                        
                    if (!empty($numeroCuenta)) {
                        $cliente = $this->clienteRepository->buscarPorNumeroCuenta($numeroCuenta);
                        
                        if (!$cliente) {
                            throw new Exception("Cliente no encontrado");
                        }
                    }
                        
                    // Asignar caja disponible
                    $caja = $this->cajaRepository->obtenerCajaDisponible($id_departamento);
                    // Obtener siguiente número de turno
                    $numeroTurno = $this->turnoRepository->obtenerSiguienteNumero($id_departamento);
                    
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
                    
                    // Mostrar ticket
                    $turnoGenerado = $turno;
                    header('Location: ' . BASE_URL . '/turno/ticket?id=' . $turno->getId());
                    exit;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    $this->manejarError($e->getMessage());
                    require_once __DIR__ . '/../views/publicas/generar-turno.php';
                }
            }   
            
        } else {
            require_once __DIR__ . '/../views/publicas/generar-turno.php';
        }
    }

    public function obtenerTiempoEspera(): void {
        header('Content-Type: application/json');
        
        if (!isset($_GET['id'])) {
            throw new Exception("ID requerido");
        }
        
        try {
            $idTurno = (int)$_GET['id'];
            $resultado = $this->turnoRepository->obtenerTiempoEspera($idTurno);
            
            if (isset($resultado['error'])) {
                throw new Exception("Tiempo de espera no disponible");
            }

            $resultado['timestamp_servidor'] = time();
            
            echo json_encode($resultado);
            exit;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
            exit;
        }
    }

    //--IniOperador--

    public function obtenerNumeroCajaActiva(int $id_caja): ?Turno {
        try {
            $turno = $this->turnoRepository->obtenerTurnoActivoPorCaja($id_caja);
            return $turno;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    //FinOperador--

    public function imprimirTurno(?Cliente $cliente, int $id_caja, Turno $turno): void {
        $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
        switch ($caja->getDepartamento()) {
            case 1:
                $departamento = 'Cajas';
                break;
            case 2:
                $departamento = 'Asociados';
                break;
            case 3:
                $departamento = 'Caja Fuerte';
                break;
            case 4:
                $departamento = 'Asesoramiento Financiero';
                break;
            default:
                throw new Exception("No existe el departamento.");
                break;
        }
        $this->servicioTurnos->generarTurnoPdf(
            (!$cliente) ? "N/A" : $cliente->getNombreCompleto(),
            $caja->getNumero(),
            $departamento,
            $turno->getNumero());
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

    public function listarTurnosEnEspera(): ?array {
        try {
            $turnos = $this->turnoRepository->obtenerTurnosEnEspera();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosEnAtencion(): ?array {
        try {
            $turnos = $this->turnoRepository->obtenerTurnosEnAtencion();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosCompletados(): ?array {
        try {
            $turnos = $this->turnoRepository->obtenerTurnosCompletados();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function gestionDeTurnos(): array {
    try {
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $porPagina = 10;

        // Obtener filtros simples desde GET
        $filtros = [
            'id_departamento' => $_GET['departamento'] ?? '',
            'id_caja' => $_GET['caja'] ?? '',
            'fecha' => $_GET['fecha'] ?? '',
            'numero_turno' => $_GET['numero'] ?? ''
        ];

        // IMPORTANTE: Usar los nuevos métodos con filtros
        $turnos = $this->turnoRepository->obtenerTurnosPaginadosConFiltros($pagina, $porPagina, $filtros);
        $totalTurnos = $this->turnoRepository->contarTurnosConFiltros($filtros);
        $totalPaginas = ceil($totalTurnos / $porPagina);

        return [
            'turnos' => $turnos,
            'paginaActual' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalTurnos' => $totalTurnos,
            'filtros' => $filtros
        ];
    } catch (Exception $e) {
        error_log("Error en gestionDeTurnos: " . $e->getMessage());
        return [
            'turnos' => [],
            'paginaActual' => 1,
            'totalPaginas' => 0,
            'totalTurnos' => 0,
            'filtros' => []
        ];
    }
}




    

    public function obtenerDepartamentoTurno(int $id_caja): string {
        try {
            $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
            $id_departamento = $caja->getDepartamento();
            switch ($id_departamento) {
                case 1:
                    return 'Ventanillas';
                case 2:
                    return 'Asociados';
                case 3:
                    return 'Caja Fuerte';
                case 4:
                    return 'Asesoramiento Financiero';
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
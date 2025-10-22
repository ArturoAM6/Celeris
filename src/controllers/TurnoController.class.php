<?php

class TurnoController {
    private TurnoRepository $turnoRepository;
    private CajaRepository $cajaRepository;
    private ClienteRepository $clienteRepository;
    private ServicioTurnos $servicioTurnos;
    private EmpleadoRepository $repositorioOperadores;

    public function __construct() {
        $this->turnoRepository = new TurnoRepository();
        $this->cajaRepository = new CajaRepository();
        $this->clienteRepository = new ClienteRepository();
        $this->servicioTurnos = new ServicioTurnos;
        $this->repositorioOperadores = new EmpleadoRepository();
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

    //llamar
    public function llamarTurnoCaja(int $id_caja): ?Turno {
        try {
            $turno = $this->turnoRepository->obtenerTurnoLlamadoPorCaja($id_caja);
            if (!$turno) {
                return null;
            } else {
                $turno->setTimestampLlamado(date('Y-m-d H:i:s'));
                $turno->setEstado(1);
            }
            return $turno;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function obtenerNumeroEsperaCaja(int $id_caja): ?array {
        try {
            $turnos = $this->turnoRepository->obtenerTurnoEsperaPorCaja($id_caja);
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }


    //Nuevo llamarUnTurno
    public function llamarUnTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Obtener la caja del empleado actual (como hace la vista)
                $empleadoController = new EmpleadoController();
                $caja = $empleadoController->ObtenerEmpleadoCaja();
                if (!$caja) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No se pudo determinar la caja'));
                    exit;
                }

                $idCaja = $caja->getId();

                // 1) Verificar que NO exista ya un turno llamado o en atención para esta caja
                $turnoLlamado = $this->turnoRepository->obtenerTurnoLlamadoPorCaja($idCaja);
                $turnoAtencion = $this->turnoRepository->obtenerTurnoEnAtencionPorCaja($idCaja) ?? $this->obtenerNumeroCajaActiva($idCaja);

                if ($turnoLlamado || $turnoAtencion) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('Ya existe un turno llamado o en atención.'));
                    exit;
                }

                // 2) Obtener la cola de espera y seleccionar el primer turno (FIFO)
                $turnosEspera = $this->turnoRepository->obtenerTurnoEsperaPorCaja($idCaja); // devuelve array ordenado asc
                if (empty($turnosEspera)) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No hay turnos en espera.'));
                    exit;
                }

                $primerTurno = $turnosEspera[0];
                $this->turnoRepository->cambiarEstado($primerTurno->getId(), 1); // marcar como Llamado (1)

                header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Turno llamado'));
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }


    public function empezarUnTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_turno = $_POST["id_turno"];
                $id_estado_turno = $_POST["id_estado_turno"]; // se espera 3

                // Obtener caja del empleado actual
                $empleadoController = new EmpleadoController();
                $caja = $empleadoController->ObtenerEmpleadoCaja();
                if (!$caja) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No se pudo determinar la caja.'));
                    exit;
                }
                $idCaja = $caja->getId();

                // Obtener el turno por id
                $turno_llamado = $this->turnoRepository->buscarPorId($id_turno);
                if (!$turno_llamado) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('Turno no encontrado.'));
                    exit;
                }

                // Verificar que el turno pertenezca a esta caja (normalizando tipo)
                if ((int)$turno_llamado->getCaja() !== (int)$idCaja) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('El turno no pertenece a su caja.'));
                    exit;
                }

                // Verificar que el estado actual sea '1' (Llamado)
                $estadoActual = $this->turnoRepository->obtenerEstadoActual($id_turno);
                if ((int)$estadoActual !== 1) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('El turno no está en estado Llamado.' . $estadoActual));
                    exit;
                }


                // Cambiar a En atención (3)
                $this->turnoRepository->cambiarEstado($turno_llamado->getId(), 3);
                header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Turno en atención.'));
                exit;
                
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                exit;
            }

            // try {
                //     $id_turno = $_POST["id_turno"];
                //     $id_estado_turno = $_POST["id_estado_turno"];
                //     $this->turnoRepository->cambiarEstado($id_turno, $id_estado_turno);
                //     header('Location: ' . BASE_URL . '/operador?mensaje=funciono');
                //     exit;
                // } catch (Exception $e) {
                //     header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                //     exit;
                // }
        }
    }

    public function finalizarUnTurno(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_turno = $_POST["id_turno"];
                $id_estado_turno = $_POST["id_estado_turno"]; // se espera 5

                // Obtener caja del empleado actual
                $empleadoController = new EmpleadoController();
                $caja = $empleadoController->ObtenerEmpleadoCaja();
                if (!$caja) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No se pudo determinar la caja.'));
                    exit;
                }
                $idCaja = $caja->getId();

                // Obtener el turno por id
                $turno = $this->turnoRepository->buscarPorId($id_turno);
                if (!$turno) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('Turno no encontrado.'));
                    exit;
                }

                // Verificar que el turno pertenezca a esta caja
                // Aseguramos comparar enteros (evita fallo por tipo '1' !== 1)
                if ((int)$turno->getCaja() !== (int)$idCaja) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('El turno no pertenece a su caja.'));
                    exit;
                }

                // Verificar que el estado actual sea '3' (En atención)
                $estadoActual = $this->turnoRepository->obtenerEstadoActual($id_turno);
                if ((int)$estadoActual !== 3) { // 3 = En atención
                    header('Location: ' . BASE_URL . '/operador?...');
                    exit;
                }

                // Normalizamos el valor: quitamos espacios y casteamos a entero
                $estadoActualNormalized = is_null($estadoActual) ? null : (int) trim($estadoActual);
                if (is_null($estadoActualNormalized)) {
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('No se encontró el estado del turno. Contacte al admin. ID turno: '.$id_turno));
                    exit;
                }
                if ($estadoActualNormalized !== 3) {
                    if ($estadoActualNormalized === 5) {
                        header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Turno ya finalizado.'));
                        exit;
                    }
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode('El turno no está en atención y no puede finalizarse. Estado actual: ' . $estadoActualNormalized));
                    exit;
                }

                if ($estadoActualNormalized !== 3) {
                    // Opcional: añadir el valor actual al mensaje para depurar desde la UI
                    $msg = 'El turno no está en atención y no puede finalizarse. Estado actual: ' . var_export($estadoActualNormalized, true);
                    header('Location: ' . BASE_URL . '/operador?error=' . urlencode($msg));
                    exit;
                }

                // Cambiar a Finalizado (5)
                $this->turnoRepository->cambiarEstado($id_turno, 5);
                header('Location: ' . BASE_URL . '/operador?mensaje=' . urlencode('Turno finalizado.'));
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                exit;
            }

            // try {
                //     echo var_dump($_POST);
                //     $id_turno = $_POST["id_turno"];
                //     $id_estado_turno = $_POST["id_estado_turno"];
                //     $this->turnoRepository->cambiarEstado($id_turno, $id_estado_turno);
                //     header('Location: ' . BASE_URL . '/operador?mensaje=funciono');
                //     exit;
                // } catch (Exception $e) {
                //     header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                //     exit;
                // }
        }
    }




    // public function obtenerNumeroCajaActiva(int $idOperador): ?array {
    //     // Retorna el turno que está actualmente en atención en la caja asignada al operador
    //     $operador = $this->repositorioOperadores->obtenerPorId($idOperador);
    //     if (!$operador) {
    //         return null;
    //     }

    //     $caja = $operador['caja_asignada'] ?? null;
    //     if (!$caja) {
    //         return null;
    //     }

    //     // Consulta si hay un turno en atención en esa caja
    //     $turnoEnAtencion = $this->repositorioTurnos->obtenerTurnoEnAtencionPorCaja($caja['id']);
    //     return $turnoEnAtencion;
    // }

    // public function puedeLlamarTurno(int $idOperador): bool {
    //     // Verifica si el operador puede llamar un nuevo turno. Si ya tiene uno en atención, devuelve false.
    //     $turnoActual = $this->obtenerNumeroCajaActiva($idOperador);
    //     return $turnoActual === null;
    // }



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

        $turnos = $this->turnoRepository->obtenerTurnosPaginados($pagina, $porPagina);
        $totalTurnos = $this->turnoRepository->contarTurnos();
        $totalPaginas = ceil($totalTurnos / $porPagina);

        return [
            'turnos' => $turnos,
            'paginaActual' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalTurnos' => $totalTurnos
        ];
    } catch (Exception $e) {
        error_log("Error en gestionDeTurnos: " . $e->getMessage());
        return [
            'turnos' => [],
            'paginaActual' => 1,
            'totalPaginas' => 0,
            'totalTurnos' => 0
        ];
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
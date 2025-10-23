<?php

class TurnoController {
    private ServicioTurnos $servicioTurnos;

    public function __construct() {
        $this->servicioTurnos = new ServicioTurnos();
    }

    // ============ VISTAS PÚBLICAS ============
    public function mostrarPorId(int $id): ?Turno {
        try {
            $turno = $this->servicioTurnos->obtenerPorId($id);
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
                    $cliente = $this->servicioTurnos->obtenerClientePorNumeroCuenta($numeroCuenta);
                    if (!$cliente) {
                        throw new Exception("Cliente no encontrado");
                    }
                    $this->servicioTurnos->iniciarSesionCliente($cliente);
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
                        $cliente = $this->servicioTurnos->obtenerClientePorNumeroCuenta($numeroCuenta);
                        
                        if (!$cliente) {
                            throw new Exception("Cliente no encontrado");
                        }
                    }
                        
                    $turno = $this->servicioTurnos->generarTurno($_POST, $cliente);
                    if ($cliente) {
                        $this->servicioTurnos->cerrarSesionCliente();
                    }
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

    public function mostrarTiempoEspera(): void {
        header('Content-Type: application/json');
        try {
            if (!isset($_GET['id'])) {
                throw new Exception("ID requerido");
            }

            $idTurno = (int)$_GET['id'];
            $resultado = $this->servicioTurnos->obtenerTiempoEspera($idTurno);
            
            echo json_encode($resultado);
            exit;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
            exit;
        }
    }

        public function mostrarTurnos(): array {
        $turnos = $this->servicioTurnos->mostrarTurnos();
        return $turnos;
    }

    // ============ VISTAS INTERNAS ============
    public function listarTodos(): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTodos();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosActivos(): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTurnosActivos();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosEnEspera(): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTurnosEnEspera();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosEnAtencion(): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTurnosEnAtencion();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarTurnosCompletados(): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTurnosCompletados();
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function gestion(): array {
        try {
            $pagina = isset($_GET['pagina_Turno']) ? (int)$_GET['pagina_Turno'] : 1;
            $porPagina = 10;

            // Obtener filtros simples desde GET
            $filtros = [
                'id_departamento' => $_GET['departamento'] ?? '',
                'id_estado' => $_GET['estado'] ?? '',
                'id_caja' => $_GET['caja'] ?? '',
                'fecha' => $_GET['fecha'] ?? '',
                'numero_turno' => $_GET['numero'] ?? ''
            ];

            return $this->servicioTurnos->gestion($pagina, $porPagina, $filtros);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function mostrarClientePorId(int $idCliente): ?Cliente {
        try {
            $cliente = $this->servicioTurnos->obtenerClientePorId($idCliente);
            return $cliente;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function cambiarEstado(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $idTurno = $_POST["id_turno"];
                $idEstado = $_POST["id_estado"];

                if (empty($idTurno) || empty($idEstado)) {
                    throw new Exception("Datos insuficientes");
                }

                if (!$this->servicioTurnos->cambiarEstado($_POST)) {
                    throw new Exception("Algo salió mal durante el cambio de estado");
                }

                header("Location: ". BASE_URL . "/operador?mensaje=actualizado");
                exit();
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "/operador?error=" . urlencode($e->getMessage()));
                exit();
            }
        }
    }

    public function listarTurnosPorCaja(int $id_caja): ?array {
        try {
            $turnos = $this->servicioTurnos->obtenerTurnosPorCaja($id_caja);
            return $turnos;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function imprimirTurno(?Cliente $cliente, int $idCaja, Turno $turno): void {
        try {
            $this->servicioTurnos->imprimirTurno($cliente, $idCaja, $turno);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    // public function consultarTurno(): void {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $numeroTurno = $_POST['numero_turno'] ?? '';
                
    //             if (empty($numeroTurno)) {
    //                 throw new Exception("Número de turno es obligatorio");
    //             }
                
    //             $turno = $this->turnoRepository->buscarPorNumero($numeroTurno);
                
    //             if (!$turno) {
    //                 throw new Exception("Turno no encontrado");
    //             }
                
    //             $estadoActual = $this->turnoRepository->obtenerEstadoActual($turno->getId());
    //             $caja = $this->cajaRepository->obtenerCajaPorId($turno->getCajaId());
                
    //             require_once __DIR__ . '/../views/publicas/consultar-turno.php';
    //         } catch (Exception $e) {
    //             $error = $e->getMessage();
    //             require_once __DIR__ . '/../views/publicas/consultar-turno.php';
    //         }
    //     } else {
    //         require_once __DIR__ . '/../views/publicas/consultar-turno.php';
    //     }
    // }

    // ============ MANEJAR ERRORES ============
    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

}
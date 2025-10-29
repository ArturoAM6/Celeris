<?php

class ServicioTurnos {
    private TurnoRepository $turnoRepository;
    private ClienteRepository $clienteRepository;
    private CajaRepository $cajaRepository;

    public function __construct() {
        $this->turnoRepository = new TurnoRepository();
        $this->clienteRepository = new ClienteRepository();
        $this->cajaRepository = new CajaRepository();
    }

    public function obtenerTodos(): array {
        $turnos = $this->turnoRepository->todos();
        return $turnos;
    }

    public function obtenerTurnosPorCaja(int $idCaja): array {
        $data = [];
        $turnos = $this->turnoRepository->buscarTurnosPorCaja($idCaja, true, true);
        foreach($turnos as $turno) {
            if ($turno->getEstadoId() === 1) {
                $data["turnoLlamado"][] = $turno;
            } elseif ($turno->getEstadoId() === 3) {
                $data["turnoEnAtencion"][] = $turno;
            } elseif ($turno->getEstadoId() === 2) {
                $data["turnoEnEspera"][] = $turno;
            }
        }
        return $data;
    }

    public function generarTurno(array $data, ?Cliente $cliente): ?Turno {
        // Asignar caja disponible
        $caja = $this->cajaRepository->buscarDisponible($data["id_departamento"]);
        // Obtener siguiente número de turno
        $numeroTurno = $this->turnoRepository->obtenerSiguienteNumero($data["id_departamento"]);
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
        return $turno;
    }

    public function iniciarSesionCliente(Cliente $cliente): void {
        $_SESSION["numeroCuenta"] = $cliente->getNumeroCuenta();
        $_SESSION["idCliente"] = $cliente->getId();
    }

    public function cerrarSesionCliente(): void {
        session_unset();
        session_destroy();
    }

    public function imprimirTurno(?Cliente $cliente, int $idCaja, Turno $turno): void {
        $caja = $this->cajaRepository->buscarPorId($idCaja);
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
        ServicioPDF::generarTurnoPdf(
            $caja->getNumero(),
            $departamento,
            $turno->getNumero(),
            (!$cliente) ? "N/A" : $cliente->getNombreCompleto()
        );
    }

    public function subirTurnoADrive(?Cliente $cliente, int $idCaja, Turno $turno): string {
        $caja = $this->cajaRepository->buscarPorId($idCaja);
        switch ($caja->getDepartamento()) {
            case 1: $departamento = 'Cajas'; break;
            case 2: $departamento = 'Asociados'; break;
            case 3: $departamento = 'Caja Fuerte'; break;
            case 4: $departamento = 'Asesoramiento Financiero'; break;
            default: throw new Exception("No existe el departamento.");
        }
        
        $pdfContent = ServicioPDF::generarTurnoPdfString(
            $caja->getNumero(),
            $departamento,
            $turno->getNumero(),
            (!$cliente) ? "N/A" : $cliente->getNombreCompleto()
        );
        
        $client = getClient();
        $service = new Google\Service\Drive($client);
        
        $fileName = 'turno_' . $turno->getNumero() . '_' . date('YmdHis') . '.pdf';
        
        $fileMetadata = new Google\Service\Drive\DriveFile(['name' => $fileName]);
        
        $file = $service->files->create($fileMetadata, [
            'data' => $pdfContent,
            'mimeType' => 'application/pdf',
            'uploadType' => 'multipart'
        ]);
        
        return $file->id;
    }

    public function obtenerClientePorNumeroCuenta(string $numeroCuenta): ?Cliente {
        $cliente = $this->clienteRepository->buscarPorNumeroCuenta($numeroCuenta);
        if (!$cliente) {
            return null;
        }
        return $cliente;
    }

    public function obtenerClientePorTurno(int $idTurno): ?Cliente {
        $cliente = $this->clienteRepository->buscarPorTurno($idTurno);
        if (!$cliente) {
            return null;
        }
        return $cliente;
    }

    public function obtenerClientePorId(int $idCliente): ?Cliente {
        $cliente = $this->clienteRepository->buscarPorid($idCliente);
        if (!$cliente) {
            return null;
        }
        return $cliente;
    }

    public function obtenerPorId(int $id): ?Turno {
        $turno = $this->turnoRepository->buscarPorId($id);
        if (!$turno) {
            return null;
        }
        return $turno;
    }

    public function obtenerIdDepartamentoPorTurno(array $turnos): ?array {
        $departamentos = [];
        foreach($turnos as $turno) {
            $departamentos[] = $this->turnoRepository->obtenerIdDepartamentoPorTurno($turno["id"]);
        }
        return $departamentos;
    }

    public function mostrarTurnos(): array {
        $departamentos = array(1, 2, 3, 4);
        $turnos = [];

        foreach ($departamentos as $dep) {
            $turnos[$dep] = [
                'siguiente' => $this->turnoRepository->buscarSiguienteNumero($dep),
                'espera'    => $this->turnoRepository->buscarTurnosEnEspera($dep, $limite=4)
            ];
        }

        return $turnos;
    }

    public function obtenerTiempoEspera(int $idTurno): array {
        $resultado = $this->turnoRepository->obtenerTiempoEspera($idTurno);
        if (isset($resultado['error'])) {
                throw new Exception("Tiempo de espera no disponible");
        }
        $resultado['timestamp_servidor'] = time();
        return $resultado;
    }

    public function obtenerTurnosEnEspera(): array {
        $turnos =$this->turnoRepository->buscarTurnosEnEspera();
        return $turnos;
    }

    public function obtenerTurnosEnAtencion(): array {
        $turnos =$this->turnoRepository->buscarTurnosEnAtencion();
        return $turnos;
    }

    public function obtenerTurnosActivos(): ?array {
        $turnos =$this->turnoRepository->buscarTurnosActivos();
        return $turnos;
    }

    public function obtenerTurnosCompletados(): ?array {
        $turnos =$this->turnoRepository->buscarTurnosCompletados();
        return $turnos;
    }

    public function cambiarEstado(array $data): bool {
        $turno = $this->turnoRepository->buscarPorId($data["id_turno"]);

        if (!$turno) {
            return false;
        }
        $turno->setEstado($data["id_estado"]);
        switch ($turno->getEstadoId()) {
            case 1:
                $turno->setTimestampLlamado(date("Y-m-d H:i:s"));
                $this->turnoRepository->guardarEnLog($turno->getId(), $turno->getEstadoId(), $turno->getTimestampLlamado());
                $this->turnoRepository->actualizarTimestampLlamado($turno->getId());
                return true;
            case 3:
                $turno->setTimestampInicioAtencion(date("Y-m-d H:i:s"));
                $this->turnoRepository->guardarEnLog($turno->getId(), $turno->getEstadoId(), $turno->getTimestampInicioAtencion());
                $this->turnoRepository->actualizarTimestampInicioAtencion($turno->getId());
                return true;
            case 4:
                // Cancelado
                $turno->setTimestampFinAtencion(date("Y-m-d H:i:s"));
                $this->turnoRepository->guardarEnLog($turno->getId(), $turno->getEstadoId(), $turno->getTimestampFinAtencion());
                $this->turnoRepository->actualizarTimestampFinAtencion($turno->getId());
                return true;
            case 5;
                $turno->setTimestampFinAtencion(date("Y-m-d H:i:s"));
                $this->turnoRepository->guardarEnLog($turno->getId(), $turno->getEstadoId(), $turno->getTimestampFinAtencion());
                $this->turnoRepository->actualizarTimestampFinAtencion($turno->getId());
                return true;
            default:
                return false;
        }
    }

    public function gestion(int $pagina, int $porPagina, array $filtros): array {
        try {
            $turnos = $this->turnoRepository->obtenerPaginacionConFiltros($pagina, $porPagina, $filtros);
            $totalTurnos = $this->turnoRepository->contarConFiltros($filtros);
            $totalPaginas = ceil($totalTurnos / $porPagina);
    
            return [
                'turnos' => $turnos,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas,
                'totalTurnos' => $totalTurnos,
                'filtros' => $filtros
            ];
        } catch (\Throwable $th) {
            return [
            'turnos' => [],
            'paginaActual' => 1,
            'totalPaginas' => 0,
            'totalTurnos' => 0,
            'filtros' => []
            ];
        }
    }
}
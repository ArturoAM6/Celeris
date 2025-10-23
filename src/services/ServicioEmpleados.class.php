<?php

class ServicioEmpleados {
    private EmpleadoRepository $empleadoRepository;
    private AsignacionCajaRepository $asignacionCajaRepository;

    public function __construct() {
        $this->empleadoRepository = new EmpleadoRepository();
        $this->asignacionCajaRepository = new AsignacionCajaRepository();
    }

    public function obtenerTodos(): ?array {
        $empleados = $this->empleadoRepository->todos();
        return $empleados;
    }

    public function crear(array $data): bool {
        if (!$this->validarNombre($data['nombre']) || !$this->validarNombre($data['apellido_paterno']) || !$this->validarEmail($data['email']) || !$this->validarContraseñas($data["password"], $data["password2"])) {
            return false;
        }

        $empleado = $this->instanciarSegunRol($data);
        $this->empleadoRepository->guardar($empleado);
        return true;
    }

    public function actualizar(array $data): bool {
        if (!$this->validarNombre($data['nombre']) || !$this->validarNombre($data['apellido_paterno']) || !$this->validarEmail($data['email'])) {
            return false;
        }

        $empleado = $this->empleadoRepository->buscarPorId($data["id"]);

        if (!$empleado) {
            return false;
        }

        $empleadoActualizado = $this->instanciarSegunRol($data);
        $empleadoActualizado->setId($data["id"]);
        return $this->empleadoRepository->actualizar($empleadoActualizado);
    }

    public function desactivar(int $id): bool {
        $empleado = $this->empleadoRepository->buscarPorId($id);

        if (!$empleado) {
            return false;
        }

        return $this->empleadoRepository->desactivar($empleado);
    }

    public function obtenerPorId(int $id): ?Empleado{
        $empleado = $this->empleadoRepository->buscarPorId($id);
        return $empleado;
    }

    public function obtenerAsignados(): ?array {
        $empleados = $this->empleadoRepository->buscarAsignados();
        return $empleados;
    }

    public function obtenerNoAsignadosPorDepartamento(int $idDepartamento): ?array {
        $empleados = $this->empleadoRepository->buscarNoAsignadosPorDepartamento($idDepartamento);
        $data = [];
        
        foreach ($empleados as $empleado) {
            $data[] = [
                'id' => $empleado->getId(),
                'nombre' => $empleado->getNombreCompleto()
            ];
        }

        return $data;
    }

    public function obtenerActivos(): ?array {
        $empleados = $this->empleadoRepository->buscarActivos();
        return $empleados;
    }

    public function iniciarSesion(int $id): bool {
        $empleado = $this->empleadoRepository->buscarPorId($id);

        if (!$empleado) {
            return false;
        }

        return $this->empleadoRepository->iniciarSesion($empleado->getId());
    }

    public function desconectar(Empleado $empleado): bool {
        return $this->empleadoRepository->desconectar($empleado->getId());
    }

    public function gestion(int $pagina, int $porPagina): array {
        try {
            $empleados = $this->empleadoRepository->obtenerPaginacion($pagina, $porPagina);
            $totalEmpleados = $this->empleadoRepository->contar();
            $totalPaginasEmpleados = ceil($totalEmpleados / $porPagina);
    
            return [
                'empleados' => $empleados,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginasEmpleados,
                'totalEmpleados' => $totalEmpleados
            ];
        } catch (Exception $e) {
            return [
                'empleados' => [],
                'paginaActual' => 1,
                'totalPaginas' => 0,
                'totalEmpleados' => 0
            ];
        }
    }

    public function gestionDescansos(int $pagina, int $porPagina): array {
         try {
            $descansos = $this->empleadoRepository->obtenerPaginacionEnDescanso($pagina, $porPagina);
            $totalDescansos = $this->empleadoRepository->contarEnDescanso();
            $totalPaginasDescansos = ceil($totalDescansos / $porPagina);

            return [
                'descansos' => $descansos,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginasDescansos,
                'totalDescansos' => $totalDescansos
            ];
        } catch (Exception $e) {
            return [
                'descansos' => [],
                'paginaActual' => 1,
                'totalPaginas' => 0,
                'totalDescansos' => 0
            ];
        }
    }

    public function obtenerHorarios() {
        $horarios = $this->empleadoRepository->buscarHorarios();
        return $horarios;
    }

    public function actualizarHorario(array $data): bool {
        try {
            foreach ($data as $id => $idTipoTurno) {
                echo "ID: $id " . "TIPOTURNO: $idTipoTurno";
                echo "<br><br><hr>";
                $empleadoActualizado = $this->empleadoRepository->buscarPorId($id);
                $empleadoActualizado->setId($id);
                $empleadoActualizado->setTipoTurno($idTipoTurno);
    
                $this->empleadoRepository->actualizarHorario($empleadoActualizado);
            }
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function gestionHorarios(int $pagina, int $porPagina): array {
        try {
            $horarios = $this->empleadoRepository->obtenerPaginacionHorarios($pagina, $porPagina);
            $totalHorarios = $this->empleadoRepository->contarHorarios();
            $totalPaginasHorarios = ceil($totalHorarios / $porPagina);

            return [
                'horarios' => $horarios,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginasHorarios,
                'totalHorarios' => $totalHorarios
            ];
        } catch (Exception $e) {
            return [
                'horarios' => [],
                'paginaActual' => 1,
                'totalPaginas' => 0,
                'totalHorarios' => 0
            ];
        }
    }

    // Empleado - caja
    public function obtenerEmpleadoPorCaja(array $data): ?array {
        $empleados = [];
        foreach ($data as $caja) {
            $empleado = $this->asignacionCajaRepository->buscarEmpleadoPorCaja($caja);
            $empleados[] = $this->instanciarSegunRol($empleado);
        }

        return $empleados;
    }

    private function validarHorario(int $id): bool {
        $empleado = $this->empleadoRepository->buscarPorId($id);
        if ($empleado->getTipoTurno() == 1) {
            $diasPermitidos = array(1,3,5);
            return (in_array(date('w'), $diasPermitidos)) ? true : false;
        } elseif ($empleado->getTipoTurno() == 2) {
            $diasPermitidos = array(1,2,3,4,5);
            return (in_array(date('w'), $diasPermitidos)) ? true : false;
        }
    }

    private function validarNombre(string $nombre): bool {
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,30}$/", $nombre)) {
            return false;
        } 

        return true;
    }
    
    private function validarContraseñas(string $password, string $password2): bool {
        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$/", $password) || !preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$/", $password2) || $password !== $password2){
            return false;
        }

        return true;
    }

    private function validarEmail(string $email): bool {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        } 

        return true;
    }

    private function instanciarSegunRol(array $data): Empleado {
        $passwordHash = !empty($data['password']) 
            ? password_hash($data['password'], PASSWORD_DEFAULT)
            : $data['password_hash'];

        switch ($data['id_rol']) {
            case 1:
                return new Administrador(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'] ?? null,
                    $data['email'],
                    $passwordHash,
                    $data['id_departamento'],
                    $data['id_tipo_turno'],
                );
            case 2:
                return new Operador(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'] ?? null,
                    $data['email'],
                    $passwordHash,
                    $data['id_departamento'],
                    $data['id_tipo_turno']
                );
            case 3:
                return new Recepcionista(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'] ?? null,
                    $data['email'],
                    $passwordHash,
                    $data['id_departamento'],
                    $data['id_tipo_turno']
                );
            default:
                throw new Exception("Rol desconocido");
        }
    }
}
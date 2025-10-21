<?php

class EmpleadoController {
    private EmpleadoRepository $empleadoRepository;
    private CajaRepository $cajaRepository;
    private TurnoRepository $turnoRepository;
    private ClienteRepository $clienteRepository;


    public function __construct() {
        $this->empleadoRepository = new EmpleadoRepository();
        $this->cajaRepository = new CajaRepository();
        $this->turnoRepository = new TurnoRepository();
        $this->clienteRepository = new ClienteRepository();
    }

    public function listarEmpleados(): ?array {
        try {
            $empleados = $this->empleadoRepository->todos();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    //Funcion de Cesar :) 
    public function MostrarDatosDeEmpleados(): ?array {
        try {
            $empleados = $this->listarEmpleados();
            require_once __DIR__ . '/../views/admin/dashboard.php';
            return $empleados;
        } 

        catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }

    }

    // ----IniOperador----

    

    public function empleadosConCajaPausada(): ?array {
        try {
            $empleados = $this->empleadoRepository->buscarEmpleadosCajaAsignadaPausada();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    // ----IniOperador----

    public function ObtenerEmpleadoCaja(): ?Caja{
        try {
            $id_caja = $this->cajaRepository->getNumeroCaja($_SESSION["id_empleado"]);
            $caja = $this->cajaRepository->obtenerCajaPorId($id_caja);
            return $caja;

        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function CambiarEstadoCaja(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_caja = $_POST["id_caja"];
                $id_estado = $_POST["id_estado"];
                $this->cajaRepository->cambiarEstado($id_caja, $id_estado);
                header('Location: ' . BASE_URL . '/operador?mensaje=funciono');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/operador?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }


    // ----FinOperador----
    public function listarEmpleadosAsignados(): ?array {
        try {
            $empleados = $this->empleadoRepository->buscarEmpleadosAsignados();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarEmpleadosActivos(): ?array {
        try {
            $empleados = $this->empleadoRepository->buscarEmpleadosActivos();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function obtenerEmpleadoPorId(int $id): ?Empleado {
        try {
            $empleado = $this->empleadoRepository->buscarPorId($id);
            return $empleado;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function crearEmpleado(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'];
                $apellido_paterno = $_POST['apellido_paterno'];
                $apellido_materno = $_POST['apellido_materno'] ?? '';
                $password = $_POST['password'];
                $email = $_POST['email'];
                $id_rol = $_POST['id_rol'];
                $id_departamento = $_POST['id_departamento'];
                $id_tipo_turno = $_POST['id_tipo_turno'];

                if (empty($nombre) || empty($apellido_paterno) || empty($password) || empty($email) || empty($id_rol) || empty($id_departamento) || empty($id_tipo_turno)) {
                    throw new Exception("Los campos con * son obligatorios");
                }

                $empleado = $this->instanciarEmpleadoSegunRol($_POST);
                $this->empleadoRepository->guardar($empleado);

                header('Location: ' . BASE_URL . '/admin?mensaje=creado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function editarEmpleado(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $apellido_paterno = $_POST['apellido_paterno'];
                $apellido_materno = $_POST['apellido_materno'] ?? '';
                $email = $_POST['email'];
                $id_rol = $_POST['id_rol'];
                $id_departamento = $_POST['id_departamento'];
                $id_tipo_turno = $_POST['id_tipo_turno'];

                if (empty($nombre) || empty($apellido_paterno) || empty($email) || empty($id_rol) || empty($id_departamento) || empty($id_tipo_turno)) {
                    throw new Exception("Los campos con * son obligatorios");
                }

                $empleado = $this->empleadoRepository->buscarPorId($id);

                if (!$empleado) {
                    throw new Exception("Empleado no encontrado");
                }

                $empleadoActualizado = $this->instanciarEmpleadoSegunRol($_POST);
                $empleadoActualizado->setId($id);

                $this->empleadoRepository->actualizar($empleadoActualizado);
                
                header('Location: ' . BASE_URL . '/admin?mensaje=actualizado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function desactivarEmpleado(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];

                $empleado = $this->empleadoRepository->buscarPorId($id);

                if (!$empleado) {
                    throw new Exception("Empleado no encontrado");
                }

                $this->empleadoRepository->bajaEmpleado($empleado);
                header('Location: ' . BASE_URL . '/admin?mensaje=eliminado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function filtrarPorDepartamento(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idDepartamento = $_POST['id_departamento'] ?? null;
    
                if (!$idDepartamento) {
                    throw new Exception("error: Falta el ID del departamento");
                }
    
                $empleados = $this->empleadoRepository->buscarEmpleadosAsignadosPorDepartamento($idDepartamento);
                $data = [];
    
                foreach ($empleados as $empleado) {
                    $data[] = [
                        'id' => $empleado->getId(),
                        'nombre' => $empleado->getNombreCompleto()
                    ];
                }
    
                header('Content-Type: application/json');
                echo json_encode($data);
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function validarTipoTurno(Empleado $empleado): bool {
        try {
            if ($empleado->getTipoTurno() == 1) {
                $diasPermitidos = array(1,3,5);
                return (in_array(date('w'), $diasPermitidos)) ? true : false;
            } elseif ($empleado->getTipoTurno() == 2) {
                $diasPermitidos = array(1,2,3,4,5);
                return (in_array(date('w'), $diasPermitidos)) ? true : false;
            }
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
            return false;
        }
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

    private function instanciarEmpleadoSegunRol(array $data): Empleado {
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

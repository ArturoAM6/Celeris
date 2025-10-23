<?php

class EmpleadoController {
    private ServicioEmpleados $servicioEmpleados;

    public function __construct() {
        $this->servicioEmpleados = new ServicioEmpleados();
    }

    public function listarTodos(): ?array {
        try {
            $empleados = $this->servicioEmpleados->obtenerTodos();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarAsignados(): ?array {
        try {
            $empleados = $this->servicioEmpleados->obtenerAsignados();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarActivos(): ?array {
        try {
            $empleados = $this->servicioEmpleados->obtenerActivos();
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function generar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'];
                $apellido_paterno = $_POST['apellido_paterno'];
                $apellido_materno = $_POST['apellido_materno'] ?? '';
                $password = $_POST['password'];
                $password2 = $_POST['password2'];
                $email = $_POST['email'];
                $id_rol = $_POST['id_rol'];
                $id_departamento = $_POST['id_departamento'];
                $id_tipo_turno = $_POST['id_tipo_turno'];

                if (empty($nombre) || empty($apellido_paterno) || empty($password) || empty($email) || empty($id_rol) || empty($id_departamento) || empty($id_tipo_turno)) {
                    throw new Exception("Los campos con * son obligatorios");
                }

                if (!$this->servicioEmpleados->crear($_POST)) {
                    throw new Exception("Algo salió mal durante la creación del empleado");
                }

                header('Location: ' . BASE_URL . '/admin?mensaje=creado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function editar(): void {
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

                if (!$this->servicioEmpleados->actualizar($_POST)) {
                    throw new Exception("Algo salió mal durante la actualización del empleado");
                }
                
                header('Location: ' . BASE_URL . '/admin?mensaje=actualizado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function desactivar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                
                if (!$this->servicioEmpleados->desactivar($id)) {
                    throw new Exception("Algo salió mal durante la desactivación del empleado");
                }
            
                header('Location: ' . BASE_URL . '/admin?mensaje=eliminado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function mostrarPorId(int $id): ?Empleado {
        try {
            $empleado = $this->servicioEmpleados->obtenerPorId($id);
            return $empleado;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function filtrarPorDepartamento(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idDepartamento = $_POST['id_departamento'] ?? null;
    
                if (!$idDepartamento) {
                    throw new Exception("error: Falta el ID del departamento");
                }
    
                $data = $this->servicioEmpleados->obtenerNoAsignadosPorDepartamento($idDepartamento);
    
                header('Content-Type: application/json');
                echo json_encode($data);
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public function gestion(): array {
        try {
            $pagina = isset($_GET['pagina_Empleados']) ? (int)$_GET['pagina_Empleados'] : 1;
            $porPagina = 10;

            return $this->servicioEmpleados->gestion($pagina, $porPagina);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function gestionDescansos(): array {
        try {
            $pagina = isset($_GET['pagina_Descanso']) ? (int)$_GET['pagina_Descanso'] : 1;
            $porPagina = 10;

            return $this->servicioEmpleados->gestionDescansos($pagina, $porPagina);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function listarHorarios(): array {
        try {
            $horarios = $this->servicioEmpleados->obtenerHorarios();
            return $horarios;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function editarHorario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $empleados = $_POST['id_tipo_turno'];

                if (empty($empleados)) {
                    throw new Exception("No hay empleados");
                }
                
                if (!$this->servicioEmpleados->actualizarHorario($empleados)) {
                    throw new Exception("Algo salió mal durante la actualización de horarios");
                }
                
                header('Location: ' . BASE_URL . '/admin?mensaje=actualizado');
                exit;
            } catch (Exception $e) {
                header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }
    
    public function gestionHorarios(): array {
        try {
            $pagina = isset($_GET['pagina_Horario']) ? (int)$_GET['pagina_Horario'] : 1;
            $porPagina = 10;

            return $this->servicioEmpleados->gestionHorarios($pagina, $porPagina);
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    public function obtenerEmpleadoPorCaja(array $data): ?array {
        try {
            $empleados = $this->servicioEmpleados->obtenerEmpleadoPorCaja($data);
            return $empleados;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}

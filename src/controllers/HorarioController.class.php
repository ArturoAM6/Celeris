<?php

class HorarioController {
    private HorarioRepository $horarioRepository;
    private EmpleadoRepository $empleadoRepository;


    public function __construct() {
        $this->horarioRepository = new HorarioRepository();
        $this->empleadoRepository = new EmpleadoRepository();
    }

    public function listarEmpleados() {
        try {
            $horario = $this->horarioRepository->todos();
            return $horario;
        } catch (Exception $e) {
            $this->manejarError($e->getMessage());
        }
    }

        public function editarEmpleado(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $id_tipo_turno = $_POST['id_tipo_turno'];

                $empleado = $this->empleadoRepository->buscarPorId($id);

                if (!$empleado) {
                    throw new Exception("Empleado no encontrado");
                }

                $empleadoController = new EmpleadoController();
                $empleadoActualizado = $empleadoController->instanciarEmpleadoSegunRol($_POST);
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
        private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}
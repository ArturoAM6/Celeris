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

        public function modificarHorario(): void {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $array_turnos = $_POST['id_tipo_turno'];

                    foreach ($array_turnos as $id => $id_tipo_turno) {
                        
                        $empleado = $this->empleadoRepository->buscarPorId($id);
        
                        if (!$empleado) {
                            throw new Exception("Empleado no encontrado");
                        }

                        $empleadoActualizado = $this->instanciarEmpleadoSegunRol($empleado);
                        $empleadoActualizado->setId($id);
                        $empleadoActualizado->setTipoTurno($id_tipo_turno);
        
                        $this->horarioRepository->modificarHorario($empleadoActualizado);
                    }
                    
                    header('Location: ' . BASE_URL . '/admin?mensaje=actualizado');
                    exit;
                } catch (Exception $e) {
                    header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
                    exit;
                }
            }
        }

        private function instanciarEmpleadoSegunRol(Empleado $data): Empleado {
            switch ($data->getRol()) {
                case 1:
                    return new Administrador(
                        $data->getNombre(),
                        $data->getApellidoPaterno(),
                        $data->getApellidoMaterno() ?? null,
                        $data->getEmail(),
                        $data->getPasswordHash(),
                        $data->getDepartamento(),
                        $data->getTipoTurno()
                    );
                case 2:
                    return new Operador(
                        $data->getNombre(),
                        $data->getApellidoPaterno(),
                        $data->getApellidoMaterno() ?? null,
                        $data->getEmail(),
                        $data->getPasswordHash(),
                        $data->getDepartamento(),
                        $data->getTipoTurno()
                    );
                case 3:
                    return new Recepcionista(
                        $data->getNombre(),
                        $data->getApellidoPaterno(),
                        $data->getApellidoMaterno() ?? null,
                        $data->getEmail(),
                        $data->getPasswordHash(),
                        $data->getDepartamento(),
                        $data->getTipoTurno()
                    );
                default:
                    throw new Exception("Rol desconocido");
            }
        }

        private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}
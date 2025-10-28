<?php

class AuthController {
    private EmpleadoRepository $empleadoRepository;
    private ServicioEmpleados $servicioEmpleados;

    public function __construct() {
        $this->empleadoRepository = new EmpleadoRepository();
        $this->servicioEmpleados = new ServicioEmpleados();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($password)) {
                    throw new Exception("Correo y contraseña son obligatorios");
                }

                $empleado = $this->empleadoRepository->buscarPorEmail($email);

                if (!$empleado) {
                    http_response_code(404);
                    throw new Exception("Correo no registrado");
                }

                if (!$this->servicioEmpleados->validarHorario($empleado->getId())) {
                    http_response_code(403);
                    throw new Exception("No es posible acceder fuera de horario");
                }

                if (!ServicioAutenticacion::iniciarSesion($empleado, $password)) {
                    http_response_code(400);
                    throw new Exception("Credenciales incorrectas");
                }

                // Establecer status de empleado a activo
                if (!$this->servicioEmpleados->iniciarSesion($empleado->getId())) {
                    http_response_code(500);
                    throw new DatabaseException("Ocurrió un error");
                }

                // Redirigir según rol
                switch ($empleado->getRol()) {
                    case 1:
                        header('Location: '. BASE_URL . '/admin');
                        break;
                    case 2:
                        header('Location: '. BASE_URL . '/operador');
                        break;
                    case 3:
                        header('Location: '. BASE_URL . '/recepcionista');
                        break;
                }
                exit;

            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/publicas/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/publicas/login.php';
        }
    }

    public function logout(): void {
            $empleado = ServicioAutenticacion::obtenerEmpleadoActual();
            
            if (!$empleado) {
                header("location: " . BASE_URL . "/login");
            }

            $this->servicioEmpleados->desconectar($empleado);
            ServicioAutenticacion::cerrarSesion();
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Expires: 0");
            header("Location: " . BASE_URL . "/login");
    }
}
<?php

class AuthController {
    private EmpleadoRepository $empleadoRepository;

    public function __construct() {
        $this->empleadoRepository = new EmpleadoRepository();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($password)) {
                    throw new Exception("Email y contraseña son obligatorios");
                }

                $empleado = $this->empleadoRepository->buscarPorEmail($email);

                if (!$empleado) {
                    throw new Exception("Credenciales incorrectas");
                }

                if (!ServicioAutenticacion::iniciarSesion($empleado, $password)) {
                    throw new Exception("Credenciales incorrectas");
                }

                // Establecer status de empleado a activo
                if (!$this->empleadoRepository->iniciarSesionEmpleado($empleado->getId())) {
                    throw new Exception("Ocurrio un error");
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['logout'];
            $this->empleadoRepository->desconectarEmpleado($id);
            ServicioAutenticacion::cerrarSesion();
        }
    }
}
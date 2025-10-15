<?php

// Clase ServicioAutenticacion
class ServicioAutenticacion {

    // Metodos estáticos (No hace falta instanciar/crear un objeto para acceder a ellos).
    
    // Metodo estatico publico que maneja el inicio de sesión. Pide un objeto Empleado y un string. Devuelve un booleano.
    public static function iniciarSesion(Empleado $empleado, string $password): bool {
        if (self::validarCredenciales($empleado, $password)) {
            session_start();
            $_SESSION['id_empleado'] = $empleado->getId();
            $_SESSION['id_rol'] = $empleado->getRol();
            return true;
        }
        return false;
    }

    // Metodo estatico publico que maneja el cierre de sesión. No devuelve nada.
    public static function cerrarSesion(): void {
        session_start();
        session_destroy();
    }

    // Metodo estatico publico que para validacion de credenciales. Pide un objeto Empleado y un string. Devuelve un booleano.
    public static function validarCredenciales(Empleado $empleado, string $password): bool {
        return password_verify($password, $empleado->getPasswordHash());
    }

    // Metodo estatico publico que obtiene el empleado de la sesion actual. Devuelve un objeto Empleado o null.
    public static function getEmpleadoActual(): ?Empleado {
        if (!isset($_SESSION['id_empleado'])) {
            return null;
        }   
        
        $repo = new EmpleadoRepository();
        return $repo->buscarPorId($_SESSION['id_empleado']);
    }
}
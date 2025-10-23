<?php

// Clase Database
class Database {
    // Propiedades
    private static ?Database $instancia = null; // Propiedad privada estática que debe ser tipo Database o null, es null por defecto.
    private PDO $conexion; // Propiedad privada que debe ser tipo PDO.

    // Constructor, se corre cada vez que se instancia/crea un nuevo objeto de la clase Database.
    public function __construct() {
        try {
            // Creacion y asignacion de un objeto PDO a la propiedad $conexion.
            $this->conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        } catch (PDOException $e) {
            // En caso de error, detiene la ejecucion del codigo y devuelve el mensaje.
            throw new RuntimeException("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Método publico estático para devolver la instancia actual de la clase Database.
    public static function getInstancia(): Database {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }

    // Método publico para devolver la conexión a la base de datos.
    public function getConexion(): PDO {
        return $this->conexion;
    }
}
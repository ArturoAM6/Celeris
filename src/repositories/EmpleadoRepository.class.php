<?php

class EmpleadoRepository {
    private PDO $conexion;

    public function buscarEmpleadosCajaAsignadaPausada(): array{
        $stmt = $this->conexion->prepare ("
        SELECT e.*, c.id as id_caja, ec.nombre as estado_caja
        FROM empleados e
        JOIN asignacion_cajas ac ON e.id = ac.id_empleado
        JOIN cajas c ON ac.id_caja = c.id
        JOIN estado_caja ec ON c.id_estado = ec.id
        WHERE ec.nombre = 'Pausada'
        ");

        $stmt->execute();
        $empleados=[];
        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    public function buscarEmpleadosConCajaPausada(): array{
        $stmt = $this->conexion->prepare("
        SELECT e.* FROM empleados e JOIN asignacion_cajas ac ON e.id = ac.id_empleado
        JOIN cajas c ON ac.id_caja = c.id
        WHERE c.id_estado = 3 AND e.activo = 1");

        $stmt->execute();
        $empleados =[];
        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE activo = 1");
        $stmt->execute();
        $empleados = [];
        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    public function guardar(Empleado $empleado): void {
        $stmt = $this->conexion->prepare(
            "INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, email, password_hash, id_departamento, id_tipo_turno, id_rol, id_horario, activo) 
            VALUES (:nombre, :apellido_paterno, :apellido_materno, :email, :password_hash, :id_departamento, :id_tipo_turno, :id_rol, :id_horario, 1)"
        );
        $stmt->execute([
            ':nombre' => $empleado->getNombre(),
            ':apellido_paterno' => $empleado->getApellidoPaterno(),
            ':apellido_materno' => $empleado->getApellidoMaterno(),
            ':email' => $empleado->getEmail(),
            ':password_hash' => $empleado->getPasswordHash(),
            ':id_departamento' => $empleado->getDepartamento(),
            ':id_tipo_turno' => $empleado->getTipoTurno(),
            ':id_rol' => $empleado->getRol(),
            ':id_horario' => $empleado->getHorario()
        ]);

        $empleado->setId($this->conexion->lastInsertId());
    }

    public function actualizar(Empleado $empleado): bool {
        $stmt = $this->conexion->prepare(
            "UPDATE empleados SET 
            nombre = :nombre, 
            apellido_paterno = :apellido_paterno, 
            apellido_materno = :apellido_materno, 
            email = :email,  
            id_departamento = :id_departamento, 
            id_tipo_turno = :id_tipo_turno, 
            id_rol = :id_rol
            WHERE id = :id"
        );

        return $stmt->execute([
            ':nombre' => $empleado->getNombre(),
            ':apellido_paterno' => $empleado->getApellidoPaterno(),
            ':apellido_materno' => $empleado->getApellidoMaterno(),
            ':email' => $empleado->getEmail(),
            ':id_departamento' => $empleado->getDepartamento(),
            ':id_tipo_turno' => $empleado->getTipoTurno(),
            ':id_rol' => $empleado->getRol(),
            ':id' => $empleado->getId()
        ]);
    }

    public function bajaEmpleado(Empleado $empleado): bool {
        $stmt = $this->conexion->prepare("UPDATE empleados SET activo = 0 WHERE id = :id");
        return $stmt->execute([':id' => $empleado->getId()]);
    }

    public function buscarPorEmail(string $email): ?Empleado { 
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearEmpleadoSegunRol($data);
    }

    public function buscarPorId(int $id): ?Empleado { 
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearEmpleadoSegunRol($data);
    }

    public function buscarEmpleadosAsignados(): array {
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE id IN (SELECT id_empleado FROM asignacion_cajas) AND id_rol = 2 AND activo = 1");
        $stmt->execute();
        $empleados = [];

        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    public function buscarEmpleadosAsignadosPorDepartamento(int $id_departamento): array {
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE id NOT IN (SELECT id_empleado FROM asignacion_cajas) AND id_rol = 2 AND activo = 1 AND id_departamento = :id_departamento");
        $stmt->execute([':id_departamento' => $id_departamento]);
        $empleados = [];

        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    public function iniciarSesionEmpleado(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE empleados SET status = 1 WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            return true;
        }
        return false;
    }

    public function desconectarEmpleado(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE empleados SET status = 0 WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            return true;
        }
        return false;
    }
    
    public function buscarEmpleadosActivos(): ?array {
        $stmt = $this->conexion->prepare("SELECT * FROM empleados WHERE status = 1");
        $stmt->execute();
        $empleados = [];

        while ($data = $stmt->fetch()) {
            $empleados[] = $this->crearEmpleadoSegunRol($data);
        }

        return $empleados;
    }

    private function crearEmpleadoSegunRol(array $data): Empleado {
        switch ($data['id_rol']) {
            case 1:
                $empleado = new Administrador(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'],
                    $data['email'],
                    $data['password_hash'],
                    $data['id_departamento'],
                    $data['id_tipo_turno']
                );
                break;
            case 2:
                $empleado = new Operador(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'],
                    $data['email'],
                    $data['password_hash'],
                    $data['id_departamento'],
                    $data['id_tipo_turno']
                );
                break;
            case 3:
                $empleado = new Recepcionista(
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'],
                    $data['email'],
                    $data['password_hash'],
                    $data['id_departamento'],
                    $data['id_tipo_turno']
                );
                break;
            default:
                throw new InvalidArgumentException("Rol desconocido: " . $data['id_rol']);
        }
        
        $empleado->setId($data['id']);
        return $empleado;
    }

    //PAGINACION
    public function obtenerEmpleadosPaginados(int $pagina, int $porPagina): array {
        $inicio = ($pagina - 1) * $porPagina;
        $query = "SELECT * FROM empleados ORDER BY id ASC LIMIT :inicio, :porPagina";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $empleados = [];
        while ($empleado = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $empleados[] = $this->crearEmpleadoSegunRol($empleado);
        }

        return $empleados;
        
    }

    public function contarEmpleados(): int {
        $query = "SELECT COUNT(*) FROM empleados";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerDescansosPaginados(int $pagina, int $porPagina): array {
        $inicio = ($pagina - 1) * $porPagina;
        $query = "SELECT e.* FROM empleados e 
            JOIN asignacion_cajas ac ON e.id = ac.id_empleado
            JOIN cajas c ON ac.id_caja = c.id
            WHERE c.id_estado = 3 AND e.activo = 1 
            ORDER BY e.id DESC 
            LIMIT :inicio, :porPagina";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $descansos = [];
        while ($descanso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $descansos[] = $this->crearEmpleadoSegunRol($descanso);
        }

        return $descansos;
        
    }

    public function contarDescansos(): int {
        $query = "SELECT COUNT(*) FROM empleados e JOIN asignacion_cajas ac ON e.id = ac.id_empleado
        JOIN cajas c ON ac.id_caja = c.id
        WHERE c.id_estado = 3 AND e.activo = 1";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }


    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }

}
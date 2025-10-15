<?php

class CajaRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function obtenerCajaPorId(int $id): ?Caja {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $caja = $stmt->fetch();

        if (!$caja) {
            throw new Exception("La caja no existe");
        }

        return $this->crearCajaDesdeArray($caja);
    }

    public function obtenerCajasPorDepartamento(int $id_departamento): array {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas WHERE id_departamento = :id_departamento');
        $stmt->execute([':id_departamento' => $id_departamento]);
        $cajas= [];

        while ($caja = $stmt->fetch()) {
            $cajas[] = $this->crearCajaDesdeArray($caja);
        }

        return $cajas;
    }

    public function obtenerCajaDisponible(): ?array {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas WHERE id_estado = 1 LIMIT 1');
        $stmt->execute();
        $caja = $stmt->fetch();

        if (!$caja) {
            throw new Exception("No hay cajas disponibles");
        }

        $this->crearCajaDesdeArray($caja);

        return $caja;
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas');
        $stmt->execute();
        $cajas= [];

        while ($caja = $stmt->fetch()) {
            $cajas[] = $this->crearCajaDesdeArray($caja);
        }

        return $cajas;
    }

    // public function guardar(Caja $caja): void {
    //     $stmt = $this->conexion->prepare(
    //         "INSERT INTO cajas (numero, id_departamento, id_estado) 
    //         VALUES (:numero, :id_departamento, :id_estado)"
    //     );
    //     $stmt->execute([
    //         ':numero' => $caja->getNumero(),
    //         ':id_departamento' => $caja->getDepartamento(),
    //         ':id_estado' => $caja->getEstado()
    //     ]);

    //     $caja->setId($this->conexion->lastInsertId());
    // }

    public function actualizar(Caja $caja): bool {
        $stmt = $this->conexion->prepare(
            "UPDATE cajas SET 
            numero = :numero,
            id_departamento = :id_departamento,
            id_estado = :id_estado
            WHERE id = :id"
        );

        return $stmt->execute([
            ':numero' => $caja->getNumero(),
            ':id_departamento' => $caja->getDepartamento(),
            ':id_estado' => $caja->getEstado(),
            ':id' => $caja->getId()
        ]);
    }

    public function asignarCaja(int $id_caja, int $id_empleado): void {
        $stmt = $this->conexion->prepare("UPDATE asignacion_cajas SET id_caja = :id_caja, id_empleado = :id_empleado WHERE id_caja = :id");
        $stmt->execute([
            ':id_caja' => $id_caja,
            ':id_empleado' => $id_empleado,
            ':id' => $id_caja
        ]);
    }

    public function cambiarEstado(int $id_caja, int $id_estado): bool {
        $stmt = $this->conexion->prepare('UPDATE cajas SET id_estado = :id_estado WHERE id = :id');
        
        return $stmt->execute([
            'id_estado' => $id_estado, 
            ':id' => $id_caja
        ]);
    }

    public function getCajaEmpleado(int $id_caja): int {
        $stmt = $this->conexion->prepare('SELECT id_empleado FROM asignacion_cajas WHERE id_caja = :id_caja');
        $stmt->execute([':id_caja' => $id_caja]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $empleado['id_empleado'];
    }

    private function crearCajaDesdeArray(array $data): Caja {
        $caja = new Caja(
            $data['numero'],
            $data['id_departamento'],
            $data['id_estado']
        );
        
        $caja->setId($data['id']);

        return $caja;
    }

    private function manejarError(string $mensaje): void {
        $error = $mensaje;
        require_once __DIR__ . '/../views/error.php';
    }
}

?>
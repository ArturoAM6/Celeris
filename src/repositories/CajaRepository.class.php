<?php

class CajaRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
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

    public function obtenerCajaDisponible(int $id_departamento): ?Caja {
        $stmt = $this->conexion->prepare('SELECT c.id, c.numero, c.id_departamento, c.id_estado, COUNT(t.id) AS ocurrencias
            FROM cajas c
            JOIN turnos t ON t.id_caja = c.id
            WHERE c.id_departamento = :id_departamento
            AND c.id_estado = 1
            AND t.id NOT IN (
                SELECT tl.id_turno
                FROM turnos_log tl
                WHERE tl.id_estado IN (4,5)
                    AND tl.timestamp_actualizacion = (
                        SELECT MAX(tl2.timestamp_actualizacion)
                        FROM turnos_log tl2
                        WHERE tl2.id_turno = tl.id_turno
                    )
            )
            GROUP BY c.id
            UNION
            SELECT c.id, c.numero, c.id_departamento, c.id_estado, 0 AS ocurrencias
            FROM cajas c
            WHERE c.id_departamento = :id_departamento
            AND c.id_estado = 1
            AND c.id NOT IN (SELECT id_caja FROM turnos)
            ORDER BY ocurrencias ASC, id ASC
            LIMIT 1'
        );
        $stmt->execute([':id_departamento' => $id_departamento]);
        $caja = $stmt->fetch();

        if (!$caja) {
            throw new Exception("Error de base de datos");
        }

        $caja = $this->crearCajaDesdeArray($caja);  

        return $caja;
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

    // ---Operador---

    public function getNumeroCaja(int $id_empleado): ?int {
        $stmt = $this->conexion->prepare('SELECT id_caja FROM asignacion_cajas WHERE id_empleado = :id_empleado');
        $stmt->execute([':id_empleado' => $id_empleado]);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);

        return $caja['id_caja'] ?? null;
    }

    // ---Operador---

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
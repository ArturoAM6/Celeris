<?php

class HorarioRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare(
        "SELECT e.id, h.hora_entrada, h.hora_salida, t.id as tipo_turno
        FROM empleados e
        INNER JOIN horarios h ON e.id_horario = h.id
        INNER JOIN tipo_turno t ON e.id_tipo_turno = t.id"
        );
        $stmt->execute();
        $horarios = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $horarios[] = $data;
        }
        return $horarios;
    }

    public function modificarHorario(Empleado $empleado): bool{
        $stmt = $this->conexion->prepare(
            "UPDATE empleados SET 
            id_tipo_turno = :id_tipo_turno
            WHERE id = :id"
        );
        
        return $stmt->execute([
            ':id_tipo_turno' => $empleado->getTipoTurno(),
            ':id' => $empleado->getId()
        ]);
    }
}

?> 
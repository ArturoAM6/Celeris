<?php

class HorarioRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare("SELECT h.hora_entrada, h.hora_salida, t.id
        FROM horarios h, tipo_turno t");
        $stmt->execute();
        $empleados = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $empleados[] = $data;
        }

        return $empleados;
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
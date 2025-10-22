<?php

class HorarioRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare(
        "SELECT e.id, e.nombre, e.apellido_paterno, h.hora_entrada, h.hora_salida, t.id as tipo_turno
        FROM empleados e
        INNER JOIN horarios h ON e.id_horario = h.id
        INNER JOIN tipo_turno t ON e.id_tipo_turno = t.id
        ORDER BY e.id"
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

    //PAGINACION
    public function obtenerHorariosPaginados(int $pagina, int $porPagina): array {
        $inicio = ($pagina - 1) * $porPagina;
        $query = "SELECT e.id, e.nombre, e.apellido_paterno, h.hora_entrada, h.hora_salida, t.id as tipo_turno
               FROM empleados e
               INNER JOIN horarios h ON e.id_horario = h.id
               INNER JOIN tipo_turno t ON e.id_tipo_turno = t.id
               ORDER BY e.id ASC LIMIT :inicio, :porPagina;";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $horarios = [];
        while ($horario = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $horarios[] = ($horario);
        }

        return $horarios;
        
    }

    public function contarHorarios(): int {
        $query = "SELECT COUNT(*) FROM empleados";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}

?> 
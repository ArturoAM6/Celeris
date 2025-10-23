<?php

class AsignacionCajaRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare('SELECT * FROM asignacion_cajas');
        $stmt->execute();
        $cajas= [];

        while ($caja = $stmt->fetch()) {
            $cajas[] = $this->crearDesdeArray($caja);
        }

        return $cajas;
    }

    public function buscarEmpleadoPorCaja(Caja $caja): ?array {
        $stmt = $this->conexion->prepare("SELECT e.* FROM empleados e, asignacion_cajas ac WHERE id_caja = :idCaja AND e.id = ac.id_empleado");
        $stmt->execute([":idCaja" => $caja->getId()]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function buscarCajaPorEmpleado(Empleado $empleado): ?array {
        $stmt = $this->conexion->prepare("SELECT c.* FROM cajas c, asignacion_cajas ac WHERE id_empleado = :idEmpleado AND c.id = ac.id_caja");
        $stmt->execute([":idEmpleado" => $empleado->getId()]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function actualizarAsignacion(Caja $caja, Empleado $empleado): bool {
        $stmt = $this->conexion->prepare("UPDATE asignacion_cajas SET id_empleado = :idEmpleado WHERE id_caja = :idCaja");
        return $stmt->execute([
            "idEmpleado" => $empleado->getId(),
            ":idCaja" => $caja->getId()
        ]);
    }
}
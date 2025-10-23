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

    // // INYECTAR EN SERVICIOEMPLEADOS Y SERVICIOCAJAS
    // public function obtenerCaja(int $idEmpleado): ?int {
    //     try {
    //         $empleado = $this->cajaRepository->buscarCaja($idEmpleado);
    //         return $empleado;
    //     } catch (Exception $th) {
    //         $this->manejarError($e->getMessage());
    //     }
    // }
    // // ESTA TAMBIEN
    // public function asignarEmpleadoCaja(): void {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $id = $_POST['id'];
    //             $numero = $_POST['numero'];
    //             $id_departamento = $_POST['id_departamento'];
    //             $id_empleado = $_POST['id_empleado'];
    //             $id_estado = $_POST['id_estado'];
    
    //             if (empty($id_departamento) || empty($id_empleado) || empty($id_estado)) {
    //                 throw new Exception("Los campos con * son obligatorios");
    //             }
    
    //             $caja = $this->cajaRepository->obtenerCajaPorId($id);
    
    //             if (!$caja) {
    //                 throw new Exception("Caja no encontrada.");
    //             }
    
    //             $cajaActualizada = $this->instanciarCaja($_POST);
    //             $cajaActualizada->setId($id);
                
    //             $this->cajaRepository->actualizar($cajaActualizada);
                    
    //             header('Location: ' . BASE_URL . '/admin?mensaje=asignado');
    //             exit;
    //         } catch (Exception $e) {
    //             header('Location: ' . BASE_URL . '/admin?error=' . urlencode($e->getMessage()));
    //             exit;
    //         }
    //     }
    // }
    
    // public function buscarNumero(int $id_empleado): ?int {
    //     $stmt = $this->conexion->prepare('SELECT id_caja FROM asignacion_cajas WHERE id_empleado = :id_empleado');
    //     $stmt->execute([':id_empleado' => $id_empleado]);
    //     $caja = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $caja['id_caja'] ?? null;
    // }

    // public function buscarCajaEmpleado(int $id_caja): int {
    //     $stmt = $this->conexion->prepare('SELECT id_empleado FROM asignacion_cajas WHERE id_caja = :id_caja');
    //     $stmt->execute([':id_caja' => $id_caja]);
    //     $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $empleado['id_empleado'];
    // }

    // public function asignarCaja(int $id_caja, int $id_empleado): void {
    //     $stmt = $this->conexion->prepare("UPDATE asignacion_cajas SET id_caja = :id_caja, id_empleado = :id_empleado WHERE id_caja = :id");
    //     $stmt->execute([
    //         ':id_caja' => $id_caja,
    //         ':id_empleado' => $id_empleado,
    //         ':id' => $id_caja
    //     ]);
    // }
}
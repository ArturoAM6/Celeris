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
            $cajas[] = $this->crearDesdeArray($caja);
        }

        return $cajas;
    }

    public function buscarPorId(int $id): ?Caja {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $caja = $stmt->fetch();

        if (!$caja) {
            return null;
        }

        return $this->crearDesdeArray($caja);
    }

    public function buscarPorDepartamento(int $idDepartamento): array {
        $stmt = $this->conexion->prepare('SELECT * FROM cajas WHERE id_departamento = :idDepartamento');
        $stmt->execute([':idDepartamento' => $idDepartamento]);
        $cajas= [];

        while ($caja = $stmt->fetch()) {
            $cajas[] = $this->crearDesdeArray($caja);
        }

        return $cajas;
    }

    public function buscarDisponible(int $idDepartamento): ?Caja {
        $stmt = $this->conexion->prepare('SELECT c.id, c.numero, c.id_departamento, c.id_estado, COUNT(t.id) AS ocurrencias
            FROM cajas c
            JOIN turnos t ON t.id_caja = c.id
            WHERE c.id_departamento = :idDepartamento
            AND c.id_estado = 1
            GROUP BY c.id
            UNION
            SELECT c.id, c.numero, c.id_departamento, c.id_estado, 0 AS ocurrencias
            FROM cajas c
            WHERE c.id_departamento = :idDepartamento
            AND c.id_estado = 1
            AND c.id NOT IN (SELECT id_caja FROM turnos)
            ORDER BY ocurrencias ASC, id ASC
            LIMIT 1'
        );
        $stmt->execute([':idDepartamento' => $idDepartamento]);
        $caja = $stmt->fetch();

        if (!$caja) {
            return null;
        }

        $caja = $this->crearDesdeArray($caja);  

        return $caja;
    }

    public function actualizarEstado(Caja $caja): bool {
        $stmt = $this->conexion->prepare('UPDATE cajas SET id_estado = :idEstado WHERE id = :id');
        
        return $stmt->execute([
            'idEstado' => $caja->getEstado(), 
            ':id' => $caja->getId()
        ]);
    }

    //PAGINACION
    public function obtenerPaginacion(int $pagina, int $porPagina): array {
        $inicio = ($pagina - 1) * $porPagina;
        $query = "SELECT * FROM cajas ORDER BY id ASC LIMIT :inicio, :porPagina";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $cajas = [];
        while ($caja = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cajas[] = $this->crearDesdeArray($caja);
        }

        return $cajas;
        
    }

    public function contar(): int {
        $query = "SELECT COUNT(*) FROM cajas";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function crearDesdeArray(array $data): Caja {
        $caja = new Caja(
            $data['numero'],
            $data['id_departamento'],
            $data['id_estado']
        );
        
        $caja->setId($data['id']);

        return $caja;
    }
}

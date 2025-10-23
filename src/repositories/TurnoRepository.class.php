<?php

class TurnoRepository {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstancia()->getConexion();
    }

    public function todos(): array {
        $stmt = $this->conexion->prepare(
            "SELECT t.*, tl.id_estado 
            FROM turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno 
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id) 
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion"
        );
        $stmt->execute();
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }

        return $turnos;
    }

    public function guardar(Turno $turno): void {
        $stmt = $this->conexion->prepare(
            "INSERT INTO turnos (numero, timestamp_solicitud, id_caja, id_cliente) 
            VALUES (:numero, :timestamp_solicitud, :id_caja, :id_cliente)"
        );
        $stmt->execute([
            ':numero' => $turno->getNumero(),
            ':timestamp_solicitud' => $turno->getTimestampSolicitud(),
            ':id_caja' => $turno->getCaja(),
            ':id_cliente' => $turno->getCliente()
        ]);

        $turno->setId($this->conexion->lastInsertId());
    }

    public function guardarEnLog(int $id_turno, int $id_estado, string $timestamp_actualizacion): void {
        $stmt = $this->conexion->prepare(
            "INSERT INTO turnos_log (timestamp_actualizacion, id_turno, id_estado) 
            VALUES (:timestamp_actualizacion, :id_turno, :id_estado)"
        );
        $stmt->execute([
            ':timestamp_actualizacion' => $timestamp_actualizacion,
            ':id_turno' => $id_turno,
            ':id_estado' => $id_estado
        ]);
    }

    public function actualizar(Turno $turno): bool {
        $stmt = $this->conexion->prepare(
            "UPDATE turnos SET 
            numero = :numero,
            timestamp_llamado = :timestamp_llamado,
            timestamp_inicio_atencion = :timestamp_inicio_atencion,
            timestamp_fin_atencion = :timestamp_fin_atencion,
            id_caja = :id_caja,
            id_cliente = :id_cliente
            WHERE id = :id"
        );

        return $stmt->execute([
            ':numero' => $turno->getNumero(),
            ':timestamp_llamado' => $turno->getTimestampLlamado(),
            ':timestamp_inicio_atencion' => $turno->getTimestampInicioAtencion(),
            ':timestamp_fin_atencion' => $turno->getTimestampFinAtencion(),
            ':id_caja' => $turno->getCaja(),
            ':id_cliente' => $turno->getCliente(),
            ':id' => $turno->getId()
        ]);
    }

    public function buscarTurnosActivos(): array {
        $stmt = $this->conexion->prepare(
            "SELECT t.*, tl.id_estado 
            FROM turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno 
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id) 
            AND tl.id_estado NOT IN (4,5)
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion ASC"
        );
        $stmt->execute();
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }

        return $turnos;
    }

    public function buscarTurnoActivoPorCaja(int $id_caja): ?Turno {
        $stmt = $this->conexion->prepare(
            "SELECT t.*, tl.id_estado from turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND tl.id_estado = 3
            AND t.id_caja = :id_caja
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion ASC"
        );
        $stmt->execute([':id_caja' => $id_caja]);
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $turno = $this->crearTurnoDesdeArray($data);  
        $turno->setEstado($data['id_estado']);
        return $turno;
        
    }

    public function obtenerTurnoLlamadoPorCaja(int $id_caja): ?Turno {
        $stmt = $this->conexion->prepare(
            "SELECT t.* from turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND tl.id_estado = 1
            AND t.id_caja = :id_caja
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion ASC"
        );
        $stmt->execute([':id_caja' => $id_caja]);
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return $this->crearTurnoDesdeArray($data);  
        
    }

    public function obtenerTurnoEsperaPorCaja(int $id_caja): ?array {
        $stmt = $this->conexion->prepare(
            "SELECT t.* from turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND tl.id_estado = 2
            AND t.id_caja = :id_caja
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion ASC
            LIMIT 5"
        );
        $stmt->execute([':id_caja' => $id_caja]);
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }

        return $turnos;    
    }

    public function buscarTurnosEnAtencion(?int $idDepartamento = null): array {
        $query = "SELECT t.*, tl.id_estado
            FROM turnos t
            INNER JOIN turnos_log tl ON t.id = tl.id_turno
            WHERE tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND tl.id_estado = 3
            AND DATE(tl.timestamp_actualizacion) = CURDATE()";
        
        if ($idDepartamento !== null) {
            $query .= " AND t.id_caja IN (SELECT id FROM cajas WHERE id_departamento = :departamento)";
        }
        
        $query .= " ORDER BY tl.timestamp_actualizacion ASC";
        
        $stmt = $this->conexion->prepare($query);
        
        if ($idDepartamento !== null) {
            $stmt->execute([':departamento' => $idDepartamento]);
        } else {
            $stmt->execute();
        }
        
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }
        
        return $turnos;
    }

    public function buscarTurnosCompletados(): array {
        $stmt = $this->conexion->prepare(
            "SELECT t.*, tl.id_estado 
            FROM turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno 
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id) 
            AND tl.id_estado = 5
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            ORDER BY tl.timestamp_actualizacion ASC"
        );
        $stmt->execute();
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }

        return $turnos;
    }

    public function turnosPorDepartamento(int $id_departamento): array {
        $stmt = $this->conexion->prepare(
            "SELECT t.*, tl.id_estado 
            FROM turnos t, turnos_log tl 
            WHERE t.id = tl.id_turno 
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND t.id_caja IN (SELECT id FROM cajas WHERE id_departamento = :id_departamento)
            ORDER BY tl.timestamp_actualizacion DESC"
        );
        $stmt->execute([':id_departamento' => $id_departamento]);
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }

        return $turnos;
    }

    public function buscarTurnosEnEspera(?int $idDepartamento = null, ?int $limite = null): array {
        $query = "SELECT t.*, tl.id_estado, c.numero as numero_caja
            FROM turnos t
            INNER JOIN turnos_log tl ON t.id = tl.id_turno
            INNER JOIN cajas c ON t.id_caja = c.id
            WHERE tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND tl.id_estado = 2
            AND DATE(tl.timestamp_actualizacion) = CURDATE()";
        
        if ($idDepartamento !== null) {
            $query .= " AND c.id_departamento = :departamento";
        }
        
        $query .= " ORDER BY tl.timestamp_actualizacion ASC";
        
        if ($limite !== null) {
            $query .= " LIMIT :limite";
        }
        
        $stmt = $this->conexion->prepare($query);
        
        if ($idDepartamento !== null) {
            $stmt->bindValue(':departamento', $idDepartamento, PDO::PARAM_INT);
        }
        
        if ($limite !== null) {
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }
        
        return $turnos;
    }

    public function obtenerPrimerTurnoEnAtencion(int $idDepartamento): ?Turno {
        $stmt = $this->conexion->prepare(
            "SELECT t.*
            FROM turnos t
            INNER JOIN turnos_log tl ON tl.id_turno = t.id
            INNER JOIN cajas c ON c.id = t.id_caja
            WHERE c.id_departamento = 1
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)
            AND DATE(tl.timestamp_actualizacion) = CURDATE()
            AND tl.id_estado = 3
            ORDER BY tl.timestamp_actualizacion ASC
            LIMIT 1"
        );
        $stmt->execute([':departamento' => $idDepartamento]);
        $data = $stmt->fetch();
        
        return $data ? $this->crearTurnoDesdeArray($data) : null;
    }

    public function buscarTurnosPorCaja(int $idCaja, bool $soloActivos = false, bool $soloHoy = false): array {
        $query = "SELECT t.*, tl.id_estado
            FROM turnos t
            INNER JOIN turnos_log tl ON t.id = tl.id_turno
            WHERE t.id_caja = :idCaja
            AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id)";
        
        if ($soloActivos) {
            $query .= " AND tl.id_estado NOT IN (4, 5)";
        }
        
        if ($soloHoy) {
            $query .= " AND DATE(tl.timestamp_actualizacion) = CURDATE()";
        }
        
        $query .= " ORDER BY tl.timestamp_actualizacion ASC";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([':idCaja' => $idCaja]);
        
        $turnos = [];
        while ($data = $stmt->fetch()) {
            $turnos[] = $this->crearTurnoDesdeArray($data);
        }
        
        return $turnos;
    }

    public function actualizarTimestampLlamado(int $idTurno): void {
        $stmt = $this->conexion->prepare(
            "UPDATE turnos SET timestamp_llamado = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $idTurno]);
    }

    public function actualizarTimestampInicioAtencion(int $idTurno): void {
        $stmt = $this->conexion->prepare(
            "UPDATE turnos SET timestamp_inicio_atencion = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $idTurno]);
    }

    public function actualizarTimestampFinAtencion(int $idTurno): void {
        $stmt = $this->conexion->prepare(
            "UPDATE turnos SET timestamp_fin_atencion = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $idTurno]);
    }

    public function obtenerEstadoActual(int $id_turno): ?int {
        $stmt = $this->conexion->prepare(
            "SELECT id_estado 
            FROM turnos_log 
            WHERE id_turno = :id_turno 
            ORDER BY timestamp_actualizacion DESC 
            LIMIT 1"
        );
        $stmt->execute([':id_turno' => $id_turno]);
        $resultado = $stmt->fetch();
        
        return $resultado ? (int)$resultado['id_estado'] : null;
    }

    public function obtenerIdDepartamentoPorTurno(int $idTurno): ?int {
        $stmt = $this->conexion->prepare(
            "SELECT c.id_departamento
            FROM turnos t
            INNER JOIN cajas c ON t.id_caja = c.id
            WHERE t.id = :idTurno"
        );
        $stmt->execute([':idTurno' => $idTurno]);
        $resultado = $stmt->fetch();
        
        return $resultado ? (int)$resultado['id_departamento'] : null;
    }

    public function obtenerSiguienteNumero(int $id_departamento): int {
        $stmt = $this->conexion->prepare("SELECT MAX(t.numero) as max_numero
            FROM turnos t, cajas c
            WHERE t.id_caja = c.id
            AND DATE(t.timestamp_solicitud) = CURDATE()
            AND c.id_departamento = :id_departamento"
        );
        $stmt->execute([':id_departamento' => $id_departamento]);
        $resultado = $stmt->fetch();
        
        return $resultado && $resultado['max_numero'] ? $resultado['max_numero'] + 1 : 1;
    }

    public function buscarSiguienteNumero(int $idDepartamento): ?array {
        $stmt = $this->conexion->prepare("SELECT 
                t.*,
                c.numero as numero_caja, 
                tl.id_estado
                FROM turnos t
                INNER JOIN turnos_log tl ON tl.id_turno = t.id
                INNER JOIN cajas c ON c.id = t.id_caja
                WHERE c.id_departamento = :idDepartamento
                AND tl.id = (SELECT MAX(id) FROM turnos_log WHERE id_turno = t.id) 
                AND DATE(tl.timestamp_actualizacion) = CURDATE()
                AND tl.id_estado = 1
                ORDER BY t.id ASC
                LIMIT 1"
        );
        $stmt->execute([':idDepartamento' => $idDepartamento]);
        $resultado = $stmt->fetch();
        
        return $resultado ?: null;
    }

    public function buscarPorNumero(int $numero): ?Turno { 
        $stmt = $this->conexion->prepare("SELECT * FROM turnos WHERE numero = :numero");
        $stmt->execute([':numero' => $numero]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearTurnoDesdeArray($data);
    }

    public function buscarPorId(int $id): ?Turno { 
        $stmt = $this->conexion->prepare("SELECT * FROM turnos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->crearTurnoDesdeArray($data);
    }

    public function obtenerTiempoEspera(int $idTurno): array {
        // Obtener turno
        $stmtTurno = $this->conexion->prepare("SELECT id_caja, timestamp_solicitud FROM turnos WHERE id = :id");
        $stmtTurno->execute([":id" => $idTurno]);
        $turno = $stmtTurno->fetch();
        
        if (!$turno) {
            return ['error' => 'Turno no encontrado'];
        }
        
        // Contar turnos pendientes adelante
        $stmtPendientes = $this->conexion->prepare("SELECT COUNT(*) as pendientes FROM turnos 
            WHERE id_caja = :id_caja 
            AND id < :id 
            AND timestamp_fin_atencion IS NULL"
        );

        $stmtPendientes->execute([
            ":id_caja" => $turno['id_caja'], 
            ":id" => $idTurno]
        );

        $pendientes = $stmtPendientes->fetch(PDO::FETCH_ASSOC)['pendientes'];
        
        // Calcular tiempo promedio últimos 10 turnos
        $stmtPromedio = $this->conexion->prepare("SELECT AVG(TIMESTAMPDIFF(SECOND, timestamp_inicio_atencion, timestamp_fin_atencion)) as promedio
            FROM turnos
            WHERE id_caja = :id_caja
            AND timestamp_solicitud IS NOT NULL
            AND timestamp_inicio_atencion IS NOT NULL
            ORDER BY id DESC
            LIMIT 10
        ");
        $stmtPromedio->execute([":id_caja" => $turno['id_caja']]);
        $promedioSegundos = $stmtPromedio->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 300;
        
        $tiempoEstimado = $pendientes * $promedioSegundos;
        
        return [
            'pendientes' => (int)$pendientes,
            'tiempo_estimado_segundos' => (int)$tiempoEstimado,
            'tiempo_estimado_minutos' => round($tiempoEstimado / 60)
        ];
    }

    //PAGINACION
    public function obtenerPaginacionConFiltros(int $pagina, int $porPagina, array $filtros): array {
        $inicio = ($pagina - 1) * $porPagina;
        
        $condiciones = ["1=1"];
        $params = [];
        
        // Filtro por departamento
        if (!empty($filtros['id_departamento'])) {
            $condiciones[] = "c.id_departamento = :id_departamento";
            $params[':id_departamento'] = $filtros['id_departamento'];
        }
        
        // Filtro por estado
        if (!empty($filtros['id_estado'])) {
            $condiciones[] = "estado_actual.id_estado = :id_estado";
            $params[':id_estado'] = $filtros['id_estado'];
        }
        
        // Filtro por caja
        if (!empty($filtros['id_caja'])) {
            $condiciones[] = "t.id_caja = :id_caja";
            $params[':id_caja'] = $filtros['id_caja'];
        }
        
        // Filtro por fecha
        if (!empty($filtros['fecha'])) {
            $condiciones[] = "DATE(t.timestamp_solicitud) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        
        // Filtro por número de turno
        if (!empty($filtros['numero_turno'])) {
            $condiciones[] = "t.numero = :numero_turno";
            $params[':numero_turno'] = $filtros['numero_turno'];
        }
        
        $where = implode(" AND ", $condiciones);
        
        $query = "
            SELECT 
                t.*,
                c.numero as numero_caja,
                c.id_departamento,
                d.nombre as nombre_departamento,
                cl.nombre as cliente_nombre,
                cl.apellido_paterno as cliente_apellido_paterno,
                estado_actual.id_estado,
                estado_actual.nombre_estado
            FROM turnos t
            INNER JOIN cajas c ON t.id_caja = c.id
            INNER JOIN departamentos d ON c.id_departamento = d.id
            LEFT JOIN clientes cl ON t.id_cliente = cl.id
            INNER JOIN (
                SELECT 
                    tl.id_turno,
                    tl.id_estado,
                    et.nombre as nombre_estado
                FROM turnos_log tl
                INNER JOIN estado_turno et ON tl.id_estado = et.id
                INNER JOIN (
                    SELECT id_turno, MAX(id) as max_id
                    FROM turnos_log
                    GROUP BY id_turno
                ) latest ON tl.id_turno = latest.id_turno AND tl.id = latest.max_id
            ) estado_actual ON t.id = estado_actual.id_turno
            WHERE $where
            ORDER BY t.timestamp_solicitud DESC
            LIMIT :inicio, :porPagina
        ";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarConFiltros(array $filtros): int {
        $condiciones = ["1=1"];
        $params = [];
        
        if (!empty($filtros['id_departamento'])) {
            $condiciones[] = "c.id_departamento = :id_departamento";
            $params[':id_departamento'] = $filtros['id_departamento'];
        }
        
        if (!empty($filtros['id_caja'])) {
            $condiciones[] = "t.id_caja = :id_caja";
            $params[':id_caja'] = $filtros['id_caja'];
        }
        
        if (!empty($filtros['fecha'])) {
            $condiciones[] = "DATE(t.timestamp_solicitud) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        
        if (!empty($filtros['numero_turno'])) {
            $condiciones[] = "t.numero = :numero_turno";
            $params[':numero_turno'] = $filtros['numero_turno'];
        }
        
        $where = implode(" AND ", $condiciones);
        
        $query = "
            SELECT COUNT(*) as total
            FROM turnos t
            INNER JOIN cajas c ON t.id_caja = c.id
            WHERE $where
        ";
        
        $stmt = $this->conexion->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function registrarCambioEstado(int $idTurno, int $idEstado): void {
        $stmt = $this->conexion->prepare(
            "INSERT INTO turnos_log (id_turno, id_estado, timestamp_actualizacion) 
            VALUES (:idTurno, :idEstado, NOW())"
        );
        
        $stmt->execute([
            ':idTurno' => $idTurno,
            ':idEstado' => $idEstado
        ]);
    }

    private function crearTurnoDesdeArray(array $data): Turno {
        $turno = new Turno(
            $data['numero'],
            $data['timestamp_solicitud'],
            $data['id_cliente'] ?? null,
            $data['id_caja']
        );
        
        $turno->setId($data['id']);

        if (isset($data['timestamp_llamado'])) {
            $turno->setTimestampLlamado($data['timestamp_llamado']);
        }
        
        if (isset($data['timestamp_inicio_atencion'])) {
            $turno->setTimestampInicioAtencion($data['timestamp_inicio_atencion']);
        }
        
        if (isset($data['timestamp_fin_atencion'])) {
            $turno->setTimestampFinAtencion($data['timestamp_fin_atencion']);
        }

        if (isset($data['id_estado'])) {
            $turno->setEstado($data['id_estado']);
        }

        return $turno;
    }
}
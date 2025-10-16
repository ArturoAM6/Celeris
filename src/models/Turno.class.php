<?php

class Turno {
    // Propiedades
    private ?int $id = null;
    private int $numero;
    private string $timestamp_solicitud;
    private ?string $timestamp_llamado = null;
    private ?string $timestamp_inicio_atencion = null;
    private ?string $timestamp_fin_atencion = null;
    private int $id_caja;
    private ?int $id_cliente = null;
    private int $id_estado;

    // Constructor
    public function __construct(int $numero, string $timestamp_solicitud, ?int $id_cliente = null, int $id_caja) {
        $this->numero = $numero;
        $this->timestamp_solicitud = $timestamp_solicitud;
        $this->id_caja = $id_caja;
        $this->id_cliente = $id_cliente;
        $this->id_estado = 2;
    }

    // Getters & Setters
    // Metodo publico que devuelve el ID. Devuelve un Int.
    public function getId(): ?int {
        return $this->id;
    }

    // Metodo publico que establece el ID a un valor determinado. No devuelve ningun valor (void).
    public function setId(int $id): void {
        $this->id = $id;
    }

    // Metodo publico que devuelve el numero del turno. Devuelve un Int.
    public function getNumero(): int {
        return $this->numero;
    }

    // Metodo publico que devuelve la hora y fecha en que fue generado el turno. Devuelve un string.
    public function getTimestampSolicitud(): string {
        return $this->timestamp_solicitud;
    }

    // Metodo publico que devuelve la hora y fecha en que fue llamado el turno. Devuelve un string.
    public function getTimestampLlamado(): ?string {
        return $this->timestamp_llamado;
    }

    // Metodo publico que establece la propiedad timestamp_llamado a un valor determinado. No devuelve nada.
    public function setTimestampLlamado(string $timestamp): void {
        $this->timestamp_llamado = $timestamp;
    }

    // Metodo publico que devuelve la hora y fecha en que inicio la atencion del turno. Devuelve un string.
    public function getTimestampInicioAtencion(): ?string {
        return $this->timestamp_inicio_atencion;
    }

    // Metodo publico que establece la propiedad timestamp_inicio_atencion a un valor determinado. No devuelve nada.
    public function setTimestampInicioAtencion(string $timestamp): void {
        $this->timestamp_inicio_atencion = $timestamp;
    }

    // Metodo publico que devuelve la hora y fecha en que finalizo la atencion del turno. Devuelve un string.
    public function getTimestampFinAtencion(): ?string {
        return $this->timestamp_fin_atencion;
    }

    // Metodo publico que establece la propiedad timestamp_fin_atencion a un valor determinado. No devuelve nada.
    public function setTimestampFinAtencion(string $timestamp): void {
        $this->timestamp_fin_atencion = $timestamp;
    }

    // Metodo publico que devuelve el ID de la caja atendiendo al turno. Devuelve un Int.
    public function getCaja(): int {
        return $this->id_caja;
    }

    // Metodo publico que devuelve el ID de la caja atendiendo al turno. Devuelve un Int.
    public function getCliente(): ?int {
        return $this->id_cliente;
    }

    // Metodo publico que establece la propiedad id_cliente a un valor determinado. No devuelve nada.
    public function setCliente(int $id_cliente): void {
        $this->id_cliente = $id_cliente;
    }

    // Metodo publico que devuelve el ID del estado del turno. Devuelve un Int.
    public function getEstadoId(): ?int {
        return $this->id_estado;
    }

    // Metodo publico que devuelve el nombre del estado del turno. Devuelve un string.
    public function getEstado(): string {
        switch ($this->getEstadoId()) {
            case 1:
                return 'Llamado';
            case 2:
                return 'En espera';
            case 3:
                return 'En atencion';
            case 4:
                return 'Cancelado';
            case 5:
                return 'Finalizado';
            default:
                return 'Desconocido';
        }
    }
}

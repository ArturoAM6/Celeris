<?php

class pantallaGeneralController {
    private TurnoRepository $turnoRepository;

    public function __construct() {
        $this->turnoRepository = new TurnoRepository();
    }

    public function mostrarPantalla(): array {
        $departamentos = array(1, 2, 3, 4);
        $turnos = [];

        foreach ($departamentos as $dep) {
            $turnos[$dep] = [
                'siguiente' => $this->turnoRepository->obtenerSiguienteTurno(1),
                'espera'    => $this->turnoRepository->turnosEnEspera()
            ];
        }

        return $turnos;
    }
}
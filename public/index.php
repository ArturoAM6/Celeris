<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/database/Database.class.php';
require_once __DIR__ . '/../includes/fpdf186/fpdf.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/models/',
        __DIR__ . '/../src/controllers/',
        __DIR__ . '/../src/repositories/',
        __DIR__ . '/../src/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.class.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

session_start();

$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ruta = str_replace('/Celeris/public', '', $ruta);

// =============== Rutas publicas ===============
if ($ruta === '/' || $ruta === '/index.php') {
    require_once __DIR__ . '/../src/views/publicas/inicio.php';
    exit;
}

if ($ruta === '/turno') {
    header("Refresh:30");
    $controller = new TurnoController();
    $controller->pantallaTurnos();
    $controllerp = new pantallaGeneralController();
    $turnos = $controllerp->mostrarPantalla();
    require_once __DIR__ . '/../src/views/publicas/pantalla-turnos.php';
    exit;
}
if ($ruta === '/turno/generar') {
    $controller = new TurnoController();
    $controller->generarTurno();
    exit;
}
if ($ruta === '/turno/consultar') {
    $controller = new TurnoController();
    $controller->consultarTurno();
    exit;
}
if ($ruta === '/turno/ticket' && isset($_GET['id'])) {
    $controller = new TurnoController;
    $controllerCaja = new CajaController;
    $turno = $controller->obtenerTurnoPorId($_GET['id']);
    $caja = $controllerCaja -> obtenerCajaPorId($turno->getCaja());
    require_once __DIR__ . '/../src/views/publicas/ticket-turno.php';
    exit;
}
if ($ruta === '/turno/pdf' && isset($_GET['id'])) {
    $controllerTurno = new TurnoController();
    $controllerCliente = new ClienteController();
    $turno = $controllerTurno->obtenerTurnoPorId($_GET['id']);
    if ($turno->getCliente() !== null) {
        $cliente = $controllerCliente->obtenerClientePorId($turno->getCliente());
    } else {
        $cliente = null;
    }
    $caja = $turno->getCaja();
    $controllerTurno->imprimirTurno($cliente, $caja, $turno);
    exit;
}
if ($ruta === '/turno/tiempo-espera' && isset($_GET['id'])) {
    $controller = new TurnoController;
    $controller->obtenerTiempoEspera($_GET['id']);
    exit;
}

// =============== Autenticacion ===============
if ($ruta === '/login') {
    $controller = new AuthController();
    $controller->login();
    exit;
}

if ($ruta === '/logout') {
    $controller = new AuthController();
    $controller->logout();
    header("Location: " . BASE_URL . "/login");
    exit;
}

// =============== Verificacion para rutas internas ===============
if (!isset($_SESSION['id_empleado'])) {
    header('Location: '. BASE_URL . '/login');
    exit;
}

$empleado = ServicioAutenticacion::getEmpleadoActual();

if (!$empleado) {
    header('Location: '. BASE_URL . '/login');
    exit;
}

// =============== Rutas internas - Administrador ===============
if ($_SESSION['id_rol'] === 1) {
    if ($ruta === "/operador") {
        header("Location: " . BASE_URL . "/admin");
    }
    if ($ruta === '/admin') {
        $empleadoController = new EmpleadoController();
        $empleados = $empleadoController->listarEmpleados();
        $empleadosAsignados = $empleadoController->listarEmpleadosAsignados();
        $empleadosActivos = $empleadoController->listarEmpleadosActivos();
        $cajaController = new CajaController();
        $cajas = $cajaController->listarCajas();
        $HorarioController = new HorarioController();
        $horarios = $HorarioController->listarEmpleados();
        $turnoController = new TurnoController();
        $turnos = $turnoController->listarTurnos();
        $turnosActivos = $turnoController->listarTurnosActivos();
        $turnosEspera = $turnoController->listarTurnosEnEspera();
        $turnosAtencion = $turnoController->listarTurnosEnAtencion();
        $turnosCompletados = $turnoController->listarTurnosCompletados();
        $empleadosPausa = $empleadoController-> empleadosConCajaPausada();
        $datosPaginacion = $turnoController->gestionDeTurnos();
        $turnosPaginados = $datosPaginacion['turnos'] ?? [];
        $paginaActual = $datosPaginacion['paginaActual'] ?? 1;
        $totalPaginas = $datosPaginacion['totalPaginas'] ?? 0;
        $datosPaginacionCajas = $cajaController->gestionDeCajas();
        $cajasPaginadas = $datosPaginacionCajas['cajas'] ?? [];
        $paginaActualCajas = $datosPaginacionCajas['paginaActual'] ?? 1;
        $totalPaginasCajas = $datosPaginacionCajas['totalPaginas'] ?? 0;
        $datosPaginacionEmpleados = $empleadoController->gestionDeEmpleados();
        $empleadosPaginados = $datosPaginacionEmpleados['empleados'] ?? [];
        $paginaActualEmpleados = $datosPaginacionEmpleados['paginaActual'] ?? 1;
        $totalPaginasEmpleados = $datosPaginacionEmpleados['totalPaginas'] ?? 0;
        $datosPaginacionHorarios = $HorarioController->gestionDeHorarios();
        $horariosPaginados = $datosPaginacionHorarios['horarios'] ?? [];
        $paginaActualHorarios = $datosPaginacionHorarios['paginaActual'] ?? 1;
        $totalPaginasHorarios = $datosPaginacionHorarios['totalPaginas'] ?? 0;
        $datosPaginacionDescansos = $empleadoController->gestionDeDescansos();
        $descansosPaginados = $datosPaginacionDescansos['descansos'] ?? [];
        $paginaActualDescansos = $datosPaginacionDescansos['paginaActual'] ?? 1;
        $totalPaginasDescansos = $datosPaginacionDescansos['totalPaginas'] ?? 0;
        require_once __DIR__ . '/../src/views/admin/dashboard.php';
    }
    if ($ruta === '/admin/empleados/filtrar') {
        $empleadoController = new EmpleadoController();
        $empleadoController->filtrarPorDepartamento();
        exit;
    }
    if ($ruta === '/admin/empleados/registrar') {
        $empleadoController = new EmpleadoController();
        $empleadoController->crearEmpleado();
        exit;
    }
    if ($ruta === '/admin/empleados/editar') {
        $empleadoController = new EmpleadoController();
        $empleadoController->editarEmpleado();
        exit;
    }
    if ($ruta === '/admin/empleados/borrar') {
        $empleadoController = new EmpleadoController();
        $empleadoController->desactivarEmpleado();
        exit;
    }
        if ($ruta === '/admin/horario/asignar') {
        $HorarioController = new HorarioController();
        $HorarioController->modificarHorario();
        exit;
    }
    ///COMO ESTE
    if ($ruta === '/admin/cajas/asignar') {
        $cajaController = new CajaController();
        $cajaController->asignarEmpleadoCaja();
        exit;
    }
    ///
    if ($ruta === '/admin/cajas/abrir') {
        $cajaController = new CajaController();
        $cajaController->abrirCaja();
        exit;
    }
    if ($ruta === '/admin/cajas/cerrar') {
        $cajaController = new CajaController();
        $cajaController->cerrarCaja();
        exit;
    }
    if ($ruta === '/admin/cajas/pausar') {
        $cajaController = new CajaController();
        $cajaController->pausarCaja();
        exit;
    }
    if ($ruta === '/admin/cajas/fuera-servicio') {
        $cajaController = new CajaController();
        $cajaController->fueraServicioCaja();
        exit;
    }

    //Empleados pausados
    if ($ruta === '/admin/empleados/pausados') {
        $empleadoController = new EmpleadoController();
        $empleadosPausados = $empleadoController->MostrarDatosDeEmpleados();
        exit;
    }

    //Gestion de turnos




} 
// =============== Rutas internas - Operador ===============
if ($_SESSION['id_rol'] === 2) {
    if ($ruta === "/admin") {
        header("Location: " . BASE_URL . "/operador");
    }
    if ($ruta === '/operador') {
        $controller = new EmpleadoController();
        $empleado = $controller->obtenerEmpleadoPorId($_SESSION["id_empleado"]);
        if (!$controller->validarTipoTurno($empleado)) {
            header("location: " . BASE_URL . "/logout");
            exit;
        }
        $caja = $controller->ObtenerEmpleadoCaja();
        if (!$caja) {
            header("location: " . BASE_URL . "/logout");
            exit;
        }
        $turno_controller = new TurnoController();
        $turno_actual = $turno_controller->obtenerNumeroCajaActiva($caja->getId());
        require_once __DIR__ . '/../src/views/operador/dashboard.php';
    }

    //Pausar una caja
    if ($ruta == '/operador/caja/pausar') {
        $controller = new EmpleadoController();
        $caja = $controller->CambiarEstadoCaja();
        exit;
    }

    //Reanudar una caja
    if ($ruta == '/operador/caja/reanudar') {
        $controller = new EmpleadoController();
        $caja = $controller->CambiarEstadoCaja();
        exit;
    }


}
// =============== Rutas internas - Recepcionista ===============
if ($_SESSION['id_rol'] === 3) {
    if ($ruta === '/recepcionista') {
        $controller = new EmpleadoController();
        require_once __DIR__ . '/../src/views/recepcionista/generar-turno.php';
    }
}
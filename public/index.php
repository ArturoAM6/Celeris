<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/database/Database.class.php';
require_once __DIR__ . '/../includes/tfpdf/tfpdf.php';
require_once __DIR__ . '/../config/googleConfig.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/models/',
        __DIR__ . '/../src/controllers/',
        __DIR__ . '/../src/repositories/',
        __DIR__ . '/../src/services/',
        __DIR__ . '/../src/exceptions/',
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
$ruta = str_replace('/equipo-4/Celeris', '', $ruta);

// =============== Rutas publicas ===============
if ($ruta === '/' || $ruta === '/index.php') {
    session_unset();
    require_once __DIR__ . '/../src/views/publicas/inicio.php';
    exit;
}
if ($ruta === '/turno') {
    header("Refresh:30");
    $controller = new TurnoController();
    $turnos = $controller->mostrarTurnos();
    require_once __DIR__ . '/../src/views/publicas/pantalla-turnos.php';
    exit;
}
if ($ruta === '/turno/generar') {
    $controller = new TurnoController();
    $controller->generarTurno();
    exit;
}
// if ($ruta === '/turno/consultar') {
//     $controller = new TurnoController();
//     $controller->consultarTurno();
//     exit;
// }
if ($ruta === '/turno/ticket' && isset($_GET['id'])) {
    $turnoController = new TurnoController;
    $cajaController = new CajaController;
    $turno = $turnoController->mostrarPorId($_GET['id']);
    $caja = $cajaController -> mostrarPorId($turno->getCaja());
    require_once __DIR__ . '/../src/views/publicas/ticket-turno.php';
    exit;
}
if ($ruta === '/turno/pdf' && isset($_GET['id'])) {
    $turnoController = new TurnoController();
    $turno = $turnoController->mostrarPorId($_GET['id']);
    if ($turno->getCliente() !== null) {
        $cliente = $turnoController->mostrarClientePorId($turno->getCliente());
    } else {
        $cliente = null;
    }
    $caja = $turno->getCaja();
    $turnoController->imprimirTurno($cliente, $caja, $turno);
    exit;
}
if ($ruta === '/turno/subir-drive' && isset($_GET['id'])) {
    $turnoController = new TurnoController();
    $turno = $turnoController->mostrarPorId($_GET['id']);
    if ($turno->getCliente() !== null) {
        $cliente = $turnoController->mostrarClientePorId($turno->getCliente());
    } else {
        $cliente = null;
    }
    $caja = $turno->getCaja();
    $fileId = $turnoController->subirTurnoADrive($cliente, $caja, $turno);
    echo json_encode(['success' => true, 'fileId' => $fileId]);
    exit;
}
if ($ruta === '/turno/tiempo-espera' && isset($_GET['id'])) {
    $controller = new TurnoController;
    $controller->mostrarTiempoEspera($_GET['id']);
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
    exit;
}

// =============== Verificacion para rutas internas ===============
if (!isset($_SESSION['id_empleado'])) {
    header('Location: '. BASE_URL . '/login');
    exit;
}

$empleado = ServicioAutenticacion::obtenerEmpleadoActual();

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
        // Pestaña Empleados
        $empleadoController = new EmpleadoController();
        $empleados = $empleadoController->listarTodos();
        $empleadosAsignados = $empleadoController->listarAsignados();
        $empleadosActivos = $empleadoController->listarActivos();
        $datosPaginacionEmpleados = $empleadoController->gestion();
        $empleadosPaginados = $datosPaginacionEmpleados['empleados'] ?? [];
        $paginaActualEmpleados = $datosPaginacionEmpleados['paginaActual'] ?? 1;
        $totalPaginasEmpleados = $datosPaginacionEmpleados['totalPaginas'] ?? 0;

        // Pestaña Cajas
        $cajaController = new CajaController();
        $datosPaginacionCajas = $cajaController->gestion();
        $cajasPaginadas["cajas"] = $datosPaginacionCajas['cajas'] ?? [];
        $cajasPaginadas["empleados"] = $empleadoController->obtenerEmpleadoPorCaja($cajasPaginadas["cajas"]);
        $paginaActualCajas = $datosPaginacionCajas['paginaActual'] ?? 1;
        $totalPaginasCajas = $datosPaginacionCajas['totalPaginas'] ?? 0;
        
        // Pestaña Turnos
        $turnoController = new TurnoController();
        $turnos = $turnoController->listarTodos();
        $turnosActivos = $turnoController->listarTurnosActivos();
        $turnosEspera = $turnoController->listarTurnosEnEspera();
        $turnosAtencion = $turnoController->listarTurnosEnAtencion();
        $turnosCompletados = $turnoController->listarTurnosCompletados();
        $datosPaginacion = $turnoController->gestion();
        $turnosPaginados["turnos"] = $datosPaginacion['turnos'] ?? [];
        $turnosPaginados["departamentos"] = $turnoController->obtenerIdDepartamentoPorTurno($turnosPaginados["turnos"]);
        $paginaActual = $datosPaginacion['paginaActual'] ?? 1;
        $totalPaginas = $datosPaginacion['totalPaginas'] ?? 0;

        // Pestaña Horarios
        $horarios = $empleadoController->listarHorarios();
        $datosPaginacionHorarios = $empleadoController->gestionHorarios();
        $horariosPaginados = $datosPaginacionHorarios['horarios'] ?? [];
        $paginaActualHorarios = $datosPaginacionHorarios['paginaActual'] ?? 1;
        $totalPaginasHorarios = $datosPaginacionHorarios['totalPaginas'] ?? 0;

        // Pestaña Descansos
        $datosPaginacionDescansos = $empleadoController->gestionDescansos();
        $descansosPaginados = $datosPaginacionDescansos['descansos'] ?? [];
        $paginaActualDescansos = $datosPaginacionDescansos['paginaActual'] ?? 1;
        $totalPaginasDescansos = $datosPaginacionDescansos['totalPaginas'] ?? 0;

        require_once __DIR__ . '/../src/views/admin/dashboard.php';
    }
    if ($ruta === '/admin/empleados/filtrar') {
        $controller = new EmpleadoController();
        $controller->filtrarPorDepartamento();
        exit;
    }
    if ($ruta === '/admin/empleados/registrar') {
        $controller = new EmpleadoController();
        $controller->generar();
        exit;
    }
    if ($ruta === '/admin/empleados/editar') {
        $controller = new EmpleadoController();
        $controller->editar();
        exit;
    }
    if ($ruta === '/admin/empleados/borrar') {
        $controller = new EmpleadoController();
        $controller->desactivar();
        exit;
    }
    if ($ruta === '/admin/horario/asignar') {
        $controller = new EmpleadoController();
        $controller->editarHorario();
        exit;
    }
    if ($ruta === '/admin/cajas/asignar') {
        $controller = new CajaController();
        $controller->editarAsignacion();
        exit;
    }
    if ($ruta === '/admin/cajas/cambiar-estado') {
        $controller = new CajaController();
        $controller->cambiarEstado("admin");
        exit;
    }
} 
// =============== Rutas internas - Operador ===============
if ($_SESSION['id_rol'] === 2) {
    if ($ruta === "/admin") {
        header("Location: " . BASE_URL . "/operador");
    }
    if ($ruta === '/operador') {
        $empleadoController = new EmpleadoController();
        $cajaController = new CajaController();
        $empleado = $empleadoController->mostrarPorId($_SESSION["id_empleado"]);
        $caja = $cajaController->obtenerCajaPorEmpleado($empleado);
        
        if (!$caja) {
            header("location: " . BASE_URL . "/logout");
            exit;
        }

        $turnoController = new TurnoController();
        $turnos = $turnoController->listarTurnosPorCaja($caja->getId());
        $turnoLlamado = $turnos["turnoLlamado"] ?? null;
        $turnoEnAtencion = $turnos["turnoEnAtencion"] ?? null;
        $turnosEnEspera = $turnos["turnoEnEspera"] ?? null;

        $servicioTurnos = new ServicioTurnos();
        $cliente = null;
        if ($turnoLlamado !== null) {
            $cliente = $servicioTurnos->obtenerClientePorTurno($turnoLlamado[0]->getId());
        }
        elseif ($turnoEnAtencion !== null) {
            $cliente = $servicioTurnos->obtenerClientePorTurno($turnoEnAtencion[0]->getId());
        }
        
        require_once __DIR__ . '/../src/views/operador/dashboard.php';
    }
    if ($ruta == '/operador/caja/cambiar-estado') {
        $controller = new CajaController();
        $controller->cambiarEstado("operador");
        exit;
    }
    if ($ruta == '/operador/turno/cambiar-estado') {
        $turnoController = new TurnoController();
        $turno = $turnoController->cambiarEstado();
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
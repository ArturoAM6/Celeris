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
    $controller = new TurnoController();
    $controller->pantallaTurnos();
    require_once __DIR__ . '/../src/views/publicas/pantalla-turnos.php';
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
    $turno = $controller->obtenerTurnoPorId($_GET['id']);
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
    header('Location: '. BASE_URL . '/');
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

    //Empleados pausados
    if ($ruta === '/admin/empleados/pausados') {
        $empleadoController = new EmpleadoController();
        $empleadosPausados = $empleadoController->MostrarDatosDeEmpleados();
        exit;
    }



} 
// =============== Rutas internas - Operador ===============
if ($_SESSION['id_rol'] === 2) {
    if ($ruta === '/operador') {
        $controller = new EmpleadoController();
        $caja = $controller->ObtenerEmpleadoCaja();
        require_once __DIR__ . '/../src/views/operador/dashboard.php';
    }
}
// =============== Rutas internas - Recepcionista ===============
if ($_SESSION['id_rol'] === 3) {
    if ($ruta === '/recepcionista') {
        $controller = new EmpleadoController();
        require_once __DIR__ . '/../src/views/recepcionista/generar-turno.php';
    }
}
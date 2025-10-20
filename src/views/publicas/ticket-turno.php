<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celeris - Banca líquida</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/turnos.css">
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2/qz-tray.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsrsasign@11.0/lib/jsrsasign-all-min.js"></script>
    <script src="<?= BASE_URL ?>/js/qz-config.js"></script>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="nav-logo">
            <a href="<?= BASE_URL ?>" class="navbar-brand" style="text-decoration: none; color: inherit;">
                <img src="<?= BASE_URL ?>/img/flash-on.png" class="rayo " alt="Logo">
                <h2 class="navbar-title">CELERIS</h2>
            </a>
        </div>
    </header>

    <section class="hero-pair">
        <div class="hero-box izquierda">
            <div class="hero-box-header">
                <h2>SU TURNO</h2>
            </div>
            <div class="hero-box-content">
                <h3><?php echo $turno->getNumero() ?></h3>
                    
                    <!-- REVISAR ESTO -->
                    <?php
                    // Obtener el ID de la caja desde el turno
                    // $idCaja = $turno->getCaja();
                    // // Determinar el nombre del departamento según la caja
                    // switch ($idCaja) {
                    //     case 1:
                    //         $nombreDepartamento = 'Cajas';
                    //         break;
                    //     case 5:
                    //         $nombreDepartamento = 'Asesoramiento Financiero';
                    //         break;
                    //     case 3:
                    //         $nombreDepartamento = 'Asociados';
                    //         break;
                    //     case 4:
                    //         $nombreDepartamento = 'Caja Fuerte';
                    //         break;
                    // }
                    // ?>

                    <!-- <p class="departamento">
                        Departamento:
                        <strong><?= htmlspecialchars($nombreDepartamento) ?></strong>
                    </p> -->

                    <div id="tiempo-espera">
                        Cargando...
                    </div>

                    <div id="imprimir-turno" data-turno-id="<?= $turno->getId() ?>"></div>
                </div>
                <!-- DIV PARA DISPARAR IMPRESION AUTOMATICA -->
                <div id="imprimir-turno" data-turno-id="<?= $turno->getId() ?>" ></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        © 2025 Banco Celeris. Todos los derechos reservados.
    </footer>

    <script>const BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/js/turnos.js"></script>
</body>

</html>
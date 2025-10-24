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
                <img src="<?= BASE_URL ?>/img/logo_celeris.png" class="rayo " alt="Logo">
                <h2 class="navbar-title">CELERIS</h2>
            </a>
        </div>
    </header>

    <section class="hero-pair">
    <div class="hero-box izquierda">
        <div class="hero-box-header">
            <h2>TURNO</h2>
        </div>
        <div class="hero-box-content">
            <h3><?php echo $turno->getNumero() ?></h3>
            <p>
                Departamento:
            <?php
            // Obtener el departamento del turno
            $departamentoTurno = $caja->getDepartamento();
            
            switch ($departamentoTurno){
                case 1:
                    echo 'Ventanillas';
                    break;
                case 2:
                    echo 'Asociados';
                    break;
                case 3:
                    echo 'Caja Fuerte';
                    break;
                case 4:
                    echo 'Asesoramiento Financiero';
                    break;
                default:
                    echo 'No Especificado';
                    break;
            }?>
            </p>
            <p>
                Caja: #<?php echo $caja->getNumero(); ?>
            </p>

            <div id="tiempo-espera">
                Cargando...
            </div>

            <div id="imprimir-turno" data-turno-id="<?= $turno->getId() ?>"></div>
        </div>
        <!-- DIV PARA DISPARAR IMPRESION AUTOMATICA -->
        <div id="imprimir-turno" data-turno-id="/<?= $turno->getId() ?>" ></div>
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
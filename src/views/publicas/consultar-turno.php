<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celeris - Banca líquida</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/landing-page.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="nav-logo">
            <a href="landing-page.html" class="navbar-brand" style="text-decoration: none; color: inherit;">
                <img src="img/flash-on.png" class="rayo " alt="Logo">
                <h2 class="navbar-title">CELERIS</h2>
            </a>
        </div>
    </header>

    <section class="hero-pair">
        <div class="hero-box izquierda">
            <div class="hero-box-header">
                <h2>CONSULTAR-TURNO</h2>
            </div>
            <div class="hero-box-content">
                <h3>A001</h3>
                <p>Pasar a ventanilla 3</p>
            </div>
        </div>
        <div class="hero-box derecha">
            <div class="hero-box-header">
                <h2>Próximos Turnos</h2>
            </div>
            <div class="hero-box-content">
                <h2><?php echo $turnoGenerado->getNumero() ?></h2>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        © 2025 Banco Celeris. Todos los derechos reservados.
    </footer>
</body>

</html>
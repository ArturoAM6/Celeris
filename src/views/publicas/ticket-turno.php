<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celeris - Banca líquida</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/turnos.css">
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
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        © 2025 Banco Celeris. Todos los derechos reservados.
    </footer>
</body>

</html>
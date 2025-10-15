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
                <h2>Turnos en atencion</h2>
            </div>
            <div class="hero-box-content">
                <ul>
                    <?php if (empty($turnosEnAtencion)): ?>
                        <li>No hay turnos en atencion</li>
                            <?php else: ?>
                                <?php foreach ($turnosEnAtencion as $turno): ?>
                                    <li>
                                        <?= htmlspecialchars($turno->getNumero()) ?> - Caja <?= htmlspecialchars($turno->getCaja()) ?>
                                    </li>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="hero-box derecha">
            <div class="hero-box-header">
                <h2>Turnos en espera</h2>
            </div>
            <div class="hero-box-content">
                <ul>
                    <?php if (empty($turnosEnEspera)): ?>
                        <li>No hay turnos en espera</li>
                            <?php else: ?>
                                <?php foreach ($turnosEnEspera as $turno): ?>
                                    <li>
                                        <?= htmlspecialchars($turno->getNumero()) ?> - Caja <?= htmlspecialchars($turno->getCaja()) ?>
                                    </li>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        © 2025 Banco Celeris. Todos los derechos reservados.
    </footer>
</body>

</html>
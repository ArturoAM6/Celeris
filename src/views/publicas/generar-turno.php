<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celeris - Banca líquida</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/servicios.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <a href="<?= BASE_URL ?>/" class="navbar-brand" style="text-decoration: none; color: inherit;">
            <img src="<?= BASE_URL ?>/img/logo_celeris.png" alt="logo" class="navbar-logo">
            <h1 class="navbar-title">CELERIS</h1>
            </a>
        </nav>
        <h1>Bienvenido, ¿a qué departamento deseas acceder?</h1>
        <div>
            <form action="<?= BASE_URL ?>/turno/generar" method="post" class="button-grid">
                <?php if (isset($numeroCuenta)): ?>
                    <input type="hidden" name="numero_cuenta" value="<?= htmlspecialchars($numeroCuenta) ?>">
                <?php endif; ?>

                <button type="submit" class="btn" name="id_departamento" value="1">Caja</button>
                <button type="submit" class="btn" name="id_departamento" value="2">Asociados</button>
                <?php if(isset($_SESSION["numeroCuenta"])): ?>
                <button type="submit" class="btn" name="id_departamento" value="3">Caja Fuerte</button>
                <button type="submit" class="btn" name="id_departamento" value="4">Asesoramiento Financiero</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <footer class="footer">
    © 2025 Banco Celeris. Todos los derechos reservados.
    </footer>

</body>

</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celeris - Pantalla General</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pantallaGeneral.css">
</head>
<body>
<header class="navbar">
    <div class="nav-logo">
        <a href="<?= BASE_URL ?>" class="navbar-brand">
            <img src="<?= BASE_URL ?>/img/logo_celeris.png" class="rayo" alt="Logo">
            <h2 class="navbar-title">CELERIS</h2>
        </a>
    </div>
</header>

<div class="main-container">
    <div class="departamentos-grid">

        <?php foreach ($turnos as $departamento => $datos): ?>
            <div class="departamento-card">
                <div class="card-header">
                    <h2><?php
                    switch ($departamento){
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
                            echo 'Desconocido';
                            break;
                    }
                    ?></h2>
                </div>
                <div class="turno-siguiente">
                    <h3>Turno Siguiente</h3>
                    <?php if ($datos['siguiente']): ?>
                        <div class="numero-turno"><?= htmlspecialchars($datos['siguiente']['numero']) ?></div>
                        <div class="ventanilla-info">Caja <?= htmlspecialchars($datos['siguiente']['numero']) ?></div>
                    <?php else: ?>
                        <div class="sin-turnos">Sin turnos llamados</div>
                    <?php endif; ?>
                </div>

                <div class="turnos-espera">
                    <h3>Turnos en espera</h3>
                    <div class="lista-turnos">
                        <?php if (!empty($datos['espera'])): ?>
                            <?php foreach ($datos['espera'] as $turno): ?>
                                <div class="turno-item">Turno: <?= htmlspecialchars($turno->getNumero()) ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="sin-turnos">No hay turnos en espera</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<footer class="footer">
    © 2025 Banco Celeris. Todos los derechos reservados
</footer>
</body>
</html>
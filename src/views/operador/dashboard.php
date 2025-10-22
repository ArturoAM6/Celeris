<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de administrador - CELERIS</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/operador-dashboard.css">
</head>
<body>
  <header>
    <form action="<?= BASE_URL ?>/logout" method="post">
        <button type="submit" name="logout" value="<?= $_SESSION['id_empleado'] ?>" style="background-color: #f4f3f2; color: black;" class="btn">Salir</button>
    </form>
    <h1>Panel de Operador - CELERIS</h1>
  </header>
  
  <section class="container">
    <div class="container-header">
      <h2>Caja: <?php echo $caja->getNumero(); switch($caja->getDepartamento()){
        case 1:
          echo " Ventanillas";
          break;
        case 2:
          echo " Asociados";
          break;
        case 3:
          echo " Caja Fuerte";
          break;
        case 4:
          echo " Asesoramiento Financiero";
          break;
      } ?>  &nbsp; &nbsp; &nbsp; &nbsp; Estado: <?php if ($caja->getEstado() == 1): ?>
          Abierta
        <?php elseif ($caja->getEstado() == 3): ?>
          Pausada
        <?php endif; ?></h2>
      <?php
      // No puede poner en descanso: existe turno llamado o en atención.
          $puedePausar = ($caja->getEstado() == 1) && !isset($turno_llamado) && empty($turno_actual);
      ?>
      <div class="container-acciones">
          <?php if ($puedePausar): ?>
              <form method="post" action='<?= BASE_URL ?>/operador/caja/pausar'>
                  <!-- Nota: en el controlador aceptaremos tanto 'id' como 'id_caja' -->
                  <input type="hidden" name="id_caja" value="<?php echo $caja->getId(); ?>">
                  <input type="hidden" name="id_estado" value="3">
                  <button type="submit" class="btn">DESCANSO</button>
              </form>
          <?php elseif ($caja->getEstado() == 3): ?>
              <form method="post" action='<?= BASE_URL ?>/operador/caja/reanudar'>
                  <input type="hidden" name="id_caja" value="<?php echo $caja->getId(); ?>">
                  <input type="hidden" name="id_estado" value="1">
                  <button type="submit" class="btn">ABRIR CAJA</button>
              </form>
          <?php endif; ?>
      </div>
    </div>
    <div class="container-content">
      <div class="hero-pair">
          <div class="hero izq">
              <div class="hero-header">
                  <h2>Turno actual: <?php echo (empty($turno_actual)) ? "No hay turnos en atencion" : $turno_actual->getNumero(); ?></h2>
                  
                  <?php if ($caja->getEstado() == 1): ?>
                      <?php if (isset($turno_actual) && $turno_actual->getEstadoId() == 3): ?>
                          <form method="post" action='<?= BASE_URL ?>/operador/caja/finalizar'>
                              <input type="hidden" name="id_turno" value="<?php echo $turno_actual->getId(); ?>">
                              <input type="hidden" name="id_estado_turno" value="5">
                              <button type="submit" class="btn">Finalizar</button>
                          </form>
                      <?php elseif (isset($turno_llamado) && $turno_llamado->getEstadoId() == 1): ?>
                          <form method="post" action='<?= BASE_URL ?>/operador/caja/empezar'>
                              <input type="hidden" name="id_turno" value="<?php echo $turno_llamado->getId(); ?>">
                              <input type="hidden" name="id_estado_turno" value="3">
                              <button type="submit" class="btn">Atender Turno</button>
                          </form>
                      <?php endif; ?>
                  <?php else: ?>
                      <div class="sin-accion">Caja en descanso - acciones deshabilitadas</div>
                  <?php endif; ?>
              </div>
              <div class="hero-content">
                  <h3>INFORMACION DEL TURNO</h3>
              </div>
          </div>
          <div class="hero der">
              <div class="hero-header">
                  <h2>Próximos Turnos</h2>
              </div>
              <div class="hero-content">
                  <?php if (empty($turno_espera)): ?>
                      <p>No hay próximos turnos</p>
                  <?php else: ?>
                      <ul>
                          <?php foreach ($turno_espera as $idx => $turno): ?>
                              <li>
                                  Turno N° <?= $turno->getNumero(); ?>
                                  <?php if ($caja->getEstado() == 1 && !isset($turno_llamado) && empty($turno_actual) && $idx === 0): ?>
                                      <form method="post" action='<?= BASE_URL ?>/operador/caja/llamar' class="form-llamar">
                                          <input type="hidden" name="id_turno" value="<?= $turno->getId(); ?>">
                                          <input type="hidden" name="id_estado_turno" value="1">
                                          <input type="hidden" name="id_caja" value="<?= $caja->getId(); ?>">
                                          <button type="submit" class="btn btn-llamar">Llamar turno</button>
                                      </form>
                                  <?php else: ?>
                                      <span class="sin-accion">En espera</span>
                                  <?php endif; ?>
                              </li>
                          <?php endforeach; ?>
                      </ul>
                  <?php endif; ?>
              </div>
          </div>
      </div>
    </div>
  </section>

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>

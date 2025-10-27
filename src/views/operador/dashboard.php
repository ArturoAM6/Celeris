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
    <h1><img src="<?= BASE_URL ?>/img/logo_celeris_blanco.png" class="rayo "alt="Logo"> Panel de Operador</h1>
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
        <div class="container-acciones">
          <!-- // No puede poner en descanso: existe turno llamado o en atención. -->
          <?php if ($caja->getEstado() == 1 && empty($turnoLlamado) && empty($turnoEnAtencion)): ?>
              <form method="post" action='<?= BASE_URL ?>/operador/caja/cambiar-estado'>
                  <input type="hidden" name="id" value="<?php echo $caja->getId(); ?>">
                  <input type="hidden" name="id_estado" value="3">
                  <button type="submit" class="btn">DESCANSO</button>
              </form>
          <?php elseif ($caja->getEstado() == 3): ?>
              <form method="post" action='<?= BASE_URL ?>/operador/caja/cambiar-estado'>
                  <input type="hidden" name="id" value="<?php echo $caja->getId(); ?>">
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
                  <h2>Turno actual: <?php echo (empty($turnoEnAtencion)) ? "No hay turnos en atencion" : $turnoEnAtencion[0]->getNumero(); ?></h2>
                  
                  <?php if ($caja->getEstado() == 1): ?>
                      <?php if (!empty($turnoEnAtencion) && $turnoEnAtencion[0]->getEstadoId() == 3): ?>
                          <form method="post" action='<?= BASE_URL ?>/operador/turno/cambiar-estado'>
                              <input type="hidden" name="id_turno" value="<?php echo $turnoEnAtencion[0]->getId(); ?>">
                              <input type="hidden" name="id_estado" value="5">
                              <button type="submit" class="btn">Finalizar</button>
                          </form>
                      <?php elseif (!empty($turnoLlamado) && $turnoLlamado[0]->getEstadoId() == 1 ): ?>
                          <form method="post" action='<?= BASE_URL ?>/operador/turno/cambiar-estado'>
                              <input type="hidden" name="id_turno" value="<?php echo $turnoLlamado[0]->getId(); ?>">
                              <input type="hidden" name="id_estado" value="3">
                              <button type="submit" class="btn">Atender Turno</button>
                          </form>
                          <form method="post" action='<?= BASE_URL ?>/operador/turno/cambiar-estado'>
                              <input type="hidden" name="id_turno" value="<?php echo $turnoLlamado[0]->getId(); ?>">
                              <input type="hidden" name="id_estado" value="4">
                              <button type="submit" class="btn">Cancelar Turno</button>
                          </form>
                      <?php endif; ?>
                  <?php else: ?>
                      <div class="sin-accion">Caja en descanso - acciones deshabilitadas</div>
                  <?php endif; ?>
              </div>
              <div class="hero-content">
                  <h3>INFORMACION DEL TURNO</h3>
                  <?php if (!empty($turnoEnAtencion) && $turnoEnAtencion[0]->getEstadoId() == 3): ?>
                    <?php if ($cliente): ?>
                        <p><strong>Nombre del cliente:</strong>&nbsp; &nbsp;<?php echo $cliente->getNombreCompleto(); ?></p>
                        <p><strong>Numero de cuenta:</strong>&nbsp; &nbsp;<?php echo $cliente->getNumeroCuenta(); ?></p>
                        <P><strong>Telefono:</strong>&nbsp; &nbsp;<?php echo $cliente->getTelefono(); ?></p>
                        <p><strong>Email:</strong>&nbsp; &nbsp;<?php echo $cliente->getEmail(); ?></p><br>
                    <?php else: ?>
                        <p>Información del cliente no disponible.</p><br>
                    <?php endif; ?>

                  <?php elseif (!empty($turnoLlamado) && $turnoLlamado[0]->getEstadoId() == 1): ?>
                    <?php if ($cliente): ?>
                        <p><strong>Nombre del cliente:</strong>&nbsp; &nbsp;<?php echo $cliente->getNombreCompleto(); ?></p>
                        <p><strong>Numero de cuenta:</strong>&nbsp; &nbsp;<?php echo $cliente->getNumeroCuenta(); ?></p>
                        <P><strong>Telefono:</strong>&nbsp; &nbsp;<?php echo $cliente->getTelefono(); ?></p>
                        <p><strong>Email:</strong>&nbsp; &nbsp;<?php echo $cliente->getEmail(); ?></p><br>
                    <?php else: ?>
                        <p>Información del cliente no disponible.</p><br>
                    <?php endif; ?>
                  <?php else: ?>
                    <p>No hay turnos en atencion.</p>
                  <?php endif; ?>  
              </div>
          </div>
          <div class="hero der">
              <div class="hero-header">
                  <h2>Próximos Turnos</h2>
              </div>
              <div class="hero-content">
                  <?php if (empty($turnosEnEspera)): ?>
                      <p>No hay próximos turnos</p>
                  <?php else: ?>
                      <ul>
                          <?php foreach ($turnosEnEspera as $turno): ?>
                              <li>
                                  <p>Turno N° <?= $turno->getNumero(); ?></p>
                                  <?php if ($caja->getEstado() == 1 && empty($turnoLlamado)): ?>
                                      <form method="post" action='<?= BASE_URL ?>/operador/turno/cambiar-estado' class="form-llamar">
                                          <input type="hidden" name="id_turno" value="<?= $turno->getId(); ?>">
                                          <input type="hidden" name="id_estado" value="1">
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

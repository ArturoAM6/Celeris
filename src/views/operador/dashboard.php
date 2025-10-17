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
      } ?></h2>
      <div class="container-acciones">
        <form method="post">
          <input type="hidden" name="btn-descanso">
          <button type="submit" class="btn">DESCANSO/ABRIR CAJA</button>
        </form>
      </div>
    </div>
    <div class="container-content">
      <div class="hero-pair">
          <div class="hero izq">
              <div class="hero-header">
                  <h2>Turno actual: EL NUMERO O NO HAY TURNOS SIENDO ATENDIDOS</h2>
                  <div class="hero-header-buttons">
                    <form method="post">
                      <input type="hidden" name="btn-finalizar">
                      <button type="submit" class="btn">FINALIZAR SI HAY UN TURNO</button>
                    </form>
                  </div>
              </div>
              <div class="hero-content">
                  <h3>INFORMACION DEL TURNO</h3>
              </div>
          </div>
          <div class="hero der">
              <div class="hero-header">
                  <h2>Próximos Turnos</h2>
                  <!-- PONER UN LLAMAR TURNO SIGUIENTE UN BOTON -->
              </div>
              <div class="hero-content">
                  <ul>
                      <li>LISTA DE LOS TURNOS</li>
                  </ul>
              </div>
          </div>
      </div>
    </div>
  </section>

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>

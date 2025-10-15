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
      <img src="<?= BASE_URL ?>/img/flash-on.png" class="rayo "alt="Logo">
      <h2 class="navbar-title">CELERIS</h2>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero-pair">
    <div class="hero-box left">
      <h1>Tu banca rápida y segura</h1>
      <p>Accede a tu dinero en cualquier momento con la confianza de una conexión protegida.</p>
      <button id="btn-cuentahabiente" class="btn-cta">Cuentahabiente</button>
      <a href="<?= BASE_URL ?>/turno/generar" id="btn-no-cuentahabiente" class="btn-cta">No cuentahabiente</a>

      <div>
        <form action="<?= BASE_URL ?>/turno/generar" method="post" class="cuenta-input" id="cuenta-input">
          <label for="numeroCuenta">Número de cuenta:</label>
          <input type="text" id="numeroCuenta" placeholder="Ingresa tu número de cuenta" required>
          <button type="submit" class="btn-cta" href="<?= BASE_URL ?>/turno/generar">Aceptar</button>
        </form>
      </div>

    </div>
    <div class="hero-box right">
      <img src="<?= BASE_URL ?>/img/autenticacion.jpg" alt="Autenticación de banca" class="hero-icon">
      <h2>Autenticación de banca</h2>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    © 2025 Banco Celeris. Todos los derechos reservados.
  </footer>

<script>
  const btnCuentahabiente = document.getElementById("btn-cuentahabiente");
  const btnNoCuentahabiente = document.getElementById("btn-no-cuentahabiente");
  const cuentaInput = document.getElementById("cuenta-input");

  // Mostrar input si es cuentahabiente
  btnCuentahabiente.addEventListener("click", () => {
    if (btnCuentahabiente.textContent == "Cuentahabiente") {
      cuentaInput.style.display = "flex";
      btnNoCuentahabiente.style.display = "none";
      btnCuentahabiente.textContent = "Regresar";
    } else {
      cuentaInput.style.display = "none";
      btnNoCuentahabiente.style.display = "block";
      btnCuentahabiente.textContent = "Cuentahabiente";
    }
  });
</script>
</body>
</html>
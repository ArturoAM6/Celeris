<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Celeris - Iniciar Sesión</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/iniciar-sesion.css">
  <script src="https://kit.fontawesome.com/d4435a1b16.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="navbar">
    <a href="<?= BASE_URL ?>/" class="nav-logo" style="text-decoration: none;">
      <img src="<?= BASE_URL ?>/img/logo_celeris.png" class="rayo "alt="Logo">
      <h2 class="navbar-title">CELERIS</h2>
    </a>
  </header>
  <div class="login-container">
    <div class="login-box">
      <div class="logo">
        <img src="<?= BASE_URL ?>/img/logo_celeris.png" alt="Logo">
        <h2>Celeris</h2>
        <p>Banca rápida</p>
      </div>
      <h1><?= (isset($error)) ? $error : ""?></h1>
      <h1>Iniciar Sesión</h1>
      <p class="subtitle">Accede a tu cuenta</p>

      <form method="post" action="<?= BASE_URL ?>/login">
        <label for="email">Correo Electrónico</label>
        <input type="email" name="email" id="email" placeholder="Ingresa tu correo">

        <label for="password">Contraseña</label>
        <div class="password-box">
          <input type="password" name="password" id="password" placeholder="Ingresa tu contraseña">
          <i class="fa-solid fa-eye toggle-icon" id="togglePassword"></i>
        </div>

          <script>
            const toggleIcon = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');

            toggleIcon.addEventListener('click', () => {
              const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
              passwordField.setAttribute('type', type);

              toggleIcon.classList.toggle('fa-eye');
              toggleIcon.classList.toggle('fa-eye-slash');
            });
          </script>
            <button type="submit">Iniciar Sesión</button>
        </div>
      </form>

    </div>
  </div>
      <footer class="footer">
        © 2025 Celeris. Todos los derechos reservados.
      </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar sesión — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/Login_Styles.css">
</head>
<body>

<div class="login-container">
  <div class="login-header">
    <div class="login-brand">Iskalli<span>.</span></div>
    <h1 class="login-title">Iniciar Sesión</h1>
    <p class="login-subtitle">Ingresa tus credenciales y accede al área administrativa del sistema.</p>
  </div>

  <form method="POST" action="">
    <?php if (!empty($error)): ?>
      <div class="error-message">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="8" cy="8" r="6"/>
          <path d="M8 4V8M8 10V12"/>
        </svg>
        <div class="error-text"><?= htmlspecialchars($error) ?></div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label class="form-label" for="email">Correo Electrónico</label>
      <input
        type="email"
        id="email"
        name="email"
        class="form-input"
        placeholder="admin@iskali.com"
        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        required
      >
    </div>

    <div class="form-group">
      <label class="form-label" for="password">Contraseña</label>
      <input
        type="password"
        id="password"
        name="password"
        class="form-input"
        placeholder="••••••••"
        required
      >
    </div>

    <button type="submit" class="btn-login">
      Iniciar Sesión
    </button>

    <a href="<?= BASE_URL ?>/index.php?pagina=usuarios&accion=nuevo" class="btn-create-account">
      Crear Cuenta
    </a>
  </form>

  <div class="login-footer">
    <a href="<?= BASE_URL ?>/index.php?pagina=inicio">← Volver al inicio</a>
  </div>
</div>

</body>
</html>

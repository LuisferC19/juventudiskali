<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar sesión — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/iskalli.css">
</head>
<body>

<div class="login-wrap">

  <!-- ——— Columna izquierda: branding + estadísticas ——— -->
  <div class="login-left">
    <div style="position:relative;z-index:1;">
      <div class="login-brand"><?= APP_NAME ?></div>
      <div class="login-tagline"><?= APP_DESC ?></div>

      <!-- Estadísticas decorativas -->
      <div class="login-stats">
        <div class="ls-item">
          <div class="ls-num">248</div>
          <div class="ls-lbl">Beneficiarios</div>
        </div>
        <div class="ls-item">
          <div class="ls-num">86</div>
          <div class="ls-lbl">Donadores</div>
        </div>
        <div class="ls-item">
          <div class="ls-num">12</div>
          <div class="ls-lbl">Campañas</div>
        </div>
      </div>

      <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,.08);">
        <p style="font-size:11px;color:rgba(255,255,255,.25);">
          <?= APP_CIUDAD ?> · v<?= APP_VERSION ?> · Marzo <?= APP_ANIO ?>
        </p>
      </div>
    </div>
  </div><!-- /login-left -->

  <!-- ——— Columna derecha: formulario de login ——— -->
  <div class="login-right">
    <div class="login-form">
      <h2>Iniciar sesión</h2>
      <p>Ingresa tus credenciales para acceder al sistema</p>
      <!-- 🔐 CREDENCIALES DE PRUEBA — Quita este bloque en producción -->
      <div style="font-size:11px;color:var(--muted);background:rgba(10,175,160,.07);border:1px solid rgba(10,175,160,.2);border-radius:6px;padding:.5rem .75rem;margin-top:.5rem;">
        <strong>Demo:</strong> laura.admin@iskalli.mx &nbsp;|&nbsp; admin123
      </div>

      <!-- Mensaje de error (si aplica) -->
      <?php if (!empty($error)): ?>
        <div class="login-error show"><?= htmlspecialchars($error) ?></div>
      <?php else: ?>
        <div class="login-error" id="login-error-js"></div>
      <?php endif; ?>

      <!-- Formulario POST al mismo router -->
      <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=login" id="form-login">
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? 'laura.admin@iskalli.mx') ?>"
                 placeholder="correo@iskalli.mx" required>
        </div>
        <div class="form-group" style="margin-top:.75rem;">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password"
                 value="admin123"
                 placeholder="••••••••" required>
        </div>
        <div class="forgot">¿Olvidaste tu contraseña?</div>
        <button type="submit" class="login-btn">Entrar al sistema</button>
      </form>

      <p style="font-size:11px;color:var(--muted);text-align:center;margin-top:1rem;">
      </p>
    </div>
  </div><!-- /login-right -->

</div><!-- /login-wrap -->

<script src="<?= BASE_URL ?>/public/js/iskalli.js"></script>
</body>
</html>
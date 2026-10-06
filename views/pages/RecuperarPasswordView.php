<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/Login_Styles.css">
<style>
  .recover-icon { width: 64px; height: 64px; background: rgba(10,175,160,.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
  .recover-icon svg { width: 30px; height: 30px; color: var(--verde); }
  .step-chips { display: flex; gap: 8px; justify-content: center; margin-bottom: 20px; flex-wrap: wrap; }
  .step-chip { background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 50px; padding: 4px 12px; font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 5px; }
  .step-chip .step-num { width: 18px; height: 18px; border-radius: 50%; background: var(--verde); color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .login-link-note { text-align: center; font-size: 14px; color: var(--muted); margin-top: 16px; }
  .login-link-note a { color: var(--verde); font-weight: 600; text-decoration: none; }
  .login-link-note a:hover { text-decoration: underline; }
  .error-message { margin-bottom: 20px; }
  .success-box { background: rgba(10,175,160,.08); border: 1px solid rgba(10,175,160,.25); border-radius: 14px; padding: 20px; text-align: center; margin-top: 16px; color: var(--verde-d); line-height: 1.6; }
</style>
</head>
<body>

<div class="login-container">
  <div class="login-header">
    <div class="login-brand">Juventud Iskali<span>.</span></div>
    <div class="recover-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
      </svg>
    </div>
    <h1 class="login-title">Recuperar contraseña</h1>
    <p class="login-subtitle">Te enviaremos un enlace para restablecer tu contraseña.</p>
  </div>

  <div class="step-chips">
    <div class="step-chip"><span class="step-num">1</span> Ingresa tu correo</div>
    <div class="step-chip"><span class="step-num">2</span> Revisa tu bandeja</div>
    <div class="step-chip"><span class="step-num">3</span> Restablece</div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="error-message" role="alert">
      <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <circle cx="10" cy="10" r="8"></circle>
        <path d="M10 5v5M10 13h.01"></path>
      </svg>
      <div class="error-text"><?= e($error) ?></div>
    </div>
  <?php endif; ?>

  <?php if (empty($error) && !empty($success)): ?>
    <div class="success-box" role="status"><?= e($success) ?></div>
  <?php else: ?>
    <form id="recoverForm" method="POST" action="<?= BASE_URL ?>/index.php?pagina=recuperar_password">
      <?= csrfField() ?>
      <div class="form-group">
        <label class="form-label" for="email">Correo electrónico registrado</label>
        <input type="email" id="email" name="email" class="form-input" value="<?= e($email ?? '') ?>" placeholder="tu@correo.com" required>
      </div>
      <button type="submit" class="btn-login">Enviar enlace de recuperación</button>
    </form>
  <?php endif; ?>

  <p class="login-link-note"><a href="<?= BASE_URL ?>/index.php?pagina=login">← Regresar al inicio de sesión</a></p>

  <div class="login-footer">
    <a href="<?= BASE_URL ?>/index.php?pagina=inicio">← Volver al inicio</a>
  </div>
</div>

</body>
</html>
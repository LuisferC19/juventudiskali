<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear cuenta — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/Login_Styles.css">
<style>
  /* Estilos extra para formulario de registro */
  .register-note {
    background: rgba(10,175,160,.07);
    border: 1px solid rgba(10,175,160,.2);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
  }
  .register-note svg {
    width: 18px; height: 18px;
    color: var(--verde);
    flex-shrink: 0;
    margin-top: 1px;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }
  @media (max-width: 480px) {
    .form-row { grid-template-columns: 1fr; }
  }
  .password-hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 5px;
  }
  .login-link-note {
    text-align: center;
    font-size: 14px;
    color: var(--muted);
    margin-top: 16px;
  }
  .login-link-note a {
    color: var(--verde);
    font-weight: 600;
    text-decoration: none;
  }
  .login-link-note a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="login-container" style="max-width:480px;">
  <div class="login-header">
    <div class="login-brand">Juventud Iskali<span>.</span></div>
    <h1 class="login-title">Crear cuenta</h1>
    <p class="login-subtitle">Únete al equipo de Fundación Iskali</p>
  </div>

  <!-- Aviso informativo -->
  <div class="register-note">
    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
      <circle cx="8" cy="8" r="6"/>
      <path d="M8 6V8M8 10V10.5"/>
    </svg>
    <span>El acceso al sistema es aprobado por un administrador. Al registrarte, tu cuenta quedará pendiente de activación.</span>
  </div>

  <form method="POST" action="" id="registerForm">

    <?= csrfField() ?>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="nombre">Nombre(s)</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          class="form-input"
          placeholder="Ana"
          required
        >
      </div>
      <div class="form-group">
        <label class="form-label" for="apellido">Apellido(s)</label>
        <input
          type="text"
          id="apellido"
          name="apellido"
          class="form-input"
          placeholder="García"
          required
        >
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="email">Correo Electrónico</label>
      <input
        type="email"
        id="email"
        name="email"
        class="form-input"
        placeholder="tu@correo.com"
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
        placeholder="Mínimo 8 caracteres"
        required
      >
      <p class="password-hint">Usa al menos 8 caracteres, una mayúscula y un número.</p>
    </div>

    <div class="form-group">
      <label class="form-label" for="password_confirm">Confirmar contraseña</label>
      <input
        type="password"
        id="password_confirm"
        name="password_confirm"
        class="form-input"
        placeholder="Repite tu contraseña"
        required
      >
    </div>

    <button type="submit" class="btn-login">
      Solicitar acceso
    </button>

    <p class="login-link-note">
      ¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/index.php?pagina=login">Inicia sesión</a>
    </p>

  </form>

  <div class="login-footer">
    <a href="<?= BASE_URL ?>/index.php?pagina=inicio">← Volver al inicio</a>
  </div>
</div>

<script>
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
  const pwd = document.getElementById('password').value;
  const confirm = document.getElementById('password_confirm').value;
  if (pwd !== confirm) {
    e.preventDefault();
    alert('Las contraseñas no coinciden. Por favor verifica.');
  }
});
</script>

</body>
</html>
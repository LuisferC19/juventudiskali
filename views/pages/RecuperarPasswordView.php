<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/Login_Styles.css">
<style>
  /* Estilos extra para recuperación */
  .recover-icon {
    width: 64px; height: 64px;
    background: rgba(10,175,160,.1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
  }
  .recover-icon svg {
    width: 30px; height: 30px;
    color: var(--verde);
  }
  .success-box {
    display: none;
    background: rgba(10,175,160,.08);
    border: 1px solid rgba(10,175,160,.25);
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    margin-top: 16px;
  }
  .success-box.show { display: block; }
  .success-box h3 {
    font-family: 'Syne', sans-serif;
    font-size: 17px;
    color: var(--verde-d);
    margin-bottom: 8px;
  }
  .success-box p {
    font-size: 14px;
    color: var(--muted);
    line-height: 1.6;
  }
  .success-icon {
    width: 48px; height: 48px;
    background: rgba(10,175,160,.12);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
  }
  .success-icon svg { width: 24px; height: 24px; color: var(--verde); }
  .step-chips {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .step-chip {
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: 50px;
    padding: 4px 12px;
    font-size: 12px;
    color: var(--muted);
    display: flex; align-items: center; gap: 5px;
  }
  .step-chip .step-num {
    width: 18px; height: 18px; border-radius: 50%;
    background: var(--verde);
    color: #fff;
    font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
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

<div class="login-container">
  <div class="login-header">
    <div class="login-brand">Juventud Iskali<span>.</span></div>

    <!-- Ícono de llave/correo -->
    <div class="recover-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="2" y="7" width="20" height="14" rx="2"/>
        <path d="M16 7V5a4 4 0 0 0-8 0v2"/>
        <circle cx="12" cy="14" r="1.5" fill="currentColor" stroke="none"/>
      </svg>
    </div>

    <h1 class="login-title">Recuperar contraseña</h1>
    <p class="login-subtitle">Te enviaremos un enlace para restablecer tu contraseña.</p>
  </div>

  <!-- Pasos visuales -->
  <div class="step-chips">
    <div class="step-chip"><span class="step-num">1</span> Ingresa tu correo</div>
    <div class="step-chip"><span class="step-num">2</span> Revisa tu bandeja</div>
    <div class="step-chip"><span class="step-num">3</span> Restablece</div>
  </div>

  <!-- Formulario -->
  <form id="recoverForm" method="POST" action="">

    <?= csrfField() ?>

    <div class="form-group">
      <label class="form-label" for="email">Correo Electrónico registrado</label>
      <input
        type="email"
        id="email"
        name="email"
        class="form-input"
        placeholder="tu@correo.com"
        required
      >
    </div>

    <button type="submit" class="btn-login">
      Enviar enlace de recuperación
    </button>

    <p class="login-link-note" style="margin-top:14px;">
      <a href="<?= BASE_URL ?>/index.php?pagina=login">← Regresar al inicio de sesión</a>
    </p>

  </form>

  <!-- Mensaje de éxito (se muestra tras enviar) -->
  <div class="success-box" id="successBox">
    <div class="success-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <h3>¡Correo enviado!</h3>
    <p>Si el correo está registrado, recibirás un enlace para restablecer tu contraseña en los próximos minutos. Revisa también tu carpeta de spam.</p>
  </div>

  <div class="login-footer">
    <a href="<?= BASE_URL ?>/index.php?pagina=inicio">← Volver al inicio</a>
  </div>
</div>

<script>
/* Vista estática: simula el envío mostrando el mensaje de éxito.
   Cuando implementes el backend real, elimina este bloque. */
document.getElementById('recoverForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  if (!email) return;

  // Oculta el form y muestra éxito
  this.style.display = 'none';
  document.getElementById('successBox').classList.add('show');
});
</script>

</body>
</html>
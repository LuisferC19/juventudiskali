<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar sesión — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/iskalli.css">
<style>
  :root {
    --verde:  #0aafa0;
    --verde-d:#067d72;
    --amber:  #e8a020;
    --dark:   #0d1117;
    --dark2:  #151b23;
    --card:   #1c2430;
    --border: rgba(255,255,255,.08);
    --text:   #e8eaf0;
    --muted:  #8b949e;
    --danger: #f44336;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--dark);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: stretch;
  }

  /* ═══ PANEL IZQUIERDO ═══ */
  .lp-left {
    flex: 1;
    background:
      radial-gradient(ellipse 80% 60% at 30% 20%, rgba(10,175,160,.22), transparent 60%),
      radial-gradient(ellipse 50% 40% at 80% 80%, rgba(232,160,32,.08), transparent 60%),
      var(--dark2);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 48px;
    position: relative;
    overflow: hidden;
  }

  .lp-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
      linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 40px 40px;
  }

  .lp-left-content { position: relative; z-index: 1; }

  .lp-brand {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 28px;
    color: var(--verde);
    letter-spacing: -1px;
    margin-bottom: 4px;
  }
  .lp-brand span { color: var(--text); }

  .lp-tagline {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 52px;
  }

  .lp-headline {
    font-family: 'Syne', sans-serif;
    font-size: clamp(28px, 3.5vw, 44px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -1.5px;
    margin-bottom: 16px;
  }
  .lp-headline .accent { color: var(--verde); }

  .lp-desc {
    font-size: 15px;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.6;
    max-width: 380px;
    margin-bottom: 48px;
  }

  /* Estadísticas decorativas */
  .lp-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .lp-stat {
    background: rgba(255,255,255,.03);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
  }

  .lp-stat-num {
    font-family: 'Syne', sans-serif;
    font-size: 26px;
    font-weight: 800;
    color: var(--verde);
  }

  .lp-stat-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

  .lp-footer {
    position: relative;
    z-index: 1;
    font-size: 11px;
    color: rgba(139,148,158,.4);
  }

  /* ═══ PANEL DERECHO ═══ */
  .lp-right {
    width: 460px;
    min-width: 360px;
    background: var(--dark);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 40px;
    border-left: 1px solid var(--border);
  }

  .lp-form-wrap { width: 100%; max-width: 360px; }

  .lp-tabs {
    display: flex;
    gap: 0;
    border-radius: 10px;
    background: var(--dark2);
    border: 1px solid var(--border);
    padding: 4px;
    margin-bottom: 32px;
  }

  .lp-tab {
    flex: 1;
    padding: 10px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    color: var(--muted);
    border-radius: 7px;
    cursor: pointer;
    transition: all .2s;
    border: none;
    background: transparent;
    font-family: inherit;
  }
  .lp-tab.active {
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--border);
  }

  .lp-form-title {
    font-family: 'Syne', sans-serif;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 6px;
  }

  .lp-form-subtitle { font-size: 13px; color: var(--muted); margin-bottom: 28px; }

  /* Error/success message */
  .lp-alert {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 20px;
    display: none;
  }
  .lp-alert.error {
    background: rgba(244,67,54,.1);
    border: 1px solid rgba(244,67,54,.3);
    color: #f44336;
    display: block;
  }
  .lp-alert.success {
    background: rgba(10,175,160,.1);
    border: 1px solid rgba(10,175,160,.3);
    color: var(--verde);
    display: block;
  }

  /* Demo box */
  .lp-demo {
    background: rgba(10,175,160,.06);
    border: 1px solid rgba(10,175,160,.18);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 24px;
  }
  .lp-demo strong { color: var(--verde); }

  /* Form fields */
  .form-group { margin-bottom: 18px; }

  .form-group label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 7px;
  }

  .form-group input,
  .form-group select {
    width: 100%;
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 11px 14px;
    color: var(--text);
    font-family: inherit;
    font-size: 14px;
    outline: none;
    transition: border-color .2s;
  }

  .form-group input:focus,
  .form-group select:focus { border-color: var(--verde); }

  /* Inline validation */
  .field-error {
    font-size: 11px;
    color: var(--danger);
    margin-top: 5px;
    display: none;
  }
  .field-error.show { display: block; }

  .form-group input.invalid { border-color: var(--danger); }
  .form-group input.valid   { border-color: #4caf50; }

  /* Forgot */
  .lp-forgot {
    text-align: right;
    margin-top: -10px;
    margin-bottom: 20px;
  }
  .lp-forgot a { font-size: 12px; color: var(--muted); text-decoration: none; }
  .lp-forgot a:hover { color: var(--verde); }

  /* Submit */
  .btn-submit {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background: var(--verde);
    color: #fff;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    margin-top: 8px;
  }
  .btn-submit:hover { background: var(--verde-d); transform: translateY(-1px); }

  /* Divider */
  .lp-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0;
    font-size: 12px;
    color: var(--muted);
  }
  .lp-divider::before,
  .lp-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  /* Enlace de registro */
  .lp-switch {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: var(--muted);
  }
  .lp-switch a {
    color: var(--verde);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }
  .lp-switch a:hover { text-decoration: underline; }

  /* Back to landing */
  .lp-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
    text-decoration: none;
    margin-bottom: 32px;
    transition: color .2s;
  }
  .lp-back:hover { color: var(--text); }

  /* Panel de registro (oculto por defecto) */
  #panel-registro { display: none; }

  @media (max-width: 768px) {
    .lp-left { display: none; }
    .lp-right { width: 100%; border-left: none; }
  }
</style>
</head>
<body>

<!-- ═══ PANEL IZQUIERDO ═══ -->
<div class="lp-left">
  <div class="lp-left-content">
    <div class="lp-brand">isk<span>ali</span></div>
    <div class="lp-tagline">Sistema de Gestión de Donaciones</div>

    <div class="lp-headline">
      Conectando <span class="accent">donadores</span><br>
      con quienes<br>más lo necesitan
    </div>

    <p class="lp-desc">
      Gestiona campañas, beneficiarios, inventario y voluntarios desde un solo sistema 
      diseñado para organizaciones sociales en Puebla.
    </p>

    <div class="lp-stats">
      <div class="lp-stat">
        <div class="lp-stat-num">248</div>
        <div class="lp-stat-lbl">Beneficiarios</div>
      </div>
      <div class="lp-stat">
        <div class="lp-stat-num">86</div>
        <div class="lp-stat-lbl">Donadores</div>
      </div>
      <div class="lp-stat">
        <div class="lp-stat-num">15</div>
        <div class="lp-stat-lbl">Campañas</div>
      </div>
    </div>
  </div>

  <div class="lp-footer">
    <?= APP_CIUDAD ?? 'Puebla' ?> · v<?= APP_VERSION ?? '2.0' ?> · 2026
  </div>
</div>

<!-- ═══ PANEL DERECHO ═══ -->
<div class="lp-right">
  <div class="lp-form-wrap">

    <a href="<?= BASE_URL ?>/index.php?pagina=inicio" class="lp-back">
      ← Volver al inicio
    </a>

    <!-- Tabs login / registro -->
    <div class="lp-tabs">
      <button class="lp-tab active" id="tab-login" onclick="switchTab('login')">Iniciar sesión</button>
      <button class="lp-tab" id="tab-registro" onclick="switchTab('registro')">Crear cuenta</button>
    </div>

    <!-- ═══ FORMULARIO LOGIN ═══ -->
    <div id="panel-login">
      <div class="lp-form-title">Bienvenido de nuevo</div>
      <p class="lp-form-subtitle">Ingresa tus credenciales para acceder</p>

      <!-- Demo box — quitar en producción -->
      <div class="lp-demo">
        <strong>Demo:</strong> laura.admin@iskalli.mx &nbsp;|&nbsp; admin123
      </div>

      <?php if (!empty($error)): ?>
        <div class="lp-alert error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=login" id="form-login" novalidate>

        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? 'laura.admin@iskalli.mx') ?>"
                 placeholder="correo@iskalli.mx" autocomplete="email">
          <div class="field-error" id="err-email">Ingresa un correo electrónico válido.</div>
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password"
                 value="admin123"
                 placeholder="••••••••" autocomplete="current-password">
          <div class="field-error" id="err-password">La contraseña es requerida.</div>
        </div>

        <div class="lp-forgot"><a href="#">¿Olvidaste tu contraseña?</a></div>

        <button type="submit" class="btn-submit" onclick="return validarLogin(event)">
          Entrar al sistema
        </button>
      </form>

      <div class="lp-divider">o</div>
      <div class="lp-switch">
        ¿No tienes cuenta? <a onclick="switchTab('registro')">Crear cuenta gratis</a>
      </div>
    </div>

    <!-- ═══ FORMULARIO REGISTRO ═══ -->
    <div id="panel-registro">
      <div class="lp-form-title">Crear cuenta</div>
      <p class="lp-form-subtitle">Completa los datos para solicitar acceso</p>

      <div id="msg-registro"></div>

      <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=registro" id="form-registro" novalidate>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label for="reg-nombre">Nombre</label>
            <input type="text" id="reg-nombre" name="nombre" placeholder="Tu nombre" autocomplete="given-name">
            <div class="field-error" id="err-nombre">Solo letras y espacios.</div>
          </div>
          <div class="form-group">
            <label for="reg-apellido">Apellido</label>
            <input type="text" id="reg-apellido" name="apellido" placeholder="Tu apellido" autocomplete="family-name">
            <div class="field-error" id="err-apellido">Solo letras y espacios.</div>
          </div>
        </div>

        <div class="form-group">
          <label for="reg-email">Correo electrónico</label>
          <input type="email" id="reg-email" name="email" placeholder="correo@ejemplo.com" autocomplete="email">
          <div class="field-error" id="err-reg-email">Ingresa un correo válido.</div>
        </div>

        <div class="form-group">
          <label for="reg-password">Contraseña</label>
          <input type="password" id="reg-password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
          <div class="field-error" id="err-reg-password">Mínimo 8 caracteres.</div>
        </div>

        <div class="form-group">
          <label for="reg-password2">Confirmar contraseña</label>
          <input type="password" id="reg-password2" name="password2" placeholder="Repite tu contraseña" autocomplete="new-password">
          <div class="field-error" id="err-reg-password2">Las contraseñas no coinciden.</div>
        </div>

        <button type="submit" class="btn-submit" onclick="return validarRegistro(event)">
          Solicitar acceso
        </button>
      </form>

      <div class="lp-divider">o</div>
      <div class="lp-switch">
        ¿Ya tienes cuenta? <a onclick="switchTab('login')">Iniciar sesión</a>
      </div>
    </div>

  </div>
</div>

<script src="<?= BASE_URL ?>/public/js/iskalli.js"></script>
<script>
/* ─── Tabs ─── */
function switchTab(tab) {
  const isLogin = tab === 'login';
  document.getElementById('panel-login').style.display    = isLogin ? 'block' : 'none';
  document.getElementById('panel-registro').style.display = isLogin ? 'none'  : 'block';
  document.getElementById('tab-login').classList.toggle('active', isLogin);
  document.getElementById('tab-registro').classList.toggle('active', !isLogin);
}

// Si la URL tiene ?accion=registro, abrir el tab de registro
if (new URLSearchParams(location.search).get('accion') === 'registro') {
  switchTab('registro');
}

/* ─── Helpers de validación ─── */
function showErr(id, msg) {
  const el = document.getElementById(id);
  if (el) { el.textContent = msg || el.textContent; el.classList.add('show'); }
}
function clearErr(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('show');
}
function markField(input, ok) {
  input.classList.toggle('valid', ok);
  input.classList.toggle('invalid', !ok);
}

/* ─── Validación en tiempo real ─── */
document.getElementById('email').addEventListener('input', function() {
  const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim());
  markField(this, ok);
  ok ? clearErr('err-email') : showErr('err-email');
});

document.getElementById('password').addEventListener('input', function() {
  const ok = this.value.length >= 1;
  markField(this, ok);
  ok ? clearErr('err-password') : showErr('err-password');
});

// Registro - nombre
['reg-nombre', 'reg-apellido'].forEach(id => {
  const input = document.getElementById(id);
  if (!input) return;
  input.addEventListener('input', function() {
    // Bloquear caracteres que no sean letras, tildes, ñ o espacios
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
    const ok = this.value.trim().length >= 2;
    markField(this, ok);
    ok ? clearErr('err-' + id.replace('reg-','')) : showErr('err-' + id.replace('reg-',''));
  });
  // Evitar pegar caracteres inválidos
  input.addEventListener('paste', function(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text');
    const clean = text.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
    this.value = clean;
    this.dispatchEvent(new Event('input'));
  });
});

document.getElementById('reg-email')?.addEventListener('input', function() {
  const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim());
  markField(this, ok);
  ok ? clearErr('err-reg-email') : showErr('err-reg-email');
});

document.getElementById('reg-password')?.addEventListener('input', function() {
  const ok = this.value.length >= 8;
  markField(this, ok);
  ok ? clearErr('err-reg-password') : showErr('err-reg-password', 'Mínimo 8 caracteres.');
  // Re-validar confirmación
  const p2 = document.getElementById('reg-password2');
  if (p2 && p2.value) p2.dispatchEvent(new Event('input'));
});

document.getElementById('reg-password2')?.addEventListener('input', function() {
  const p1 = document.getElementById('reg-password').value;
  const ok = this.value === p1 && this.value.length >= 8;
  markField(this, ok);
  ok ? clearErr('err-reg-password2') : showErr('err-reg-password2', 'Las contraseñas no coinciden.');
});

/* ─── Validación al enviar LOGIN ─── */
function validarLogin(e) {
  e.preventDefault();
  let ok = true;

  const email    = document.getElementById('email');
  const password = document.getElementById('password');

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    showErr('err-email'); markField(email, false); ok = false;
  }
  if (!password.value) {
    showErr('err-password'); markField(password, false); ok = false;
  }

  if (ok) document.getElementById('form-login').submit();
}

/* ─── Validación al enviar REGISTRO ─── */
function validarRegistro(e) {
  e.preventDefault();
  let ok = true;

  const nombre   = document.getElementById('reg-nombre');
  const apellido = document.getElementById('reg-apellido');
  const email    = document.getElementById('reg-email');
  const pass     = document.getElementById('reg-password');
  const pass2    = document.getElementById('reg-password2');

  if (!nombre || nombre.value.trim().length < 2) {
    showErr('err-nombre', 'El nombre es obligatorio (mínimo 2 letras).'); if(nombre) markField(nombre,false); ok = false;
  }
  if (!apellido || apellido.value.trim().length < 2) {
    showErr('err-apellido', 'El apellido es obligatorio.'); if(apellido) markField(apellido,false); ok = false;
  }
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    showErr('err-reg-email'); if(email) markField(email,false); ok = false;
  }
  if (!pass || pass.value.length < 8) {
    showErr('err-reg-password'); if(pass) markField(pass,false); ok = false;
  }
  if (!pass2 || pass2.value !== (pass ? pass.value : '')) {
    showErr('err-reg-password2'); if(pass2) markField(pass2,false); ok = false;
  }

  if (ok) document.getElementById('form-registro').submit();
  return false;
}
</script>
</body>
</html>
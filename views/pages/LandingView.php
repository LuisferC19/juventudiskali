<?php require_once 'views/layouts/header.php'; ?>

<!-- ══════════════════════════════════════════════════════════════
     🎨 PERSONALIZACIÓN — NAVBAR
     ══════════════════════════════════════════════════════════════
     • nav-logo-ring : Reemplaza "ISK" por un <img> con el logo oficial:
         <img src="<?= BASE_URL ?>/public/img/logo-iskali.png" alt="Iskali" width="36">
     • nav-links     : Agrega o quita secciones según crezca la landing.
     • icons8-login-50.svg : Puedes sustituirlo por FontAwesome/Material Icons.
     • El href de "Iniciar sesión" NO debe cambiarse — es la ruta de auth.
     ══════════════════════════════════════════════════════════════ -->
<nav id="navbar">
  <div class="nav-brand">
    <div class="nav-logo-ring">ISK</div>
    <div>
      <div class="nav-name">Juventud <span>ISKALI</span></div>
    </div>
  </div>
  <ul class="nav-links">
    <li><a href="#inicio">Inicio</a></li>
    <li><a href="#quienes">Quiénes somos</a></li>
    <li><a href="#proyectos">Proyectos</a></li>
    <li><a href="#como-ayudar">Cómo ayudar</a></li>
    <li><a href="#redes">Redes</a></li>
  </ul>
  <div class="nav-actions">
    <a href="<?= BASE_URL ?>/index.php?pagina=login" class="nav-login btn-icon">
      <img src="<?= BASE_URL ?>/public/iconos/icons8-login-50.svg" alt="Iniciar sesión" class="icon-img">
      Iniciar sesión
    </a>
    <a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank" class="nav-cta btn-icon">
      <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="" class="icon-img">
      ¡Únete!
    </a>
  </div>
</nav>

<section class="hero" id="inicio">
  <div class="shape shape-1"></div>
  <div class="shape shape-2"></div>
  <div class="shape shape-3"></div>
  <div class="shape shape-4"></div>

  <!-- ══════════════════════════════════════════════════════════════
       🎨 PERSONALIZACIÓN — SECCIÓN HERO
       ══════════════════════════════════════════════════════════════
       • hero-title    : Cambia el slogan principal de la organización.
       • hero-sub      : Descripción breve (2-3 líneas max).
       • hero-stats    : Actualiza los números conforme crezca la org.
       • btn-hero-primary : Apunta al formulario de voluntariado en Google Forms.
       • tag-float     : Etiquetas flotantes decorativas — personaliza el texto.
       ══════════════════════════════════════════════════════════════ -->
  <div class="hero-inner">
    <div>
      <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        Colectivo juvenil · Tlaxcala & Puebla
      </div>
      <h1 class="hero-title">
        Juntos<br>
        <span class="line-teal">Transformamos</span><br>
        <span class="line-magenta">Comunidades</span>
      </h1>
      <p class="hero-sub">
        Somos un colectivo de jóvenes apasionados por el servicio, la inclusión y la protección ambiental. Desde 2021 llevamos apoyo real a quienes más lo necesitan.
      </p>
      <div class="hero-btns">
        <a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank" class="btn-hero-primary btn-icon">
          <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="" class="icon-img">
          Ser voluntario
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=login" class="btn-hero-secondary btn-icon">
          <img src="<?= BASE_URL ?>/public/iconos/icons8-login-50.svg" alt="" class="icon-img">
          Iniciar sesión
        </a>
        <a href="#proyectos" class="btn-hero-secondary btn-icon">
          <img src="<?= BASE_URL ?>/public/iconos/icons8-youtube-play-50.png" alt="" class="icon-img">
          Ver proyectos
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num">+4</div>
          <div class="stat-lbl">Años activos</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">+10</div>
          <div class="stat-lbl">Proyectos</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">2</div>
          <div class="stat-lbl">Estados</div>
        </div>
        <div class="stat-item">
          <div class="stat-num"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="Con amor" class="icon-img stat-icon"></div>
          <div class="stat-lbl">Con amor</div>
        </div>
      </div>
    </div>

    <div class="hero-mascot">
      <div class="mascot-card">
        <div class="tag-float tag-a">🌿 Sostenible</div>
        <div class="tag-float tag-b">💜 Inclusión</div>
        <div class="tag-float tag-c">⭐ Voluntario</div>
        <img src="https://i.imgur.com/placeholder.png"
          onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
          class="mascot-img" alt="Mascota Iski">
        <div class="mascot-fallback">🐨</div>
        <div class="mascot-name">-ISKI-</div>
        <div class="mascot-desc">Tu amigo koala de Fundación Iskali A.C.</div>
      </div>
    </div>
  </div>
</section>

<div class="wave-divider">
  <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="wave-svg">
    <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#F5F3FF"/>
  </svg>
</div>

<section class="quienes-bg" id="quienes">
  <div class="section-inner">
    <div class="quienes-grid">
      <div class="quienes-text">
        <div class="section-label">¿Quiénes somos?</div>
        <h2 class="section-title">Un colectivo que <span>actúa</span> con el corazón</h2>
        <p class="section-sub">
          Juventud ISKALI es un colectivo juvenil de servicio social enfocado en causas sociales y protección ambiental, operando en Tlaxcala y Puebla, México. Creemos en acompañar sin invadir, escuchar sin juzgar y actuar con responsabilidad.
        </p>
        <div class="quienes-values">
          <div class="value-card">
          <div class="value-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="Servicio" class="icon-img"></div>
            <div class="value-title">Servicio</div>
            <div class="value-desc">Acción directa para comunidades vulnerables con respeto y dignidad.</div>
          </div>
          <div class="value-card">
          <div class="value-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-leaf-50.svg" alt="Ambiente" class="icon-img"></div>
            <div class="value-title">Ambiente</div>
            <div class="value-desc">Reforestación, reciclaje y educación ambiental como ejes centrales.</div>
          </div>
          <div class="value-card">
          <div class="value-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-casa-50.png" alt="Inclusión" class="icon-img"></div>
            <div class="value-title">Inclusión</div>
            <div class="value-desc">Talleres de LSM, braille y apoyo a grupos vulnerados.</div>
          </div>
          <div class="value-card">
          <div class="value-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="Liderazgo" class="icon-img"></div>
            <div class="value-title">Liderazgo</div>
            <div class="value-desc">Formamos líderes juveniles conscientes y comprometidos.</div>
          </div>
        </div>
      </div>

      <div>
        <div class="section-label section-label-large">Nuestra historia</div>
        <div class="timeline-strip">
          <div class="tl-item">
            <div class="tl-year">2021</div>
            <div class="tl-dot"></div>
            <div class="tl-desc">Nace Juventud Iskali</div>
          </div>
          <div class="tl-item">
            <div class="tl-year">2022</div>
            <div class="tl-dot"></div>
            <div class="tl-desc">Donaciones & bazares</div>
          </div>
          <div class="tl-item">
            <div class="tl-year">2023</div>
            <div class="tl-dot"></div>
            <div class="tl-desc">Reforestaciones & Red Club</div>
          </div>
          <div class="tl-item">
            <div class="tl-year">2024</div>
            <div class="tl-dot"></div>
            <div class="tl-desc">Fundación Iskali A.C.</div>
          </div>
        </div>
        <div class="section-block">
          <div class="section-label">Redes sociales</div>
          <p class="section-sub section-meta">
            📘 <strong>Facebook:</strong> Juventud Iskali · 1.1K seguidores<br>
            📸 <strong>Instagram:</strong> @juventud_iskali · 953 seguidores<br>
            🎵 <strong>TikTok:</strong> Juventud.iskali
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="proyectos-bg" id="proyectos">
  <div class="section-inner section-center">
    <div class="section-copy">
      <div class="section-label">Proyectos & Actividades</div>
      <h2 class="section-title">Lo que hacemos <span>cada día</span></h2>
      <p class="section-sub">Desde conciertos inclusivos hasta reforestaciones — aquí hay algo para cada voluntario.</p>
    </div>

    <div class="proj-grid">
      <div class="proj-card c-teal">
        <div class="proj-cat">Apoyo social</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="Apoyo social" class="icon-img"></div>
        <div class="proj-name">Café para Todos</div>
        <div class="proj-desc">Donaciones de alimentos, despensas, kits escolares, bucales y de mujeres para comunidades vulnerables.</div>
        <div class="proj-tags">
          <span class="proj-tag">Alimentos</span>
          <span class="proj-tag">Kits</span>
          <span class="proj-tag">Comunidad</span>
        </div>
      </div>

      <div class="proj-card c-magenta">
        <div class="proj-cat">Inclusión</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-youtube-play-50.png" alt="Inclusión" class="icon-img"></div>
        <div class="proj-name">Concierto Inclusivo</div>
        <div class="proj-desc">Arte y música como herramientas de integración. Talleres de braille, Lengua de Señas Mexicana y expresión juvenil.</div>
        <div class="proj-tags">
          <span class="proj-tag">LSM</span>
          <span class="proj-tag">Braille</span>
          <span class="proj-tag">Arte</span>
        </div>
      </div>

      <div class="proj-card c-lila">
        <div class="proj-cat">Medio Ambiente</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-leaf-50.svg" alt="Medio Ambiente" class="icon-img"></div>
        <div class="proj-name">Sueños Sostenibles</div>
        <div class="proj-desc">Reforestaciones, limpieza de calles, talleres de huertos, reciclaje, ferias ecológicas y murales conscientes.</div>
        <div class="proj-tags">
          <span class="proj-tag">Reforestación</span>
          <span class="proj-tag">Reciclaje</span>
          <span class="proj-tag">Tapitas</span>
        </div>
      </div>

      <div class="proj-card c-teal">
        <div class="proj-cat">Donación responsable</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="Re-Vístete" class="icon-img"></div>
        <div class="proj-name">Re-Vístete</div>
        <div class="proj-desc">Manos Amigas, Re-Vístete y donación a centros de rehabilitación. Ropa, libros y mucho más para quienes más lo necesitan.</div>
        <div class="proj-tags">
          <span class="proj-tag">Ropa</span>
          <span class="proj-tag">Libros</span>
          <span class="proj-tag">Rehab</span>
        </div>
      </div>

      <div class="proj-card c-magenta">
        <div class="proj-cat">Acompañamiento</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-casa-50.png" alt="Acompañar" class="icon-img"></div>
        <div class="proj-name">Acompañar sin Juzgar</div>
        <div class="proj-desc">Visitas a hospitales, adultos mayores y centros de rehabilitación. Escucha activa, lenguaje digno y respeto a procesos ajenos.</div>
        <div class="proj-tags">
          <span class="proj-tag">Hospital</span>
          <span class="proj-tag">Adultos mayores</span>
          <span class="proj-tag">Salud</span>
        </div>
      </div>

      <div class="proj-card c-lila">
        <div class="proj-cat">Salud mental & Liderazgo</div>
        <div class="proj-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="Salud mental" class="icon-img"></div>
        <div class="proj-name">Hablemos de Salud Mental</div>
        <div class="proj-desc">Talleres de autoestima, introspección, autocuidado del voluntario y formación de liderazgo juvenil consciente.</div>
        <div class="proj-tags">
          <span class="proj-tag">Autoestima</span>
          <span class="proj-tag">Liderazgo</span>
          <span class="proj-tag">Autocuidado</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="como-ayudar">
  <div class="section-inner section-center">
    <div class="section-copy">
      <div class="section-label">¿Cómo ayudar?</div>
      <h2 class="section-title">Hay un lugar <span>para ti</span></h2>
      <p class="section-sub">No importa cuánto tiempo tengas — siempre hay una forma de sumar.</p>
    </div>
    <div class="como-grid">
      <div class="como-card">
        <div class="como-num n1">1</div>
        <div class="como-icon">🙌</div>
        <div class="como-title">Voluntariado</div>
        <div class="como-desc">Únete al equipo y aporta tus manos y conocimiento para llevar a cabo actividades de servicio comunitario.</div>
        <a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank" class="como-btn bt1">Quiero ser voluntario</a>
      </div>
      <div class="como-card">
        <div class="como-num n2">2</div>
        <div class="como-icon">🏪</div>
        <div class="como-title">Colaborador</div>
        <div class="como-desc">Si tienes un negocio o poco tiempo, sé nuestro centro de acopio o intermediario en tu comunidad.</div>
        <a href="mailto:contacto@iskali.org" class="como-btn bt2">Ser colaborador</a>
      </div>
      <div class="como-card">
        <div class="como-num n3">3</div>
        <div class="como-icon">💖</div>
        <div class="como-title">Patrocinador</div>
        <div class="como-desc">Colabora con una cuota monetaria o en especie destinada a casos específicos y campañas activas.</div>
        <a href="mailto:contacto@iskali.org" class="como-btn bt3">Patrocinar</a>
      </div>
    </div>
  </div>
</section>

<section class="colab-bg">
  <div class="section-inner section-center">
    <div class="section-label">Colaboradores</div>
    <h2 class="section-title">Quienes <span>confían</span> en nosotros</h2>
    <div class="colab-logos">
      <div class="colab-chip"><div class="dot"></div>de Regil</div>
      <div class="colab-chip"><div class="dot"></div>Creativa Colectiva</div>
      <div class="colab-chip"><div class="dot"></div>Alebrijes — El derecho a ser diferentes</div>
      <div class="colab-chip"><div class="dot"></div>DIVM Educación Especial</div>
      <div class="colab-chip"><div class="dot"></div>Brilliant English</div>
      <div class="colab-chip"><div class="dot"></div>Movimiento Juventud 2000</div>
      <div class="colab-chip"><div class="dot"></div>Pizzas Nico</div>
      <div class="colab-chip"><div class="dot"></div>Fit Body</div>
      <div class="colab-chip"><div class="dot"></div>Venta de Productos D</div>
      <div class="colab-chip"><div class="dot"></div>Papelería Nery Edgar</div>
    </div>
  </div>
</section>

<section class="redes-bg" id="redes">
  <div class="section-inner redes-inner">
    <div class="section-label section-label-soft">Síguenos</div>
    <h2 class="redes-title">Encuéntranos en <br>Redes Sociales</h2>
    <p class="redes-sub">Comparte, inspira y suma más manos al cambio.</p>
    <div class="redes-cards">
      <a href="https://facebook.com/JuventudIskali" target="_blank" class="red-card">
        <div class="red-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-facebook-50.png" alt="Facebook" class="icon-img"></div>
        <div class="red-name">Facebook</div>
        <div class="red-handle">Juventud Iskali</div>
        <div class="red-followers">1,100 seguidores</div>
      </a>
      <a href="https://instagram.com/juventud_iskali" target="_blank" class="red-card">
        <div class="red-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-instagram-50.png" alt="Instagram" class="icon-img"></div>
        <div class="red-name">Instagram</div>
        <div class="red-handle">@juventud_iskali</div>
        <div class="red-followers">953 seguidores</div>
      </a>
      <a href="https://tiktok.com/@Juventud.iskali" target="_blank" class="red-card">
        <div class="red-icon"><img src="<?= BASE_URL ?>/public/iconos/icons8-tiktok-50.svg" alt="TikTok" class="icon-img"></div>
        <div class="red-name">TikTok</div>
        <div class="red-handle">Juventud.iskali</div>
        <div class="red-followers">¡Síguenos!</div>
      </a>
    </div>

    <div class="qr-section">
      <div class="qr-box">
        <div class="qr-label">📲 Formulario de Voluntario</div>
        <div class="qr-img">📋</div>
        <div class="qr-desc">Escanea para<br>unirte al equipo</div>
      </div>
      <div class="qr-copy">
        <p class="qr-title">¿Listo para<br>ser parte del cambio?</p>
        <p class="qr-text">Escanea el código QR o llena el formulario de voluntario. ¡Te esperamos!</p>
        <a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank" class="qr-btn btn-icon">
          <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="" class="icon-img"> Llenar formulario
        </a>
      </div>
    </div>
  </div>
</section>

<section class="cta-section" id="voluntario">
  <div class="section-inner">
    <div class="cta-pill">✨ ¡Únete hoy!</div>
    <h2 class="cta-title">Tu tiempo puede <span class="c-teal">cambiar vidas</span></h2>
    <p class="cta-sub">Cada acción cuenta. Cada voluntario suma. Juntos creamos un impacto real en las comunidades de Tlaxcala y Puebla.</p>
    <a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank" class="cta-btn-main btn-icon">
      <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="" class="icon-img">
      ¡Quiero ser voluntario!
    </a>
    <div class="trust-row">
      <div class="trust-item"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="" class="icon-img"> Sin experiencia previa necesaria</div>
      <div class="trust-item"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="" class="icon-img"> Tlaxcala & Puebla</div>
      <div class="trust-item"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="" class="icon-img"> Comunidad increíble</div>
      <div class="trust-item"><img src="<?= BASE_URL ?>/public/iconos/icons8-marca-de-verificación-50.png" alt="" class="icon-img"> Formación en liderazgo</div>
    </div>
  </div>
</section>

<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="footer-logo">Juventud <span>ISKALI</span></div>
      <div class="footer-meta">Fundación Iskali A.C.</div>
      <p class="footer-tagline">Colectivo juvenil de servicio social enfocado en causas comunitarias y protección ambiental. Tlaxcala & Puebla, México.</p>
      <p class="footer-contact">📞 248 116 6778</p>
    </div>
    <div class="footer-col">
      <h4>Navegación</h4>
      <ul>
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#quienes">Quiénes somos</a></li>
        <li><a href="#proyectos">Proyectos</a></li>
        <li><a href="#como-ayudar">Cómo ayudar</a></li>
        <li><a href="#redes">Redes Sociales</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Únete</h4>
      <ul>
        <li><a href="https://forms.gle/uejAezgSyg4QLCCr8" target="_blank">Ser voluntario</a></li>
        <li><a href="mailto:contacto@iskali.org">Ser colaborador</a></li>
        <li><a href="mailto:contacto@iskali.org">Patrocinar</a></li>
        <li><a href="https://facebook.com/JuventudIskali" target="_blank">Facebook</a></li>
        <li><a href="https://instagram.com/juventud_iskali" target="_blank">Instagram</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 Fundación Iskali A.C. — Todos los derechos reservados.</p>
    <p class="footer-hearts">Hecho con <span>❤️</span> por y para la comunidad</p>
  </div>
</footer>

<script>
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  if (!navbar) return;
  if (window.scrollY > 40) navbar.classList.add('scrolled');
  else navbar.classList.remove('scrolled');
});

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.proj-card, .como-card, .value-card, .colab-chip').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(24px)';
  el.style.transition = 'opacity .5s ease, transform .5s ease';
  observer.observe(el);
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>

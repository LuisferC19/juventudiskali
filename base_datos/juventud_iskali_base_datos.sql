

DROP DATABASE IF EXISTS iskali;
CREATE DATABASE iskali CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE iskali;

-- ============================================================
--  CATÁLOGOS BASE
-- ============================================================

CREATE TABLE roles (
    id_rol      INT         NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(60) NOT NULL,
    descripcion TEXT,
    activo      BOOLEAN     NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_rol),
    UNIQUE KEY uq_rol_nombre (nombre)
);

CREATE TABLE comunidades (
    id_comunidad INT          NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(150) NOT NULL,
    municipio    VARCHAR(100) NOT NULL,
    estado       VARCHAR(100) NOT NULL DEFAULT 'Puebla',
    PRIMARY KEY (id_comunidad),
    UNIQUE KEY uq_comunidad (nombre, municipio)
);

CREATE TABLE tipos_apoyo (
    id_tipo_apoyo INT          NOT NULL AUTO_INCREMENT,
    nombre        VARCHAR(100) NOT NULL,
    descripcion   TEXT,
    activo        BOOLEAN      NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_tipo_apoyo),
    UNIQUE KEY uq_tipo_apoyo (nombre)
);

CREATE TABLE niveles_gamificacion (
    id_nivel       INT          NOT NULL AUTO_INCREMENT,
    nombre         VARCHAR(80)  NOT NULL,
    puntos_minimos INT          NOT NULL DEFAULT 0,
    puntos_maximos INT          NOT NULL,
    descripcion    TEXT,
    imagen_url     VARCHAR(255),
    PRIMARY KEY (id_nivel),
    CONSTRAINT chk_nivel_rango CHECK (puntos_maximos > puntos_minimos)
);

CREATE TABLE insignias (
    id_insignia      INT          NOT NULL AUTO_INCREMENT,
    nombre           VARCHAR(100) NOT NULL,
    descripcion      TEXT,
    puntos_otorgados INT          NOT NULL DEFAULT 0,
    imagen_url       VARCHAR(255),
    activa           BOOLEAN      NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_insignia)
);

CREATE TABLE tipos_referencia_notif (
    id_tipo_ref INT         NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(60) NOT NULL,
    descripcion VARCHAR(200),
    PRIMARY KEY (id_tipo_ref),
    UNIQUE KEY uq_ref_nombre (nombre)
);

CREATE TABLE tipos_bien (
    id_tipo_bien  INT          NOT NULL AUTO_INCREMENT,
    nombre        VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(50)  NOT NULL,
    descripcion   TEXT,
    activo        BOOLEAN      NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_tipo_bien),
    UNIQUE KEY uq_bien_nombre (nombre)
);


CREATE TABLE usuarios (
    id_usuario             INT          NOT NULL AUTO_INCREMENT,
    id_rol                 INT          NOT NULL,
    nombre                 VARCHAR(100) NOT NULL,
    apellido               VARCHAR(100) NOT NULL,
    email                  VARCHAR(150) NOT NULL,
    contrasena_hash        VARCHAR(255) NOT NULL,
    activo                 BOOLEAN      NOT NULL DEFAULT TRUE,
    intentos_fallidos      INT          NOT NULL DEFAULT 0,
    fecha_bloqueo_temporal DATETIME,
    ultimo_acceso          DATETIME,
    created_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_usuario_email (email)
);

CREATE TABLE historial_accesos (
    id_acceso   INT      NOT NULL AUTO_INCREMENT,
    id_usuario  INT      NOT NULL,
    tipo_accion ENUM('login','logout','fallo_login','recuperacion') NOT NULL,
    fecha_hora  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address  VARCHAR(45),
    user_agent  VARCHAR(255),
    exitoso     BOOLEAN  NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_acceso)
);

CREATE TABLE recuperacion_contrasena (
    id_recuperacion  INT          NOT NULL AUTO_INCREMENT,
    id_usuario       INT          NOT NULL,
    token            VARCHAR(255) NOT NULL,
    fecha_solicitud  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME     NOT NULL,
    usado            BOOLEAN      NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id_recuperacion),
    UNIQUE KEY uq_token (token)
);

CREATE TABLE donadores (
    id_donador        INT          NOT NULL AUTO_INCREMENT,
    id_nivel          INT,
    puntos_acumulados INT          NOT NULL DEFAULT 0,
    email             VARCHAR(150) NOT NULL,
    telefono          VARCHAR(20),
    activo            BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_donador),
    UNIQUE KEY uq_donador_email (email)
);

CREATE TABLE donadores_fisicos (
    id_donador       INT          NOT NULL,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    curp             VARCHAR(18),
    fecha_nacimiento DATE,
    PRIMARY KEY (id_donador),
    UNIQUE KEY uq_curp (curp)
);

CREATE TABLE donadores_morales (
    id_donador          INT          NOT NULL,
    razon_social        VARCHAR(150) NOT NULL,
    rfc                 VARCHAR(13),
    representante_legal VARCHAR(200),
    giro_comercial      VARCHAR(100),
    PRIMARY KEY (id_donador),
    UNIQUE KEY uq_rfc_donador (rfc)
);

CREATE TABLE donador_insignias (
    id_donador           INT      NOT NULL,
    id_insignia          INT      NOT NULL,
    fecha_obtencion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario_asignador INT      NOT NULL,
    PRIMARY KEY (id_donador, id_insignia)
);


CREATE TABLE campanas (
    id_campana         INT           NOT NULL AUTO_INCREMENT,
    id_usuario_creador INT           NOT NULL,
    nombre             VARCHAR(150)  NOT NULL,
    descripcion        TEXT,
    tipo_meta          ENUM('economica','especie','mixta','servicio') NOT NULL,
    meta_economica     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fecha_inicio       DATE          NOT NULL,
    fecha_cierre       DATE          NOT NULL,
    estado             ENUM('borrador','activa','pausada','cerrada','cancelada') NOT NULL DEFAULT 'borrador',
    imagen_url         VARCHAR(255),
    created_at         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_campana),
    CONSTRAINT chk_campana_fechas CHECK (fecha_cierre >= fecha_inicio)
);

CREATE TABLE avances_campana (
    id_avance         INT           NOT NULL AUTO_INCREMENT,
    id_campana        INT           NOT NULL,
    id_usuario        INT           NOT NULL,
    descripcion       TEXT,
    porcentaje_avance DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    monto_recaudado   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    evidencia_url     VARCHAR(255),
    fecha_registro    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_avance),
    CONSTRAINT chk_porcentaje CHECK (porcentaje_avance BETWEEN 0.00 AND 100.00)
);

CREATE TABLE reconocimientos (
    id_reconocimiento INT      NOT NULL AUTO_INCREMENT,
    id_donador        INT      NOT NULL,
    id_campana        INT,
    tipo              ENUM('diploma','carta','certificado','otro') NOT NULL,
    descripcion       TEXT,
    archivo_pdf_url   VARCHAR(255),
    fecha_emision     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario_emisor INT      NOT NULL,
    PRIMARY KEY (id_reconocimiento)
);


CREATE TABLE donaciones (
    id_donacion            INT      NOT NULL AUTO_INCREMENT,
    id_donador             INT      NOT NULL,
    id_campana             INT      NOT NULL,
    id_usuario_registrador INT      NOT NULL,
    fecha_recepcion        DATE     NOT NULL,
    estado                 ENUM('pendiente','recibida','verificada','rechazada') NOT NULL DEFAULT 'pendiente',
    evidencia_url          VARCHAR(255),
    latitud                DECIMAL(10,7),
    longitud               DECIMAL(10,7),
    created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_donacion)
);

CREATE TABLE donaciones_especie (
    id_donacion   INT           NOT NULL,
    id_tipo_bien  INT           NOT NULL,
    descripcion   TEXT,
    cantidad      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    unidad_medida VARCHAR(50)   NOT NULL,
    PRIMARY KEY (id_donacion),
    CONSTRAINT chk_esp_cantidad CHECK (cantidad > 0)
);

CREATE TABLE donaciones_economicas (
    id_donacion     INT           NOT NULL,
    monto           DECIMAL(12,2) NOT NULL,
    moneda          CHAR(3)       NOT NULL DEFAULT 'MXN',
    metodo_pago     ENUM('efectivo','transferencia','cheque','otro') NOT NULL,
    referencia_pago VARCHAR(100),
    PRIMARY KEY (id_donacion),
    CONSTRAINT chk_eco_monto CHECK (monto > 0)
);

CREATE TABLE inventario (
    id_inventario            INT           NOT NULL AUTO_INCREMENT,
    id_tipo_bien             INT           NOT NULL,
    cantidad_total_recibida  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cantidad_total_entregada DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cantidad_disponible      DECIMAL(12,2) GENERATED ALWAYS AS
                             (cantidad_total_recibida - cantidad_total_entregada) STORED,
    ultima_actualizacion     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_usuario_actualizador  INT           NOT NULL,
    PRIMARY KEY (id_inventario),
    UNIQUE KEY uq_inv_tipo (id_tipo_bien),
    CONSTRAINT chk_inv_entregada CHECK (cantidad_total_entregada <= cantidad_total_recibida)
);

CREATE TABLE movimientos_inventario (
    id_movimiento      INT           NOT NULL AUTO_INCREMENT,
    id_inventario      INT           NOT NULL,
    id_usuario         INT           NOT NULL,
    tipo_movimiento    ENUM('entrada','salida','ajuste') NOT NULL,
    cantidad           DECIMAL(12,2) NOT NULL,
    cantidad_anterior  DECIMAL(12,2) NOT NULL,
    cantidad_posterior DECIMAL(12,2) NOT NULL,
    tipo_referencia    ENUM('donacion','entrega','ajuste_manual') NOT NULL,
    id_referencia      INT,
    motivo             TEXT,
    fecha              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_movimiento)
);
LLLL
CREATE TABLE beneficiarios (
    id_beneficiario        INT          NOT NULL AUTO_INCREMENT,
    tipo_persona           ENUM('fisica','moral') NOT NULL DEFAULT 'fisica',
    id_comunidad           INT,
    direccion              VARCHAR(255),
    telefono               VARCHAR(20),
    estado                 ENUM('activo','inactivo','en_espera') NOT NULL DEFAULT 'activo',
    notas                  TEXT,
    id_usuario_registrador INT          NOT NULL,
    created_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_beneficiario)
);

CREATE TABLE beneficiarios_fisicos (
    id_beneficiario  INT          NOT NULL,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    edad             TINYINT UNSIGNED,
    curp             VARCHAR(18),
    fecha_nacimiento DATE,
    PRIMARY KEY (id_beneficiario),
    UNIQUE KEY uq_benef_curp (curp)
);

CREATE TABLE beneficiarios_morales (
    id_beneficiario INT          NOT NULL,
    razon_social    VARCHAR(150) NOT NULL,
    rfc             VARCHAR(13),
    PRIMARY KEY (id_beneficiario),
    UNIQUE KEY uq_benef_rfc (rfc)
);

CREATE TABLE beneficiario_tipos_apoyo (
    id_beneficiario  INT      NOT NULL,
    id_tipo_apoyo    INT      NOT NULL,
    fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo           BOOLEAN  NOT NULL DEFAULT TRUE,
    notas            VARCHAR(255),
    PRIMARY KEY (id_beneficiario, id_tipo_apoyo)
);

CREATE TABLE entregas (
    id_entrega             INT           NOT NULL AUTO_INCREMENT,
    id_donacion            INT           NOT NULL,
    id_beneficiario        INT           NOT NULL,
    id_usuario_responsable INT           NOT NULL,
    cantidad_entregada     DECIMAL(12,2) NOT NULL,
    fecha_entrega          DATETIME      NOT NULL,
    evidencia_url          VARCHAR(255),
    latitud                DECIMAL(10,7),
    longitud               DECIMAL(10,7),
    estado                 ENUM('programada','en_proceso','completada','cancelada') NOT NULL DEFAULT 'programada',
    observaciones          TEXT,
    PRIMARY KEY (id_entrega)
);

CREATE TABLE voluntarios (
    id_voluntario     INT          NOT NULL AUTO_INCREMENT,
    id_usuario        INT          NOT NULL,
    telefono_contacto VARCHAR(20),
    zona_asignada     VARCHAR(150),
    disponibilidad    ENUM('tiempo_completo','medio_tiempo','fines_semana','bajo_demanda') NOT NULL,
    activo            BOOLEAN      NOT NULL DEFAULT TRUE,
    fecha_ingreso     DATE         NOT NULL,
    notas             TEXT,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_voluntario),
    UNIQUE KEY uq_vol_usuario (id_usuario)
);

CREATE TABLE asignaciones_voluntario (
    id_asignacion            INT      NOT NULL AUTO_INCREMENT,
    id_voluntario            INT      NOT NULL,
    id_entrega               INT      NOT NULL,
    id_usuario_asignador     INT      NOT NULL,
    fecha_asignacion         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_compromiso         DATETIME NOT NULL,
    estado                   ENUM('asignada','aceptada','rechazada','completada','cancelada') NOT NULL DEFAULT 'asignada',
    observaciones_voluntario TEXT,
    fecha_actualizacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_asignacion)
);

CREATE TABLE sesiones_seguimiento (
    id_sesion     INT          NOT NULL AUTO_INCREMENT,
    id_entrega    INT          NOT NULL,
    id_voluntario INT          NOT NULL,
    fecha_inicio  DATETIME     NOT NULL,
    fecha_fin     DATETIME,
    estado        ENUM('en_curso','completada','cancelada') NOT NULL DEFAULT 'en_curso',
    distancia_km  DECIMAL(8,3) NOT NULL DEFAULT 0.000,
    observaciones TEXT,
    PRIMARY KEY (id_sesion)
);

CREATE TABLE puntos_geolocalizacion (
    id_punto         INT           NOT NULL AUTO_INCREMENT,
    id_sesion        INT           NOT NULL,
    latitud          DECIMAL(10,7) NOT NULL,
    longitud         DECIMAL(10,7) NOT NULL,
    precision_metros DECIMAL(6,2),
    tipo_punto       ENUM('inicio','intermedio','fin','evidencia') NOT NULL,
    evidencia_url    VARCHAR(255),
    descripcion      VARCHAR(255),
    timestamp        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_punto)
);

CREATE TABLE quejas_sugerencias (
    id_queja             INT          NOT NULL AUTO_INCREMENT,
    tipo                 ENUM('queja','sugerencia','denuncia','felicitacion') NOT NULL,
    id_donador_remitente INT,
    id_benef_remitente   INT,
    id_vol_remitente     INT,
    anonimo              BOOLEAN      NOT NULL DEFAULT FALSE,
    id_campana           INT,
    asunto               VARCHAR(200) NOT NULL,
    mensaje              TEXT         NOT NULL,
    estado               ENUM('nueva','en_revision','resuelta','cerrada') NOT NULL DEFAULT 'nueva',
    respuesta            TEXT,
    id_usuario_atiende   INT,
    fecha_registro       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion     DATETIME,
    PRIMARY KEY (id_queja),
    CONSTRAINT chk_un_remitente CHECK (
        (CASE WHEN id_donador_remitente IS NOT NULL THEN 1 ELSE 0 END +
         CASE WHEN id_benef_remitente   IS NOT NULL THEN 1 ELSE 0 END +
         CASE WHEN id_vol_remitente     IS NOT NULL THEN 1 ELSE 0 END) <= 1
    )
);

CREATE TABLE notificaciones (
    id_notificacion    INT          NOT NULL AUTO_INCREMENT,
    id_usuario         INT          NOT NULL,
    destinatario_email VARCHAR(150),
    tipo               ENUM('sistema','campana','donacion','entrega','insignia','alerta') NOT NULL,
    asunto             VARCHAR(200) NOT NULL,
    mensaje            TEXT         NOT NULL,
    enviado_email      BOOLEAN      NOT NULL DEFAULT FALSE,
    leida              BOOLEAN      NOT NULL DEFAULT FALSE,
    id_referencia      INT,
    id_tipo_referencia INT,
    fecha_envio        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_notificacion)
);

-- ============================================================
--  FOREIGN KEYS
-- ============================================================

ALTER TABLE usuarios
    ADD CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol);

ALTER TABLE historial_accesos
    ADD CONSTRAINT fk_historial_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE recuperacion_contrasena
    ADD CONSTRAINT fk_recuperacion_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE donadores
    ADD CONSTRAINT fk_donadores_nivel
        FOREIGN KEY (id_nivel) REFERENCES niveles_gamificacion(id_nivel);

ALTER TABLE donadores_fisicos
    ADD CONSTRAINT fk_df_donador
        FOREIGN KEY (id_donador) REFERENCES donadores(id_donador) ON DELETE CASCADE;

ALTER TABLE donadores_morales
    ADD CONSTRAINT fk_dm_donador
        FOREIGN KEY (id_donador) REFERENCES donadores(id_donador) ON DELETE CASCADE;

ALTER TABLE donador_insignias
    ADD CONSTRAINT fk_di_donador
        FOREIGN KEY (id_donador) REFERENCES donadores(id_donador),
    ADD CONSTRAINT fk_di_insignia
        FOREIGN KEY (id_insignia) REFERENCES insignias(id_insignia),
    ADD CONSTRAINT fk_di_asignador
        FOREIGN KEY (id_usuario_asignador) REFERENCES usuarios(id_usuario);

ALTER TABLE campanas
    ADD CONSTRAINT fk_campanas_usuario
        FOREIGN KEY (id_usuario_creador) REFERENCES usuarios(id_usuario);

ALTER TABLE avances_campana
    ADD CONSTRAINT fk_avances_campana
        FOREIGN KEY (id_campana) REFERENCES campanas(id_campana),
    ADD CONSTRAINT fk_avances_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE reconocimientos
    ADD CONSTRAINT fk_reconoc_donador
        FOREIGN KEY (id_donador) REFERENCES donadores(id_donador),
    ADD CONSTRAINT fk_reconoc_campana
        FOREIGN KEY (id_campana) REFERENCES campanas(id_campana),
    ADD CONSTRAINT fk_reconoc_emisor
        FOREIGN KEY (id_usuario_emisor) REFERENCES usuarios(id_usuario);

ALTER TABLE donaciones
    ADD CONSTRAINT fk_don_donador
        FOREIGN KEY (id_donador) REFERENCES donadores(id_donador),
    ADD CONSTRAINT fk_don_campana
        FOREIGN KEY (id_campana) REFERENCES campanas(id_campana),
    ADD CONSTRAINT fk_don_registra
        FOREIGN KEY (id_usuario_registrador) REFERENCES usuarios(id_usuario);

ALTER TABLE donaciones_especie
    ADD CONSTRAINT fk_esp_donacion
        FOREIGN KEY (id_donacion) REFERENCES donaciones(id_donacion) ON DELETE CASCADE,
    ADD CONSTRAINT fk_esp_tipo_bien
        FOREIGN KEY (id_tipo_bien) REFERENCES tipos_bien(id_tipo_bien);

ALTER TABLE donaciones_economicas
    ADD CONSTRAINT fk_eco_donacion
        FOREIGN KEY (id_donacion) REFERENCES donaciones(id_donacion) ON DELETE CASCADE;

ALTER TABLE inventario
    ADD CONSTRAINT fk_inv_tipo
        FOREIGN KEY (id_tipo_bien) REFERENCES tipos_bien(id_tipo_bien),
    ADD CONSTRAINT fk_inv_usuario
        FOREIGN KEY (id_usuario_actualizador) REFERENCES usuarios(id_usuario);

ALTER TABLE movimientos_inventario
    ADD CONSTRAINT fk_mov_inventario
        FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario),
    ADD CONSTRAINT fk_mov_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

-- BENEFICIARIOS (sub-tablas con CASCADE igual que donadores)
ALTER TABLE beneficiarios
    ADD CONSTRAINT fk_benef_usuario
        FOREIGN KEY (id_usuario_registrador) REFERENCES usuarios(id_usuario),
    ADD CONSTRAINT fk_benef_comunidad
        FOREIGN KEY (id_comunidad) REFERENCES comunidades(id_comunidad);

ALTER TABLE beneficiarios_fisicos
    ADD CONSTRAINT fk_bf_beneficiario
        FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE;

ALTER TABLE beneficiarios_morales
    ADD CONSTRAINT fk_bm_beneficiario
        FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE;

ALTER TABLE beneficiario_tipos_apoyo
    ADD CONSTRAINT fk_bta_beneficiario
        FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario),
    ADD CONSTRAINT fk_bta_tipo_apoyo
        FOREIGN KEY (id_tipo_apoyo) REFERENCES tipos_apoyo(id_tipo_apoyo);

ALTER TABLE entregas
    ADD CONSTRAINT fk_entrega_donacion
        FOREIGN KEY (id_donacion) REFERENCES donaciones(id_donacion),
    ADD CONSTRAINT fk_entrega_benefic
        FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario),
    ADD CONSTRAINT fk_entrega_responsable
        FOREIGN KEY (id_usuario_responsable) REFERENCES usuarios(id_usuario);

ALTER TABLE voluntarios
    ADD CONSTRAINT fk_voluntarios_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE asignaciones_voluntario
    ADD CONSTRAINT fk_asig_voluntario
        FOREIGN KEY (id_voluntario) REFERENCES voluntarios(id_voluntario),
    ADD CONSTRAINT fk_asig_entrega
        FOREIGN KEY (id_entrega) REFERENCES entregas(id_entrega),
    ADD CONSTRAINT fk_asig_usuario
        FOREIGN KEY (id_usuario_asignador) REFERENCES usuarios(id_usuario);

ALTER TABLE sesiones_seguimiento
    ADD CONSTRAINT fk_sesion_entrega
        FOREIGN KEY (id_entrega) REFERENCES entregas(id_entrega),
    ADD CONSTRAINT fk_sesion_voluntario
        FOREIGN KEY (id_voluntario) REFERENCES voluntarios(id_voluntario);

ALTER TABLE puntos_geolocalizacion
    ADD CONSTRAINT fk_punto_sesion
        FOREIGN KEY (id_sesion) REFERENCES sesiones_seguimiento(id_sesion);

ALTER TABLE quejas_sugerencias
    ADD CONSTRAINT fk_queja_campana
        FOREIGN KEY (id_campana) REFERENCES campanas(id_campana),
    ADD CONSTRAINT fk_queja_usuario
        FOREIGN KEY (id_usuario_atiende) REFERENCES usuarios(id_usuario),
    ADD CONSTRAINT fk_queja_donador
        FOREIGN KEY (id_donador_remitente) REFERENCES donadores(id_donador),
    ADD CONSTRAINT fk_queja_benefic
        FOREIGN KEY (id_benef_remitente) REFERENCES beneficiarios(id_beneficiario),
    ADD CONSTRAINT fk_queja_voluntario
        FOREIGN KEY (id_vol_remitente) REFERENCES voluntarios(id_voluntario);

ALTER TABLE notificaciones
    ADD CONSTRAINT fk_notif_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    ADD CONSTRAINT fk_notif_tipo_ref
        FOREIGN KEY (id_tipo_referencia) REFERENCES tipos_referencia_notif(id_tipo_ref);

-- ============================================================
--  ÍNDICES
-- ============================================================

CREATE INDEX idx_campanas_estado        ON campanas(estado);
CREATE INDEX idx_donaciones_estado      ON donaciones(estado);
CREATE INDEX idx_donaciones_fecha       ON donaciones(fecha_recepcion);
CREATE INDEX idx_donaciones_donador     ON donaciones(id_donador);
CREATE INDEX idx_donaciones_campana     ON donaciones(id_campana);
CREATE INDEX idx_entregas_estado        ON entregas(estado);
CREATE INDEX idx_entregas_fecha         ON entregas(fecha_entrega);
CREATE INDEX idx_notif_leida            ON notificaciones(id_usuario, leida);
CREATE INDEX idx_historial_usuario      ON historial_accesos(id_usuario, fecha_hora);
CREATE INDEX idx_benef_estado           ON beneficiarios(estado);
CREATE INDEX idx_benef_tipo             ON beneficiarios(tipo_persona);
CREATE INDEX idx_benef_fisicos_nombre   ON beneficiarios_fisicos(nombre, apellido);
CREATE INDEX idx_benef_morales_razon    ON beneficiarios_morales(razon_social);
CREATE INDEX idx_asig_voluntario        ON asignaciones_voluntario(id_voluntario, estado);

-- ============================================================
--  VISTAS ACTUALIZADAS
-- ============================================================

CREATE OR REPLACE VIEW v_donadores AS
SELECT
    d.id_donador,
    CASE
        WHEN df.id_donador IS NOT NULL THEN 'fisica'
        WHEN dm.id_donador IS NOT NULL THEN 'moral'
    END AS tipo_persona,
    COALESCE(dm.razon_social, CONCAT(df.nombre, ' ', df.apellido)) AS nombre_completo,
    df.curp,
    dm.rfc,
    dm.representante_legal,
    d.email,
    d.telefono,
    d.puntos_acumulados,
    ng.nombre AS nivel,
    d.activo,
    d.created_at
FROM donadores d
LEFT JOIN donadores_fisicos     df ON d.id_donador = df.id_donador
LEFT JOIN donadores_morales     dm ON d.id_donador = dm.id_donador
LEFT JOIN niveles_gamificacion  ng ON d.id_nivel   = ng.id_nivel;

CREATE OR REPLACE VIEW v_beneficiarios AS
SELECT
    b.id_beneficiario,
    b.tipo_persona,
    CASE
        WHEN bf.id_beneficiario IS NOT NULL THEN CONCAT(bf.nombre, ' ', bf.apellido)
        WHEN bm.id_beneficiario IS NOT NULL THEN bm.razon_social
        ELSE 'Sin datos'
    END AS nombre_completo,
    bf.edad,
    bf.curp,
    bm.rfc,
    com.nombre    AS comunidad,
    com.municipio,
    b.direccion,
    b.telefono,
    b.estado,
    b.notas,
    GROUP_CONCAT(ta.nombre ORDER BY ta.nombre SEPARATOR ', ') AS tipos_apoyo,
    CONCAT(u.nombre, ' ', u.apellido) AS registrado_por,
    b.created_at,
    b.updated_at
FROM beneficiarios b
LEFT JOIN beneficiarios_fisicos    bf  ON b.id_beneficiario = bf.id_beneficiario
LEFT JOIN beneficiarios_morales    bm  ON b.id_beneficiario = bm.id_beneficiario
LEFT JOIN comunidades              com ON b.id_comunidad    = com.id_comunidad
LEFT JOIN beneficiario_tipos_apoyo bta ON b.id_beneficiario = bta.id_beneficiario
LEFT JOIN tipos_apoyo              ta  ON bta.id_tipo_apoyo = ta.id_tipo_apoyo
LEFT JOIN usuarios                 u   ON b.id_usuario_registrador = u.id_usuario
GROUP BY b.id_beneficiario, b.tipo_persona, bf.id_beneficiario, bm.id_beneficiario,
         bf.edad, bf.curp, bm.rfc, com.nombre, com.municipio,
         b.direccion, b.telefono, b.estado, b.notas,
         u.nombre, u.apellido, b.created_at, b.updated_at;

CREATE OR REPLACE VIEW v_donaciones AS
SELECT
    don.id_donacion,
    don.fecha_recepcion,
    don.estado,
    CASE
        WHEN de.id_donacion   IS NOT NULL THEN 'especie'
        WHEN deco.id_donacion IS NOT NULL THEN 'economica'
    END AS tipo_donacion,
    de.descripcion,
    de.cantidad,
    de.unidad_medida,
    tb.nombre AS tipo_bien,
    deco.monto,
    deco.moneda,
    deco.metodo_pago,
    deco.referencia_pago,
    COALESCE(dm.razon_social, CONCAT(df.nombre, ' ', df.apellido)) AS donador,
    c.nombre AS campana
FROM donaciones don
LEFT JOIN donaciones_especie    de   ON don.id_donacion = de.id_donacion
LEFT JOIN donaciones_economicas deco ON don.id_donacion = deco.id_donacion
LEFT JOIN tipos_bien            tb   ON de.id_tipo_bien = tb.id_tipo_bien
LEFT JOIN donadores             d    ON don.id_donador  = d.id_donador
LEFT JOIN donadores_fisicos     df   ON d.id_donador    = df.id_donador
LEFT JOIN donadores_morales     dm   ON d.id_donador    = dm.id_donador
LEFT JOIN campanas              c    ON don.id_campana  = c.id_campana;

CREATE OR REPLACE VIEW v_inventario AS
SELECT
    i.id_inventario,
    tb.nombre            AS tipo_bien,
    tb.unidad_medida,
    i.cantidad_total_recibida,
    i.cantidad_total_entregada,
    i.cantidad_disponible,
    i.ultima_actualizacion
FROM inventario i
JOIN tipos_bien tb ON i.id_tipo_bien = tb.id_tipo_bien;

-- ============================================================
--  INSERCIONES — SOLO MÓDULOS ACTIVOS
--  (catálogos, usuarios, donadores, beneficiarios)
-- ============================================================

-- ── Roles ──────────────────────────────────────────────────
INSERT INTO roles (nombre, descripcion, activo) VALUES
('Administrador', 'Acceso total al sistema',                      TRUE),
('Coordinador',   'Gestiona campañas y donaciones',               TRUE),
('Voluntario',    'Apoya en entregas y seguimiento',              TRUE),
('Auditor',       'Solo lectura para revisión contable',          TRUE),
('Donador',       'Perfil de donante con acceso limitado',        TRUE),
('Comunicación',  'Gestiona noticias y reconocimientos',          TRUE),
('Inventarista',  'Administra entradas y salidas de inventario',  TRUE),
('Beneficiario',  'Rol informativo de beneficiarios registrados', FALSE),
('Supervisor',    'Supervisa voluntarios y entregas en campo',    TRUE),
('Soporte',       'Atiende quejas y sugerencias del sistema',     TRUE),
('Logística',     'Planifica rutas y asignaciones de entregas',   TRUE),
('Analista',      'Genera reportes y estadísticas',               TRUE),
('Captador',      'Registra nuevas donaciones en campo',          TRUE),
('Legal',         'Valida contratos y documentación oficial',     TRUE),
('Externo',       'Rol temporal para colaboraciones externas',    FALSE);

INSERT INTO comunidades (nombre, municipio, estado) VALUES
('San Andrés Cholula',    'San Andrés Cholula',    'Puebla'),
('San Martín Texmelucan', 'San Martín Texmelucan', 'Puebla'),
('Teziutlán',             'Teziutlán',             'Puebla'),
('Huauchinango',          'Huauchinango',           'Puebla'),
('Tehuacán',              'Tehuacán',              'Puebla'),
('Atlixco',               'Atlixco',               'Puebla'),
('Rancho Nuevo',          'Puebla',                'Puebla'),
('Ciudad Serdán',         'Ciudad Serdán',         'Puebla'),
('Izúcar de Matamoros',   'Izúcar de Matamoros',   'Puebla'),
('Chignahuapan',          'Chignahuapan',          'Puebla'),
('Zacatlán',              'Zacatlán',              'Puebla'),
('Tepexi de Rodríguez',   'Tepexi de Rodríguez',  'Puebla'),
('Puebla Centro',         'Puebla',                'Puebla'),
('Huejotzingo',           'Huejotzingo',           'Puebla'),
('Puebla',                'Puebla',                'Puebla');

INSERT INTO tipos_apoyo (nombre, descripcion, activo) VALUES
('Material escolar', 'Útiles y material para estudio',        TRUE),
('Despensa',         'Paquetes de alimentos básicos',         TRUE),
('Medicamentos',     'Medicamentos y atención médica',        TRUE),
('Cobijas',          'Ropa de cama y abrigo',                 TRUE),
('Juguetes',         'Juguetes para menores de edad',         TRUE),
('Empleo joven',     'Capacitación y apoyo para jóvenes',     TRUE),
('Agua potable',     'Distribución de agua potable',          TRUE),
('Cobijas/ropa',     'Cobijas y ropa de abrigo combinados',   TRUE),
('Rescate animal',   'Atención veterinaria y adopción',       TRUE),
('Servicio médico',  'Consultas y atención médica gratuita',  TRUE);

INSERT INTO niveles_gamificacion (nombre, puntos_minimos, puntos_maximos, descripcion, imagen_url) VALUES
('Semilla',      0,       99,      'Primer nivel del donador',             'img/semilla.png'),
('Brote',        100,     249,     'Empieza a contribuir regularmente',    'img/brote.png'),
('Árbol Joven',  250,     499,     'Donador comprometido',                 'img/arbol_joven.png'),
('Árbol Maduro', 500,     999,     'Alto nivel de participación',          'img/arbol_maduro.png'),
('Selva',        1000,    1999,    'Donador de gran impacto',              'img/selva.png'),
('Guardián',     2000,    3499,    'Protector activo de la comunidad',     'img/guardian.png'),
('Héroe Local',  3500,    5999,    'Reconocido por la comunidad',          'img/heroe.png'),
('Leyenda',      6000,    9999,    'Impacto transformador',                'img/leyenda.png'),
('Embajador',    10000,   19999,   'Representante oficial de iskalli',     'img/embajador.png'),
('Fundador',     20000,   49999,   'Pilar del proyecto desde sus inicios', 'img/fundador.png');

INSERT INTO insignias (nombre, descripcion, puntos_otorgados, imagen_url, activa) VALUES
('Primera Donación',    'Otorgada al realizar la primera donación',    50,  'img/i_primera.png',    TRUE),
('Donador del Mes',     'Mayor donación en el mes',                    200, 'img/i_mes.png',        TRUE),
('Corazón Solidario',   'Tres donaciones consecutivas',                150, 'img/i_corazon.png',    TRUE),
('Voluntario Estrella', 'Completó 10 entregas exitosas',               300, 'img/i_estrella.png',   TRUE),
('Gran Benefactor',     'Donación mayor a $10,000 MXN',                400, 'img/i_benefactor.png', TRUE);

INSERT INTO tipos_bien (nombre, unidad_medida, descripcion, activo) VALUES
('Despensa Básica',           'paquete',  'Paquete de alimentos básicos',              TRUE),
('Ropa Usada',                'kg',       'Ropa en buen estado',                       TRUE),
('Medicamento',               'caja',     'Medicamentos no caducados',                 TRUE),
('Material Escolar',          'set',      'Útiles escolares completos',                TRUE),
('Juguetes',                  'pieza',    'Juguetes en buen estado',                   TRUE),
('Computadora',               'pieza',    'Equipo de cómputo funcional',               TRUE),
('Cobija o Colcha',           'pieza',    'Ropa de cama en buen estado',               TRUE),
('Agua Embotellada',          'garrafón', 'Agua potable para zonas de escasez',        TRUE),
('Donativo en Especie Misc.', 'pieza',    'Artículos varios en buen estado',           TRUE);

INSERT INTO tipos_referencia_notif (nombre, descripcion) VALUES
('campana',  'Referencia a la tabla campanas'),
('donacion', 'Referencia a la tabla donaciones'),
('entrega',  'Referencia a la tabla entregas'),
('insignia', 'Referencia a la tabla insignias'),
('queja',    'Referencia a la tabla quejas_sugerencias');

INSERT INTO usuarios (id_rol, nombre, apellido, email, contrasena_hash, activo, intentos_fallidos, created_at) VALUES
(1,  'Laura',    'Hernández Ruiz',    'laura.admin@iskalli.mx',    '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-01-10 08:00:00'),
(2,  'Carlos',   'Mendoza López',     'carlos.coord@iskalli.mx',   '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-01-15 09:00:00'),
(3,  'Sofía',    'Gómez Torres',      'sofia.vol@iskalli.mx',      '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-02-01 10:00:00'),
(3,  'Miguel',   'Ramírez Castillo',  'miguel.vol@iskalli.mx',     '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-02-05 10:30:00'),
(2,  'Andrea',   'Flores Jiménez',    'andrea.coord@iskalli.mx',   '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-02-10 11:00:00'),
(6,  'Roberto',  'Sánchez Vega',      'roberto.com@iskalli.mx',    '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-02-20 08:30:00'),
(7,  'Diana',    'Cruz Morales',      'diana.inv@iskalli.mx',      '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-03-01 09:15:00'),
(9,  'Héctor',   'Vargas Ríos',       'hector.sup@iskalli.mx',     '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-03-05 07:45:00'),
(10, 'Valeria',  'Luna Espinoza',     'valeria.sop@iskalli.mx',    '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-03-10 08:00:00'),
(4,  'Jorge',    'Ortiz Salinas',     'jorge.aud@iskalli.mx',      '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-03-15 09:00:00'),
(13, 'Patricia', 'Núñez Cabrera',     'patricia.cap@iskalli.mx',   '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-04-01 10:00:00'),
(11, 'Ernesto',  'Medina Villanueva', 'ernesto.log@iskalli.mx',    '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-04-05 11:00:00'),
(12, 'Isabel',   'Peña Durán',        'isabel.ana@iskalli.mx',     '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-04-10 08:30:00'),
(5,  'Martín',   'Aguilar Ochoa',     'martin.don@iskalli.mx',     '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-04-15 09:30:00'),
(15, 'Claudia',  'Reyes Montes',      'claudia.ext@iskalli.mx',    '$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem', TRUE, 0, '2024-05-01 10:00:00');

INSERT INTO donadores (id_nivel, puntos_acumulados, email, telefono, activo, created_at) VALUES
(1, 30,   'juan.garcia@email.com',     '2221100001', TRUE, '2024-01-20 10:00:00'),
(2, 180,  'maria.lopez@email.com',     '2221100002', TRUE, '2024-01-25 11:00:00'),
(4, 620,  'contacto@empresaalfa.mx',   '2221100003', TRUE, '2024-02-01 09:00:00'),
(2, 310,  'pedro.mtz@email.com',       '2221100004', TRUE, '2024-02-10 10:00:00'),
(3, 480,  'elena.rojas@email.com',     '2221100005', TRUE, '2024-02-15 12:00:00'),
(5, 1200, 'info@fundacionluz.mx',      '2221100006', TRUE, '2024-02-20 08:00:00'),
(1, 50,   'luis.torres@email.com',     '2221100007', TRUE, '2024-03-01 09:00:00'),
(3, 420,  'carmen.vaz@email.com',      '2221100008', TRUE, '2024-03-05 10:00:00'),
(2, 260,  'arturo.diaz@email.com',     '2221100009', TRUE, '2024-03-10 11:00:00'),
(4, 750,  'admin@coopunida.mx',        '2221100010', TRUE, '2024-03-15 08:00:00'),
(1, 70,   'fernanda.rios@email.com',   '2221100011', TRUE, '2024-04-01 10:00:00'),
(2, 190,  'oscar.campos@email.com',    '2221100012', TRUE, '2024-04-05 11:00:00'),
(3, 450,  'hola@gruposemilla.mx',      '2221100013', TRUE, '2024-04-10 09:00:00'),
(2, 230,  'natalia.serrano@email.com', '2221100014', TRUE, '2024-04-15 10:30:00'),
(1, 40,   'rodrigo.blanco@email.com',  '2221100015', TRUE, '2024-05-01 09:00:00');

INSERT INTO donadores_fisicos (id_donador, nombre, apellido) VALUES
(1,  'Juan',     'García Pérez'),
(2,  'María',    'López Suárez'),
(4,  'Pedro',    'Martínez Cruz'),
(5,  'Elena',    'Rojas Ángeles'),
(7,  'Luis',     'Torres Guerrero'),
(8,  'Carmen',   'Vázquez Ibáñez'),
(9,  'Arturo',   'Díaz Méndez'),
(11, 'Fernanda', 'Ríos Estrada'),
(12, 'Óscar',    'Campos Leiva'),
(14, 'Natalia',  'Serrano Bravo'),
(15, 'Rodrigo',  'Blanco Fuentes');

INSERT INTO donadores_morales (id_donador, razon_social, giro_comercial) VALUES
(3,  'Empresa Alfa',  'Empresa privada'),
(6,  'Fundación Luz', 'Fundación sin fines de lucro'),
(10, 'Coop. Unida',   'Cooperativa'),
(13, 'Grupo Semilla', 'Organización social');

INSERT INTO beneficiarios (tipo_persona, id_comunidad, direccion, telefono, estado, notas, id_usuario_registrador, created_at) VALUES
('fisica', 1,  'Calle Jacarandas 10',     '2223001001', 'activo',    'Alumna de primaria',             2, '2024-02-01 09:00:00'),
('fisica', 2,  'Av. Principal 45',        '2223001002', 'activo',    '5 integrantes',                  2, '2024-03-01 10:00:00'),
('fisica', 3,  'Barrio el Calvario 3',    '2223001003', 'activo',    'Adulto mayor sin seguro',        5, '2024-05-01 09:00:00'),
('fisica', 4,  'Colonia Centro 22',       '2223001004', 'activo',    'Pensionada con ingresos bajos',  5, '2024-09-01 10:00:00'),
('moral',  5,  'Carretera Federal km 12', '2223001005', 'activo',    '30 niños de 2 a 10 años',        2, '2024-10-01 08:00:00'),
('fisica', 6,  'Calle Magnolias 8',       '2223001006', 'activo',    'Desempleado busca capacitación', 8, '2025-01-05 09:00:00'),
('moral',  7,  'Delegación Municipal',    '2223001007', 'activo',    '120 familias sin red de agua',   5, '2024-06-15 10:00:00'),
('fisica', 8,  'Priv. Las Flores 5',      '2223001008', 'activo',    'Ingreso a secundaria',           2, '2024-02-05 09:00:00'),
('fisica', 9,  'Calle Sauce 17',          '2223001009', 'activo',    'Diabética requiere insulina',    5, '2025-02-01 10:00:00'),
('fisica', 10, 'Col. San Francisco 3',    '2223001010', 'activo',    '7 integrantes zona fría',        5, '2024-09-10 11:00:00'),
('moral',  11, 'Av. Reforma s/n',         '2223001011', 'activo',    '250 alumnos escasos recursos',   2, '2024-02-10 08:00:00'),
('fisica', 12, 'Calle Olivos 9',          '2223001012', 'en_espera', 'En proceso de verificación',     8, '2024-03-10 09:00:00'),
('moral',  13, 'Calle 5 de Mayo 88',      '2223001013', 'activo',    'Albergan 80 animales',           5, '2025-03-01 10:00:00'),
('fisica', 14, 'Barrio Tepeyac 14',       '2223001014', 'activo',    'Requiere consultas periódicas',  5, '2024-05-15 11:00:00'),
('moral',  15, 'Col. La Paz, Puebla',     '2223001015', 'activo',    '45 niños en casa hogar',         2, '2024-10-15 09:00:00');

INSERT INTO beneficiarios_fisicos (id_beneficiario, nombre, apellido, edad) VALUES
(1,  'Ana Lucía',    'Mendoza Ríos',     8),
(2,  'José',         'Torres Herrera',   45),
(3,  'Roberto',      'Jiménez Sosa',     72),
(4,  'María del Carmen', 'Vega',         60),
(6,  'José Ramón',   'Pérez Luna',       35),
(8,  'Lucía',        'Estrada Bravo',    9),
(9,  'Guadalupe',    'Morales Tapia',    78),
(10, 'Carlos',       'Reyes Castillo',   30),
(12, 'Carlos',       'Medina Olvera',    55),
(14, 'Juana',        'Huerta Nolasco',   43);

INSERT INTO beneficiarios_morales (id_beneficiario, razon_social) VALUES
(5,  'Albergue Esperanza'),
(7,  'Comunidad Rancho Nuevo'),
(11, 'Escuela Primaria Benito Juárez'),
(13, 'Refugio Canino Ángeles'),
(15, 'Niños Casa Hogar Santa Rosa');

INSERT INTO beneficiario_tipos_apoyo (id_beneficiario, id_tipo_apoyo) VALUES
(1,  1), (2,  2), (3,  3), (4,  4), (5,  5),
(6,  6), (7,  7), (8,  1), (9,  3), (10, 8),
(11, 1), (12, 2), (13, 9), (14, 10),(15, 5),
(15, 2);

-- ============================================================
--  FIN DEL SCRIPT
-- ============================================================

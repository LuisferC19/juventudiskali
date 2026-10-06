-- Migracion de permisos por rol para Juventud Iskali.
-- Ejecutar despues de crear la tabla roles.

CREATE TABLE IF NOT EXISTS permisos_rol (
    id_permiso      INT          NOT NULL AUTO_INCREMENT,
    id_rol          INT          NOT NULL,
    modulo          VARCHAR(50)  NOT NULL,
    puede_ver       BOOLEAN      NOT NULL DEFAULT FALSE,
    puede_crear     BOOLEAN      NOT NULL DEFAULT FALSE,
    puede_editar    BOOLEAN      NOT NULL DEFAULT FALSE,
    puede_eliminar  BOOLEAN      NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id_permiso),
    UNIQUE KEY uq_permisos_rol_modulo (id_rol, modulo),
    CONSTRAINT fk_permisos_rol_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permisos_rol (id_rol, modulo, puede_ver, puede_crear, puede_editar, puede_eliminar)
SELECT r.id_rol,
       m.modulo,
       CASE
           WHEN r.nombre = 'Administrador' THEN 1
           WHEN r.nombre = 'Auditor' THEN 1
           WHEN r.nombre = 'Analista' AND m.modulo IN ('dashboard', 'campanas', 'donadores', 'donaciones', 'beneficiarios', 'entregas', 'inventario', 'voluntarios', 'planning', 'reportes') THEN 1
           WHEN r.nombre = 'Coordinador' AND m.modulo IN ('dashboard', 'campanas', 'donadores', 'donaciones', 'beneficiarios', 'entregas', 'inventario', 'voluntarios', 'planning', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Voluntario' AND m.modulo IN ('dashboard', 'donaciones', 'beneficiarios', 'entregas', 'voluntarios', 'gamificacion', 'notificaciones') THEN 1
           WHEN r.nombre = 'Donador' AND m.modulo IN ('dashboard', 'donaciones', 'gamificacion', 'notificaciones') THEN 1
           WHEN r.nombre = 'Comunicación' AND m.modulo IN ('dashboard', 'campanas', 'gamificacion', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Inventarista' AND m.modulo IN ('dashboard', 'inventario', 'entregas', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Beneficiario' AND m.modulo IN ('dashboard', 'donaciones', 'gamificacion', 'notificaciones') THEN 1
           WHEN r.nombre = 'Supervisor' AND m.modulo IN ('dashboard', 'entregas', 'voluntarios', 'planning', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Soporte' AND m.modulo IN ('dashboard', 'usuarios', 'notificaciones', 'reportes') THEN 1
           WHEN r.nombre = 'Logística' AND m.modulo IN ('dashboard', 'entregas', 'inventario', 'voluntarios', 'planning', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Captador' AND m.modulo IN ('dashboard', 'campanas', 'donadores', 'donaciones', 'beneficiarios', 'entregas', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Legal' AND m.modulo IN ('dashboard', 'campanas', 'donadores', 'donaciones', 'beneficiarios', 'usuarios', 'reportes', 'notificaciones') THEN 1
           WHEN r.nombre = 'Externo' AND m.modulo IN ('dashboard', 'campanas', 'donaciones', 'beneficiarios', 'entregas', 'gamificacion', 'notificaciones') THEN 1
           ELSE 0
       END AS puede_ver,
       CASE
           WHEN r.nombre = 'Administrador' THEN 1
           WHEN r.nombre = 'Coordinador' AND m.modulo IN ('campanas', 'donadores', 'donaciones', 'beneficiarios', 'planning') THEN 1
           WHEN r.nombre = 'Voluntario' AND m.modulo IN ('entregas', 'voluntarios') THEN 1
           WHEN r.nombre = 'Comunicación' AND m.modulo IN ('campanas', 'gamificacion') THEN 1
           WHEN r.nombre = 'Inventarista' AND m.modulo = 'inventario' THEN 1
           WHEN r.nombre = 'Supervisor' AND m.modulo IN ('entregas', 'voluntarios') THEN 1
           WHEN r.nombre = 'Logística' AND m.modulo IN ('entregas', 'planning') THEN 1
           WHEN r.nombre = 'Captador' AND m.modulo IN ('donadores', 'donaciones', 'beneficiarios', 'entregas') THEN 1
           ELSE 0
       END AS puede_crear,
       CASE
           WHEN r.nombre = 'Administrador' THEN 1
           WHEN r.nombre = 'Coordinador' AND m.modulo IN ('campanas', 'donadores', 'donaciones', 'beneficiarios', 'entregas', 'inventario', 'voluntarios', 'planning') THEN 1
           WHEN r.nombre = 'Inventarista' AND m.modulo = 'inventario' THEN 1
           WHEN r.nombre = 'Supervisor' AND m.modulo IN ('entregas', 'voluntarios') THEN 1
           WHEN r.nombre = 'Logística' AND m.modulo IN ('entregas', 'planning') THEN 1
           WHEN r.nombre = 'Captador' AND m.modulo IN ('donadores', 'donaciones', 'beneficiarios') THEN 1
           ELSE 0
       END AS puede_editar,
       CASE
           WHEN r.nombre = 'Administrador' THEN 1
           WHEN r.nombre = 'Coordinador' AND m.modulo IN ('campanas', 'donadores', 'donaciones', 'beneficiarios', 'planning') THEN 1
           WHEN r.nombre = 'Inventarista' AND m.modulo = 'inventario' THEN 1
           WHEN r.nombre = 'Logística' AND m.modulo IN ('entregas', 'planning') THEN 1
           ELSE 0
       END AS puede_eliminar
FROM roles r
CROSS JOIN (
    SELECT 'dashboard' AS modulo
    UNION ALL SELECT 'campanas'
    UNION ALL SELECT 'donadores'
    UNION ALL SELECT 'donaciones'
    UNION ALL SELECT 'beneficiarios'
    UNION ALL SELECT 'entregas'
    UNION ALL SELECT 'usuarios'
    UNION ALL SELECT 'inventario'
    UNION ALL SELECT 'voluntarios'
    UNION ALL SELECT 'respaldos'
    UNION ALL SELECT 'gamificacion'
    UNION ALL SELECT 'planning'
    UNION ALL SELECT 'reportes'
    UNION ALL SELECT 'notificaciones'
) AS m
WHERE NOT EXISTS (
        SELECT 1
        FROM permisos_rol existente
        WHERE existente.id_rol = r.id_rol
            AND existente.modulo = m.modulo
);

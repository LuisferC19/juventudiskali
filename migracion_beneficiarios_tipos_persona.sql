-- Migración para implementar tipos de persona en beneficiarios
-- Fecha: May 8, 2026
-- Descripción: Agregar soporte para personas físicas y morales en beneficiarios

USE iskali;

-- 1. Agregar columna tipo_persona a beneficiarios
ALTER TABLE beneficiarios ADD COLUMN tipo_persona ENUM('fisica','moral') NOT NULL DEFAULT 'fisica' AFTER id_beneficiario;

-- 2. Crear tabla para beneficiarios físicos
CREATE TABLE beneficiarios_fisicos (
    id_beneficiario  INT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    edad             TINYINT UNSIGNED,
    curp             VARCHAR(18),
    fecha_nacimiento DATE,
    FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE
);

-- 3. Crear tabla para beneficiarios morales
CREATE TABLE beneficiarios_morales (
    id_beneficiario     INT PRIMARY KEY,
    razon_social        VARCHAR(150) NOT NULL,
    rfc                 VARCHAR(13),
    FOREIGN KEY (id_beneficiario) REFERENCES beneficiarios(id_beneficiario) ON DELETE CASCADE
);

-- 4. Migrar datos existentes a beneficiarios_fisicos
-- (Esto divide nombre_completo en nombre y apellido)
INSERT INTO beneficiarios_fisicos (id_beneficiario, nombre, apellido, edad)
SELECT
    id_beneficiario,
    SUBSTRING_INDEX(nombre_completo, ' ', 1) as nombre,
    TRIM(SUBSTRING(nombre_completo, LOCATE(' ', nombre_completo) + 1)) as apellido,
    edad
FROM beneficiarios;

-- 5. Remover columnas obsoletas de beneficiarios
ALTER TABLE beneficiarios DROP COLUMN nombre_completo, DROP COLUMN edad;

-- 6. Agregar índices para mejor rendimiento
CREATE INDEX idx_beneficiarios_tipo ON beneficiarios(tipo_persona);
CREATE INDEX idx_beneficiarios_fisicos_nombre ON beneficiarios_fisicos(nombre, apellido);
CREATE INDEX idx_beneficiarios_morales_razon ON beneficiarios_morales(razon_social);</content>
<parameter name="filePath">c:\laragon\www\juventudiskali\migracion_beneficiarios_tipos_persona.sql
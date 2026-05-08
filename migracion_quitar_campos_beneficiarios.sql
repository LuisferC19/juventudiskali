-- Migración para quitar campos innecesarios de beneficiarios_morales
-- Fecha: May 8, 2026
-- Descripción: Remover representante_legal y giro_comercial de beneficiarios_morales

USE iskali;

-- Verificar si las columnas existen antes de intentar eliminarlas
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'iskali'
    AND TABLE_NAME = 'beneficiarios_morales'
    AND COLUMN_NAME = 'representante_legal'
);

-- Eliminar columna representante_legal si existe
SET @sql = IF(@column_exists > 0,
    'ALTER TABLE beneficiarios_morales DROP COLUMN representante_legal',
    'SELECT "Columna representante_legal no existe o ya fue eliminada" as mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si la columna giro_comercial existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'iskali'
    AND TABLE_NAME = 'beneficiarios_morales'
    AND COLUMN_NAME = 'giro_comercial'
);

-- Eliminar columna giro_comercial si existe
SET @sql = IF(@column_exists > 0,
    'ALTER TABLE beneficiarios_morales DROP COLUMN giro_comercial',
    'SELECT "Columna giro_comercial no existe o ya fue eliminada" as mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
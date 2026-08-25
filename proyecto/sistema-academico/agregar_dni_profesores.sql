-- ══════════════════════════════════════════════════════════════
--  MIGRACIÓN: Agregar columna 'dni' a la tabla profesores
--  Ejecutar una sola vez en la base de datos
-- ══════════════════════════════════════════════════════════════

ALTER TABLE profesores
    ADD COLUMN IF NOT EXISTS dni VARCHAR(15) NULL DEFAULT NULL AFTER apellido;

-- ══════════════════════════════════════════════════════════════
--  MIGRACIÓN: Vincular profesores con la tabla usuarios
--  Ejecutar una sola vez en la base de datos
-- ══════════════════════════════════════════════════════════════

-- 1. Agregar columna id_usuario a la tabla profesores (si no existe)
ALTER TABLE profesores
    ADD COLUMN IF NOT EXISTS id_usuario INT NULL DEFAULT NULL AFTER id_profesor;

-- 2. Agregar clave foránea (opcional pero recomendado para integridad)
--    Omitir si ya existe o si prefieres no restringir.
ALTER TABLE profesores
    ADD CONSTRAINT fk_profesor_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

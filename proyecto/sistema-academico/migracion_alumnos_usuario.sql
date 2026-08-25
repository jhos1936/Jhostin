-- ══════════════════════════════════════════════════════════════
--  MIGRACIÓN: Vincular alumnos con la tabla usuarios
--  Ejecutar una sola vez en la base de datos
--
--  Motivo: cuando un estudiante se registraba solo (registro.php),
--  se creaba una fila en "usuarios" pero NUNCA una fila en "alumnos".
--  El panel del estudiante filtraba las notas usando el id de
--  "usuarios" como si fuera el id de "alumnos" (son secuencias
--  autoincrementales independientes), así que un estudiante nuevo
--  podía terminar viendo las notas de OTRO alumno cuyo id coincidía
--  por casualidad, en vez de ver "0 notas / 0 cursos".
--
--  Ejemplo real encontrado en este volcado: el usuario "alumno1"
--  tiene id_usuario = 3, y alumnos.id = 3 es "Pedro Huanca" (con
--  notas ya registradas). Con el código viejo, alumno1 veía las
--  notas de Pedro Huanca al iniciar sesión.
-- ══════════════════════════════════════════════════════════════

-- 1. Agregar columna id_usuario a la tabla alumnos (si no existe)
ALTER TABLE alumnos
    ADD COLUMN IF NOT EXISTS id_usuario INT NULL DEFAULT NULL AFTER id;

-- 2. Un usuario solo puede estar vinculado a un alumno
ALTER TABLE alumnos
    ADD CONSTRAINT IF NOT EXISTS uq_alumno_usuario UNIQUE (id_usuario);

-- 3. Clave foránea hacia usuarios
ALTER TABLE alumnos
    ADD CONSTRAINT IF NOT EXISTS fk_alumno_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

-- 4. IMPORTANTE: en tu volcado, `alumnos.grado` es NOT NULL (con FK a
--    grados.id_grado). Un alumno recién auto-registrado todavía no
--    tiene grado asignado (se lo pone el admin al matricularlo), así
--    que hay que permitir NULL en esa columna. La FK sigue intacta:
--    NULL simplemente significa "sin grado todavía".
ALTER TABLE alumnos
    MODIFY COLUMN grado INT(11) NULL DEFAULT NULL;

-- 5. (Opcional) Vincular retroactivamente cuentas ya existentes que
--    quedaron "huérfanas" por el bug viejo (alumnos creados con el
--    patrón correo = usuario + '@pendiente.com', típico de la lógica
--    anterior de MatriculaController). Revisa el resultado con el
--    SELECT antes de aplicar el UPDATE: es una coincidencia de texto,
--    no una relación garantizada al 100%.
-- SELECT u.id_usuario, u.usuario, a.id AS id_alumno, a.correo
-- FROM usuarios u
-- JOIN alumnos a ON a.correo = CONCAT(u.usuario, '@pendiente.com')
-- WHERE a.id_usuario IS NULL;
--
-- UPDATE alumnos a
-- JOIN usuarios u ON a.correo = CONCAT(u.usuario, '@pendiente.com')
-- SET a.id_usuario = u.id_usuario
-- WHERE a.id_usuario IS NULL;


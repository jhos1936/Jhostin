-- ══════════════════════════════════════════════════════════════
--  MIGRACIÓN: Secciones por profesor + 3 unidades de calificación
--  Ejecutar una sola vez en la base de datos
-- ══════════════════════════════════════════════════════════════

-- 1. Tabla de asignación: profesor ↔ grado/sección ↔ curso
CREATE TABLE IF NOT EXISTS secciones_profesor (
    id_asignacion  INT AUTO_INCREMENT PRIMARY KEY,
    id_profesor    INT NOT NULL,
    id_grado       INT NOT NULL,           -- grado contiene nombre_grado + seccion
    id_curso       INT NOT NULL,
    anio_escolar   YEAR NOT NULL DEFAULT (YEAR(CURDATE())),
    UNIQUE KEY uq_asignacion (id_profesor, id_grado, id_curso, anio_escolar),
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor) ON DELETE CASCADE,
    FOREIGN KEY (id_grado)    REFERENCES grados(id_grado)        ON DELETE CASCADE,
    FOREIGN KEY (id_curso)    REFERENCES cursos(id_curso)        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Agregar nota_2 y nota_3 a calificaciones (si no existen)
ALTER TABLE calificaciones
    ADD COLUMN IF NOT EXISTS nota_2 DECIMAL(4,2) DEFAULT NULL AFTER nota_1,
    ADD COLUMN IF NOT EXISTS nota_3 DECIMAL(4,2) DEFAULT NULL AFTER nota_2;

-- 3. (Opcional) Recalcular promedio existente solo con nota_1 como antes
--    El nuevo promedio = ROUND((nota_1 + nota_2 + nota_3) / notas_ingresadas)
--    Las filas antiguas quedan con nota_2 y nota_3 NULL; el promedio no cambia.

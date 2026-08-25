-- Sincroniza el campo alumnos.grado con la matrícula ya existente
-- (arregla a los alumnos que ya estaban matriculados antes de este cambio,
-- como el caso de "No asignado" pese a tener matrícula activa).
-- Ejecutar UNA sola vez en phpMyAdmin / consola MySQL.

UPDATE alumnos a
INNER JOIN matriculas m ON m.id_estudiante = a.id
INNER JOIN grados g     ON g.id_grado      = m.id_grado
SET a.grado = TRIM(CONCAT(g.nombre_grado, ' ', g.seccion));

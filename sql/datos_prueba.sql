-- Crear usuarios de prueba si no existen
INSERT IGNORE INTO usuarios (id, nombre, email, password, created_at, updated_at)
VALUES 
(2, 'Juan Pérez', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(3, 'María García', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Borrar registros existentes
DELETE FROM registros WHERE hora_entrada >= '2024-11-02' AND hora_entrada <= '2024-12-03';

-- Insertar registros para los tres usuarios
-- Usuario 1 (Admin) - Horario típico: 9:00 - 17:00 con 1 hora de almuerzo
-- Usuario 2 - Horario típico: 8:30 - 16:30 con 1 hora de almuerzo
-- Usuario 3 - Horario típico: 10:00 - 18:00 con 1 hora de almuerzo

-- Función para verificar si es fin de semana
DELIMITER //
CREATE FUNCTION IF NOT EXISTS es_fin_de_semana(fecha DATE) 
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    RETURN DAYOFWEEK(fecha) IN (1, 7); -- 1 = Domingo, 7 = Sábado
END //
DELIMITER ;

-- Insertar registros para el Usuario 1 (Admin)
INSERT INTO registros (usuario_id, hora_entrada, hora_salida, created_at, updated_at)
SELECT 
    1 as usuario_id,
    TIMESTAMP(fecha, '09:00:00') as hora_entrada,
    TIMESTAMP(fecha, '17:00:00') as hora_salida,
    NOW() as created_at,
    NOW() as updated_at
FROM (
    SELECT DATE_ADD('2024-11-02', INTERVAL n DAY) as fecha
    FROM (
        SELECT a.N + b.N * 10 + c.N * 100 as n
        FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
             (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b,
             (SELECT 0 AS N) c
    ) numbers
    WHERE DATE_ADD('2024-11-02', INTERVAL n DAY) <= '2024-12-03'
) dates
WHERE NOT es_fin_de_semana(fecha)
    AND fecha NOT IN ('2024-11-20', '2024-11-21') -- Vacaciones
    AND fecha NOT IN ('2024-11-01'); -- Festivo

-- Insertar registros para el Usuario 2
INSERT INTO registros (usuario_id, hora_entrada, hora_salida, created_at, updated_at)
SELECT 
    2 as usuario_id,
    TIMESTAMP(fecha, '08:30:00') as hora_entrada,
    TIMESTAMP(fecha, '16:30:00') as hora_salida,
    NOW() as created_at,
    NOW() as updated_at
FROM (
    SELECT DATE_ADD('2024-11-02', INTERVAL n DAY) as fecha
    FROM (
        SELECT a.N + b.N * 10 + c.N * 100 as n
        FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
             (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b,
             (SELECT 0 AS N) c
    ) numbers
    WHERE DATE_ADD('2024-11-02', INTERVAL n DAY) <= '2024-12-03'
) dates
WHERE NOT es_fin_de_semana(fecha)
    AND fecha NOT IN ('2024-11-15') -- Día de permiso
    AND fecha NOT IN ('2024-11-01'); -- Festivo

-- Insertar registros para el Usuario 3
INSERT INTO registros (usuario_id, hora_entrada, hora_salida, created_at, updated_at)
SELECT 
    3 as usuario_id,
    TIMESTAMP(fecha, '10:00:00') as hora_entrada,
    TIMESTAMP(fecha, '18:00:00') as hora_salida,
    NOW() as created_at,
    NOW() as updated_at
FROM (
    SELECT DATE_ADD('2024-11-02', INTERVAL n DAY) as fecha
    FROM (
        SELECT a.N + b.N * 10 + c.N * 100 as n
        FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
             (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b,
             (SELECT 0 AS N) c
    ) numbers
    WHERE DATE_ADD('2024-11-02', INTERVAL n DAY) <= '2024-12-03'
) dates
WHERE NOT es_fin_de_semana(fecha)
    AND fecha NOT IN ('2024-11-27', '2024-11-28', '2024-11-29') -- Vacaciones
    AND fecha NOT IN ('2024-11-01'); -- Festivo

-- Añadir algunas variaciones aleatorias en las horas
UPDATE registros 
SET 
    hora_entrada = DATE_ADD(hora_entrada, INTERVAL FLOOR(RAND() * 20) MINUTE),
    hora_salida = DATE_ADD(hora_salida, INTERVAL FLOOR(RAND() * 20) MINUTE)
WHERE hora_entrada >= '2024-11-02' AND hora_entrada <= '2024-12-03';

-- Añadir algunos días con salida temprana
UPDATE registros 
SET hora_salida = DATE_SUB(hora_salida, INTERVAL 2 HOUR)
WHERE DAYOFWEEK(hora_entrada) = 6 -- Viernes
AND hora_entrada >= '2024-11-02' AND hora_entrada <= '2024-12-03';

-- Añadir algunos días con entrada tarde
UPDATE registros 
SET hora_entrada = DATE_ADD(hora_entrada, INTERVAL 30 MINUTE)
WHERE DAYOFWEEK(hora_entrada) = 2 -- Lunes
AND hora_entrada >= '2024-11-02' AND hora_entrada <= '2024-12-03';

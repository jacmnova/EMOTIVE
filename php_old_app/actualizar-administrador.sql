-- Script SQL para actualizar el administrador
-- Email: wheelkorner@gmail.com -> jose@gafi.com.br
-- Nombre: Jose A Cordero

-- Verificar que el usuario existe
SELECT id, name, email, admin, sa 
FROM users 
WHERE email = 'wheelkorner@gmail.com';

-- Verificar que el nuevo email no existe
SELECT id, name, email 
FROM users 
WHERE email = 'jose@gafi.com.br';

-- Actualizar el usuario
UPDATE users 
SET 
    email = 'jose@gafi.com.br',
    name = 'Jose A Cordero',
    updated_at = NOW()
WHERE email = 'wheelkorner@gmail.com';

-- Verificar la actualización
SELECT id, name, email, admin, sa, updated_at 
FROM users 
WHERE email = 'jose@gafi.com.br';




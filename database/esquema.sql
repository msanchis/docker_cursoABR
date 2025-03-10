-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS sistema_usuarios;

CREATE USER 'user_ctf'@'localhost' IDENTIFIED BY 'P@ssw0rd.';

-- Conceder todos los privilegios al usuario user_ctf en la base de datos sistema_usuarios
GRANT ALL PRIVILEGES ON sistema_usuarios.* TO 'user_ctf'@'localhost';

-- Actualizar los privilegios
FLUSH PRIVILEGES;

-- Usar la base de datos
USE sistema_usuarios;


-- Eliminar la tabla si ya existe
DROP TABLE IF EXISTS usuarios;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') NOT NULL,
    idioma VARCHAR(10) DEFAULT 'es' -- Ejemplo: 'es', 'en'
);

-- Insertar un usuario administrador por defecto (para pruebas)
 INSERT INTO usuarios (username, password, rol, idioma) VALUES
('admin', '', 'admin', 'es'); -- Reemplaza '$2y$10$hashedPassword' con una contraseña hasheada

-- Ejemplo de como generar una contraseña hasheada
-- En php puedes hacerlo con:  password_hash("tu_contraseña_aquí", PASSWORD_DEFAULT);
-- La contraseña "admin" con el password_hash daría: $2y$10$qUu5j4cQJqL.m1xU226n0u26/uXp1/W94sM6u7V7j0g3/sV6qWj2

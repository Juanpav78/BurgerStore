-- Base de datos de BurgerStore
-- Ejecutar en MySQL Workbench (File > Open SQL Script... > rayo de "Execute")
-- Usuario admin de prueba: jpav@correo.com / 123456

CREATE DATABASE IF NOT EXISTS burgershop_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE burgershop_crud;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(60) NOT NULL UNIQUE,
    password CHAR(60) NOT NULL
);

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    categoria VARCHAR(45) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(200) NOT NULL,
    descripcion LONGTEXT,
    creado DATE,
    comprador_id INT
);

-- Password: 123456 (hash bcrypt)
INSERT IGNORE INTO usuarios (email, password) VALUES
('jpav@correo.com', '$2y$12$NVAxmmG3kbRPPELueMV5wumAXgrY2GdfctlrA95ChFG/YHofwGusG');

-- Las imagenes ya existen en la carpeta img/ (el nombre va sin .png)
INSERT INTO productos (nombre, categoria, precio, imagen, descripcion, creado, comprador_id) VALUES
('Clasica', 'hamburguesa', 18000, 'hamburguesa_2daadcc41d1df3bae62bc864ee72b9eb', 'Carne de res a la parrilla, queso cheddar, lechuga fresca, tomate, cebolla y nuestra salsa especial de la casa.', CURDATE(), 1),
('Doble Queso', 'hamburguesa', 24000, 'hamburguesa_33c19d0e07baf735d0cafb6f78a6893c', 'Doble carne de res, doble queso cheddar fundido, pepinillos, cebolla caramelizada y salsa de mostaza miel.', CURDATE(), 1),
('Hot Beacon', 'hamburguesa', 26000, 'hamburguesa_3a7666efd2bc60a51f1deea2334a649b', 'Carne angus, tocineta crujiente, jalapeños, queso pepper jack y salsa picante ahumada para los valientes.', CURDATE(), 1),
('BBQ Ranch', 'hamburguesa', 25000, 'hamburguesa_5f5b278001451bb78c29778eaf7d96ef', 'Carne de res, aros de cebolla apanados, queso americano, tocineta y salsa barbacoa con un toque de ranch.', CURDATE(), 1),
('Pollo Crispy', 'hamburguesa', 21000, 'hamburguesa_6b951ba815b0d260fde9f27986723c8d', 'Pechuga de pollo apanada y crocante, lechuga, tomate, queso mozzarella y mayonesa de ajo casera.', CURDATE(), 1),
('Mexicana', 'hamburguesa', 23000, 'hamburguesa_aa12e8755cdb0ab53151487363de1fc3', 'Carne de res sazonada, guacamole, pico de gallo, queso cheddar, nachos triturados y crema agria.', CURDATE(), 1),
('Champiñones', 'hamburguesa', 24500, 'hamburguesa_d2fbe1734ad5d2829e380c9387e97f79', 'Carne angus, champiñones salteados en mantequilla, queso suizo derretido, cebolla grillé y alioli.', CURDATE(), 1),
('Veggie', 'hamburguesa', 20000, 'hamburguesa_fda51a5f54f869843931dfa94d9140c6', 'Medallón de garbanzo y quinoa, aguacate, rúgula, tomate seco, queso de cabra y vinagreta balsámica.', CURDATE(), 1),
('Combo Familiar', 'combo', 55000, 'combo_d08ade2823d5dbaeded96448f736da75', 'Tres hamburguesas clásicas, papas fritas grandes, aros de cebolla y tres bebidas a elección para compartir.', CURDATE(), 1);

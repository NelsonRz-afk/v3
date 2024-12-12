CREATE DATABASE sistema_login CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE sistema_login;

CREATE TABLE categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

INSERT INTO categoria (nombre) VALUES ('básico'), ('medio'), ('avanzado');

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    categoria_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categoria(id)
);

INSERT INTO usuarios (nombre, password, categoria_id, email)
VALUES ('admin', '$2y$10$peggz5xaoCYC61R4D8wDPu5DD1U0UU3IQUSXq/WZUrCNtLsgsTvsm', 3, 'admin@aiep.com');

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE DATABASE IF NOT EXISTS GDI; 
USE GDI;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    prioridad ENUM('baja', 'media', 'alta', 'critica') DEFAULT 'media',
    estado ENUM('abierta', 'en_progreso', 'resuelta', 'cerrada') DEFAULT 'abierta',
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS  auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_id INT NOT NULL,
    accion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
        datos_anteriores JSON NULL,
        datos_nuevos JSON NULL,
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE  IF NOT EXISTS item_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_original_id INT NOT NULL,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255),
    descripcion TEXT,
    prioridad VARCHAR(50),
    estado VARCHAR(50),
    created_at DATETIME, 
    deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP 
);
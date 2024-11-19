-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS timesheet;
USE timesheet;

-- Criação da tabela "users"
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    hourly_rate DECIMAL(10,2) DEFAULT 0.00
);

-- Criação da tabela "hours"
CREATE TABLE IF NOT EXISTS hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    entry_time TIME NOT NULL,
    lunch_start TIME NOT NULL,
    lunch_end TIME NOT NULL,
    exit_time TIME NOT NULL,
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

-- Adicionando o usuário admin com is_admin ativo
INSERT INTO users (username, email, password_hash, is_admin, hourly_rate)
VALUES 
    ('Admin', 'admin@gmail.com', '$2y$10$8yugA5LS9zn5Jkxhzauy9..dKxKq7aJqXulcb..3KqinZqIz2wz5i', 1, 0.00);


-- Todas as senhas seram guardadas em Hash

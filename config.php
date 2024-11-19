<?php
// Configuração do banco de dados
$host = 'localhost';          // Nome do host
$dbname = 'timesheet';   // Nome do banco de dados
$username = 'root';           // Nome do usuário do banco de dados
$password = 'oracle';               // Senha do banco de dados (substitua se necessário)

// Criação da conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conn->connect_error);
}

// Configurar o charset para evitar problemas com caracteres especiais
$conn->set_charset("utf8");

// Função para hash seguro de senhas
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Função para verificar a senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>

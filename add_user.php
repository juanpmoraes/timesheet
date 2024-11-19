<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os campos obrigatórios foram preenchidos
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['hourly_rate'])) {
        // Recebe os dados do formulário
        $username = $_POST['username'];      // Nome de usuário
        $email = $_POST['email'];            // E-mail
        $password = $_POST['password'];      // Senha
        $hourly_rate = $_POST['hourly_rate']; // Valor por hora
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;  // Se for admin, 1; caso contrário, 0

        // Criptografa a senha
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Verifica se o usuário já existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Este e-mail já está registrado!";
        } else {
            // Prepara e executa a inserção dos dados, incluindo o valor por hora
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, is_admin, hourly_rate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $username, $email, $password_hash, $is_admin, $hourly_rate);

            if ($stmt->execute()) {
                echo "Usuário adicionado com sucesso!";
            } else {
                echo "Erro ao adicionar usuário!";
            }
        }

        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos obrigatórios!";
    }
}
?>

<!-- Formulário para adicionar usuário -->
<link rel="stylesheet" href="style.css">
<form method="POST" action="add_user.php">
    <label for="username">Nome de Usuário:</label>
    <input type="text" name="username" required><br>

    <label for="email">E-mail:</label>
    <input type="email" name="email" required><br>

    <label for="password">Senha:</label>
    <input type="password" name="password" required><br>

    <label for="hourly_rate">Valor por Hora:</label>
    <input type="number" name="hourly_rate" step="0.01" required><br>

    <label for="is_admin">Administrador:</label>
    <input type="checkbox" name="is_admin"><br>

    <input type="submit" value="Adicionar Usuário">
    <h1></h1>
    <a href="user_list.php">Lista de Usuários</a>
</form>
<br>
<!-- Botão de Logout -->
<a href="logout.php" class="btn-logout">Sair</a>

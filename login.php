<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepara a consulta para verificar o usuário no banco de dados
    $stmt = $conn->prepare("SELECT id, password_hash, is_admin, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Verifica se o usuário existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password_hash, $is_admin, $username);
        $stmt->fetch();

        // Verifica se a senha está correta
        if (password_verify($password, $password_hash)) {
            // Salva as informações na sessão
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $is_admin;

            // Redireciona conforme o tipo de usuário
            if ($is_admin == 1) {
                // Se for administrador, redireciona para o painel do administrador
                header("Location: admin_dashboard.php");
            } else {
                // Caso contrário, redireciona para o painel do usuário normal
                header("Location: user_dashboard.php");
            }
            exit;  // Garante que o código não continue após o redirecionamento
        } else {
            // Senha incorreta
            $_SESSION['error'] = "Senha incorreta!";
            header("Location: login.php");
            exit();
        }
    } else {
        // Usuário não encontrado
        $_SESSION['error'] = "Usuário não encontrado!";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}
?>
